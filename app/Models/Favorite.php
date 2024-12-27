<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    // Поля, которые можно массово заполнять
    protected $fillable = [
        'user_id',
        'advert_id',
    ];

    // Связь с пользователем
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Связь с объявлением (advert)
    public function advert()
    {
        return $this->belongsTo(Advert::class);
    }
}