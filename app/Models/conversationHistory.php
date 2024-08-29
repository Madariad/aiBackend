<?php

namespace App\Models;

use App\Models\Chat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class conversationHistory extends Model
{
    use HasFactory;
    

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
