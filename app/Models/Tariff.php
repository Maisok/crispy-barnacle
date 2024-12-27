<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    protected $primaryKey = 'id_tariff'; // Указываем, что ключевое поле - id_tariff
    public $incrementing = false; // Если id_tariff не является автоинкрементным
    protected $keyType = 'int'; // Если id_tariff имеет тип int
    
    protected $fillable = [
        'id_user', 'price_day', 'price_day_one_advert', 'price_month', 'adverts', 'status'
    ];
}