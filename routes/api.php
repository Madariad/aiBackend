<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\AssistantController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/assistant', [AssistantController::class, 'generateAssistantsResponse']);

Route::post('/login', [loginController::class, 'login']); 
Route::post('/register', [loginController::class, 'register']); 
Route::post('/logout', [loginController::class, 'logout']);
Route::post('/role', [loginController::class, 'getRole']); 

Route::post('/chat/create', [ChatController::class, 'createChat'])->middleware('auth:sanctum');
Route::get('/chats/get', [ChatController::class, 'getChats'])->middleware('auth:sanctum');
Route::get('/chat/get', [ChatController::class, 'getChat'])->middleware('auth:sanctum');

