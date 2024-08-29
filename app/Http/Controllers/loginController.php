<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class loginController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);
    
        $user = User::where('name', $request->input('name'))->first();
    
        if ($user && Hash::check($request->input('password'), $user->password)) {
            // Генерация API токена
            $tokenResult = $user->createToken('token-api');
            $token = $tokenResult->plainTextToken;
    
            return response()->json([
                'message' => 'success',
                'token' => $token,
                'user' => $user,
                'role' => $user->role
            ], 200);
        } else {
            return response()->json([
                'message' => 'failed',
            ], 401);
        }
    }
    

    public function register(Request $request) {
        // Валидация входных данных
        $request->validate([
            'name' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string'
        ]);
    
        // Создание нового пользователя
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email'); // Сохранение email
        $user->password = Hash::make($request->input('password')); // Хэшируем пароль
        $user->role = $request->input('role');
        $user->save();
    
        // Генерация и сохранение API токена после сохранения пользователя
        $tokenResult = $user->createToken('token-api');
        $token = $tokenResult->plainTextToken;
    
        // Возвращаем успешный ответ
        return response()->json([
            'message' => 'success',
            'username' => $user->name,
            'token' => $token,
            'role' => $user->role,
            'email' => $user->email, 
        ], 201);
    }
    
    
    

    public function logout(Request $request) {
        // Аннулируем текущий токен пользователя
        $request->user()->currentAccessToken()->delete();
    
        // Возвращаем успешный ответ
        return response()->json(['message' => 'success'], 200);
    }

    public function getRole() {
        // Получение роли текущего пользователя
        $user = Auth::user();
        return response()->json(['role' => $user->role], 200);
    }
}
