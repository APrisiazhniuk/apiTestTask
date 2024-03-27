<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ImageHelper;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Перевіряємо чи переданий токен для реєстрації у заголовках запиту
        if (!$request->header('Token')) {
            return response()->json(['success' => false,'message' => 'Token is required.'], 400);
        }

         // Перевіряємо чи переданий токен є в кеші
        $token = $request->header('Token');
        if (!Cache::has('registration_token') || Cache::get('registration_token') !== $token) {
            return response()->json(['success' => false, 'message' => 'The token expired.'], 401);
        }
        
        // Валідуємо дані для реєстрації користувача
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:60',
            'email' => 'required|email:rfc|max:100',
            'phone' => 'required|string|regex:/^\+380([0-9]{9})$/',
            'position_id' => 'required|integer|min:1',
            'photo' => 'required|image|mimes:jpeg,jpg|max:5120',
        ]);

        // Перевіряємо валідацію
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'fails' => $validator->errors()], 422);
        }

        // Перевіряємо чи користувач з таким email або phone вже існує
        if (User::where('email', $request->email)->orWhere('phone', $request->phone)->exists()) {
            return response()->json(['success' => false, 'message' => 'User with this phone or email already exist'], 409);
        }

        // Створюємо нового користувача
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), 
            'phone' => $request->phone,
            'position_id' => $request->position_id,
        ]);
        
        // Image optimization
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $imagePath = ImageHelper::optimizeAndResize($photo);

            if ($imagePath) {
                $user->photo = $imagePath;
                $user->save();
            } else {
                // Handle error
                return back()->withErrors(['image' => 'Failed to optimize and resize the image.']);
            }
        }
        // Видаляємо токен, з яким зареєстрований користувач
        Cache::forget('registration_token');

        // Повертаємо успішну відповідь
        return response()->json(['success' => true, 'user_id' => $user->id, 'message' => 'New user successfully registered'], 200);
    }
    
    public function login(Request $request)
    {
        return null;
    }
}