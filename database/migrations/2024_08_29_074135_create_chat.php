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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->uuid('chat_code')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->text('name');
            $table->timestamps();
        });

        // DB::table('chats')->get()->each(function ($chat) {
        //     DB::table('chats')->where('id', $chat->id)->update(['chat_code' => Str::uuid()]);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
