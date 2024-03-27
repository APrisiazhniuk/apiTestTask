<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    
    public function usersList(Request $request)
    {
        // Валідуємо параметри запиту
        $validator = Validator::make($request->all(), [
            'page' => 'required|integer|min:1',
            'count' => 'integer|min:1|max:100',
        ]);
    
        // Перевіряємо валідацію
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'fails' => $validator->errors()], 422);
        }
    
        // Встановлюємо значення за замовчуванням для параметра count, якщо він не вказаний
        $count = $request->has('count') ? $request->count : 5;
    
        // Обчислюємо значення зміщення для запиту
        $offset = ($request->page - 1) * $count;
    
        // Отримуємо загальну кількість користувачів
        $total_users = User::count();
    
        // Отримуємо користувачів для поточної сторінки
        $users = User::orderBy('created_at', 'desc')
                     ->offset($offset)
                     ->limit($count)
                     ->get();
    
        // Перевіряємо, чи є користувачі на поточній сторінці
        if ($users->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Page not found'], 404);
        }
        // Обчислюємо кількість сторінок
        $total_pages = ceil($total_users / $count);
    
        // Генеруємо URL для наступної та попередньої сторінок
        $next_url = ($request->page < $total_pages) ? route('users.index', ['page' => $request->page + 1, 'count' => $count]) : null;
        $prev_url = ($request->page > 1) ? route('users.index', ['page' => $request->page - 1, 'count' => $count]) : null;
    
        // Формуємо відповідь
        $response = [
            'success' => true,
            'page' => $request->page,
            'total_pages' => $total_pages,
            'total_users' => $total_users,
            'count' => $count,
            'links' => [
                'next_url' => $next_url,
                'prev_url' => $prev_url,
            ],
            'users' => $users,
        ];
    
        // Повертаємо успішну відповідь
        return response()->json($response, 200);
    }
    public function getUserById(Request $request, $id)
    {
        // Валідуємо параметр id
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer',
        ]);

        // Перевіряємо валідацію
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'fails' => $validator->errors()], 400);
        }

        // Отримуємо користувача за його ідентифікатором
        $user = User::find($id);

        // Перевіряємо, чи існує користувач з таким ідентифікатором
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'The user with the requested identifier does not exist', 'fails' => ['user_id' => ['User not found']]], 404);
        }

        // Формуємо відповідь
        $response = [
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'position' => $user->position,
                'position_id' => $user->position_id,
                'photo' => $user->photo,
            ],
        ];

        // Повертаємо успішну відповідь
        return response()->json($response, 200);
    }

    public function getUserPositions()
    {
        // Отримуємо унікальні позиції користувачів з таблиці
        $positions = User::select('position_id', 'name')
                        ->groupBy('position_id', 'name')
                        ->get();

        // Перевіряємо, чи є позиції
        if ($positions->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Positions not found'], 404);
        }

        // Формуємо відповідь у вказаному форматі
        $formattedPositions = $positions->map(function ($position) {
            return [
                'id' => $position->position_id,
                'name' => $position->name,
            ];
        });

        // Формуємо відповідь
        $response = [
            'success' => true,
            'positions' => $formattedPositions,
        ];

        // Повертаємо успішну відповідь
        return response()->json($response, 200);
    }

}