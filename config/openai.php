<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OpenAI API Key and Organization
    |--------------------------------------------------------------------------
    |
    | Here you may specify your OpenAI API Key and organization. This will be
    | used to authenticate with the OpenAI API - you can find your API key
    | and organization on your OpenAI dashboard, at https://openai.com.
    */

    'api_key' => "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",
    'organization' => env('OPENAI_ORGANIZATION'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout may be used to specify the maximum number of seconds to wait
    | for a response. By default, the client will time out after 30 seconds.
    */

    'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 30),
];
