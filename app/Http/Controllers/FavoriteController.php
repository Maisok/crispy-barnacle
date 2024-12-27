<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Advert;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // Добавить товар в избранное
    public function addToFavorites(Request $request, $advertId)
    {
        $user = Auth::user();

        // Проверяем, существует ли уже такой товар в избранном
        $favorite = Favorite::where('user_id', $user->id)
                            ->where('advert_id', $advertId)
                            ->first();

        if ($favorite) {
            return response()->json(['message' => 'Товар уже в избранном'], 409);
        }

        // Создаем запись в избранном
        Favorite::create([
            'user_id' => $user->id,
            'advert_id' => $advertId,
        ]);

        return response()->json(['message' => 'Товар добавлен в избранное'], 200);
    }

    // Удалить товар из избранного
    public function removeFromFavorites(Request $request, $advertId)
    {
        $user = Auth::user();

        // Удаляем запись из избранного
        Favorite::where('user_id', $user->id)
                ->where('advert_id', $advertId)
                ->delete();

        return response()->json(['message' => 'Товар удален из избранного'], 200);
    }
}