<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\DB;
class AssistantController extends Controller
{
    private function submitMessage($assistantId, $threadId, $userMessage)
    {
        $message = OpenAI::threads()->messages()->create($threadId, [
            'role' => 'user',
            'content' => $userMessage,
        ]);

        $run = OpenAI::threads()->runs()->create(
            threadId: $threadId,
            parameters: [
                'assistant_id' => $assistantId,
            ],
        );

        return [
            $message,
            $run
        ];
    }

    private function createThreadAndRun($assistantId, $userMessage) 
    {
        $thread = OpenAI::threads()->create([]);

        [$message, $run] = $this->submitMessage($assistantId, $thread->id, $userMessage);

        return [
            $thread,
            $message,
            $run
        ];
    }

    private function waitOnRun($run, $threadId)
    {
        while ($run->status == "queued" || $run->status == "in_progress")
        {
            $run = OpenAI::threads()->runs()->retrieve(
                threadId: $threadId,
                runId: $run->id,
            );

            sleep(1);
        }

        return $run;
    }

    private function getMessages($threadId, $order = 'asc', $messageId = null)
    {
        $params = [
            'order' => $order,
            'limit' => 10
        ];

        if($messageId) {
            $params['after'] = $messageId;
        }

        return OpenAI::threads()->messages()->list($threadId, $params);
    }






    public function generateAssistantsResponse(Request $request)
    {
        $userMessage = $request->input('message');
        $chat_id = $request->input('chat_id');

    
        // Сохраняем сообщение пользователя в базу данных
        DB::table('conversation_histories')->insert([
            'role' => 'user',
            'content' => $userMessage,
            'chat_id' => $chat_id,  
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // Получаем всю историю диалога из базы данных
        $conversationHistory = DB::table('conversation_histories')->get()->map(function ($message) {
            return [
                'role' => $message->role,
                'content' => $message->content,
            ];
        })->toArray();
    
        // Отправляем историю диалога в OpenAI API
        $result = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => $conversationHistory,
        ]);
    
        // Добавляем ответ ассистента в базу данных
        $assistantResponse = $result->choices[0]->message->content;
        DB::table('conversation_histories')->insert([
            'role' => 'assistant',
            'chat_id' => $chat_id,
            'content' => $assistantResponse,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    
        // Возвращаем ответ ассистента
        return response()->json([
            'assistant_response' => $assistantResponse,
        ]);
    }


    
    private function processRunFunctions($run)
{
    // check if the run requires any action
    while ($run->status == 'requires_action' && $run->requiredAction->type == 'submit_tool_outputs')
    {
        // Extract tool calls
        // multiple calls possible
        $toolCalls = $run->requiredAction->submitToolOutputs->toolCalls; 
        $toolOutputs = [];

        foreach ($toolCalls as $toolCall) {
            $name = $toolCall->function->name;
            $arguments = json_decode($toolCall->function->arguments);

            if ($name == 'describe_image') {
                $visionResponse = OpenAI::chat()->create([
                    'model' => 'gpt-4-vision-preview',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    "type" => "text",
                                    "text" => $arguments?->user_message
                                ],
                                [
                                    "type" => "image_url",
                                    "image_url" => [
                                        "url" => $arguments?->image,
                                    ],
                                ],
                            ]
                        ],
                    ],
                    'max_tokens' => 2048
                ]);

                // you get 1 choice by default
                $toolOutputs[] = [
                    'tool_call_id' => $toolCall->id,
                    'output' => $visionResponse?->choices[0]?->message?->content
                ];
            }
        }

        $run = OpenAI::threads()->runs()->submitToolOutputs(
            threadId: $run->threadId,
            runId: $run->id,
            parameters: [
                'tool_outputs' => $toolOutputs,
            ]
        );

        $run = $this->waitOnRun($run, $run->threadId);
    }

    return $run;


    
}









}





