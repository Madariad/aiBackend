<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('password');
            $table->string('role');
            $table->string('api_token')->nullable(); // Токен для API аутентификации
            $table->string('email')->unique(); // Электронная почта
            $table->timestamp('email_verified_at')->nullable(); // Дата верификации email
            $table->rememberToken(); // Токен для "запомнить меня" (используется для авторизации)
            $table->timestamps();
        });

       
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');

    }
};
