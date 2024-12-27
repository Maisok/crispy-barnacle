<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tariff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TariffController extends Controller
{
    
    public function showTariffSettings()
{
    $user = Auth::user();
    $hasTariff = $user->tariffs()->exists(); // Проверяем, есть ли у пользователя запись в таблице tariffs

    return view('tariff-settings', compact('hasTariff'));
}

  // Метод для создания пробного тарифа
    public function createTrialTariff(Request $request)
    {
        // Получаем текущего пользователя
        $user = Auth::user();

        // Проверяем, есть ли у пользователя уже тариф
        if ($user->tariffs()->exists()) {
            return redirect()->route('tariff.settings')->with('error', 'У вас уже есть тариф.');
        }

        // Создаем запись в таблице tariffs
        Tariff::create([
            'id_tariff' => null, // Если id_tariff генерируется автоматически, оставьте null
            'id_user' => $user->id,
            'price_day' => 0,
            'price_day_one_advert' => 0,
            'price_month' => 0,
            'adverts' => 99999,
            'status' => 'new',
        ]);

        // Перенаправляем пользователя на страницу настроек тарифа с сообщением об успехе
        return redirect()->route('tariff.settings')->with('success', 'Пробный тариф успешно активирован.');
    }
    
public function save(Request $request)
{
    // Валидация данных
    $request->validate([
        'ad-count' => 'required|integer|min:100|max:100000',
    ]);

    // Получение пользователя
    $user = auth()->user();

    // Проверка наличия тарифа со статусом 'new'
    $tariff = Tariff::where('id_user', $user->id)->where('status', 'new')->first();

    if ($tariff && !$request->has('confirm')) {
        // Если есть тариф со статусом 'new' и нет подтверждения, возвращаем сообщение с подтверждением
        return redirect()->back()->with('warning', 'У вас действует пробный период, вы уверены что хотите изменить тариф?');
    }

    // Расчет цен
    $adCount = $request->input('ad-count');
    $basePricePerDay = 0.75;
    $discountFactor = 1 - min(0.5, ($adCount - 100) / 100000);
    $dailyCost = $adCount * $basePricePerDay * $discountFactor;
    $dailyCostPerItem = $basePricePerDay * $discountFactor;
    $monthlyCost = $dailyCost * 30;

    // Определение статуса (устанавливаем принудительно 'old')
    $status = 'old';

    // Поиск тарифа для пользователя
    $tariff = Tariff::where('id_user', $user->id)->first();

    if ($tariff) {
        // Если тариф уже существует, обновляем данные
        $tariff->update([
            'price_day' => $dailyCost,
            'price_day_one_advert' => $dailyCostPerItem,
            'price_month' => $monthlyCost,
            'adverts' => $adCount,
            'status' => $status,
            'updated_at' => Carbon::now(),
        ]);
    } else {
        // Если тарифа нет, создаем новый
        $tariff = Tariff::create([
            'id_user' => $user->id,
            'price_day' => $dailyCost,
            'price_day_one_advert' => $dailyCostPerItem,
            'price_month' => $monthlyCost,
            'adverts' => $adCount,
            'status' => $status,
        ]);
    }

    return redirect()->back()->with('success', 'Тариф успешно сохранен!');
}
}