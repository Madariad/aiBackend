<?php

use Illuminate\Support\Facades\Route;
use OpenAI\Laravel\Facades\OpenAI;


Route::get('/', function () {

    // $result = OpenAI::chat()->create([
    //     'model' => 'gpt-4o',
    //     'messages' => [
    //         ['role' => 'user', 'content' => 'привет, как дела?'],
    //     ],
    // ]);

    // echo $result->choices[0]->message->content;
    
    return view('welcome');
});
