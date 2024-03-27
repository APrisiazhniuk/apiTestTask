<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    public function generateTokenForRegistration()
    {
        // Генеруємо унікальний токен
        $token = Str::random(60);

        // Зберігаємо токен у кеші на 40 хвилин
        Cache::put('registration_token', $token, 40);

        // Повертаємо токен
        return response()->json(['success'=> 'true','token' => $token], 200);
    }
}