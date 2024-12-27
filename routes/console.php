<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\Advert;
use App\Models\User;
use App\Models\Tariff;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

// Команда для сброса статуса объявлений
Artisan::command('adverts:reset-status', function () {
    // Получаем текущее время
    $now = Carbon::now();

    // Сбрасываем статусы объявлений, у которых прошло более 24 часов с момента последней оплаты
    Advert::where('status_ad', 'activ')
        ->where('status_pay', 'pay')
        ->where(function ($query) use ($now) {
            $query->whereNotNull('time_last_pay')
                  ->where('time_last_pay', '<', $now->subHours(24));
        })
        ->update([
            'status_ad' => 'notactiv',
            'status_pay' => 'not_pay'
        ]);

    $this->info('Adverts status reset completed');
    Log::info('Adverts status reset completed');
})->describe('Reset adverts status to notactiv and not_pay after 24 hours from last payment');

// Команда для ежеминутной оплаты объявлений
Artisan::command('payment:minute', function () {
    $now = Carbon::now();
    $adverts = Advert::whereHas('user.tariff', function ($query) {
        $query->where('status', 'active');
    })
    ->where(function ($query) use ($now) {
        $query->whereNull('time_last_pay')
              ->orWhere('time_last_pay', '<', $now->subHours(24));
    })
    ->get();

    foreach ($adverts as $advert) {
        $user = $advert->user;
        $tariff = $user->tariff;
        $paymentAmount = $tariff->price_day_one_advert;

        try {
            DB::transaction(function () use ($advert, $user, $paymentAmount) {
                if ($user->balance >= $paymentAmount) {
                    $user->balance -= $paymentAmount;
                    $user->save();
                    $advert->update([
                        'status_ad' => 'activ',
                        'status_pay' => 'pay',
                        'time_last_pay' => Carbon::now()
                    ]);
                    Log::info("Payment processed for advert ID: {$advert->id}, User ID: {$user->id}, Amount: {$paymentAmount}, Time: " . Carbon::now());
                } else {
                    Log::warning("Insufficient balance for user ID: {$user->id}, advert ID: {$advert->id}");
                }
            });
        } catch (\Exception $e) {
            Log::error("Error processing payment for advert ID: {$advert->id}, User ID: {$user->id}: " . $e->getMessage());
        }
    }
})->describe('Minute payment for adverts');


// Команда для обновления статуса объявлений
Artisan::command('adverts:update-status', function () {
    $thirtyDaysAgo = Carbon::now()->subDays(30);

    Advert::where('status_ad', 'activ')
        ->where('created_at', '<', $thirtyDaysAgo)
        ->update(['status_ad' => 'arhive']);

    $this->info('Advert statuses updated successfully.');
})->describe('Update advert status to "arhive" if they are older than 30 days');

// Планирование команд
$schedule = app(Schedule::class);
$schedule->command('adverts:reset-status')->daily();
$schedule->command('payment:minute')->everyMinute();
$schedule->command('adverts:update-status')->daily();