<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTariffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id('id_tariff'); // Первичный ключ
            $table->unsignedBigInteger('id_user'); // Внешний ключ
            $table->decimal('price_day', 10, 2); // Стоимость размещения в день
            $table->decimal('price_day_one_advert', 10, 2); // Стоимость размещения в день одного объявления
            $table->decimal('price_month', 10, 2); // Стоимость размещения в месяц
            $table->integer('adverts'); // Количество допустимых объявлений данного тарифа
            $table->enum('status', ['old', 'new']); // Статус тарифа
            $table->timestamps(); // Временные метки created_at и updated_at

            // Ограничение внешнего ключа
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tariffs');
    }
}