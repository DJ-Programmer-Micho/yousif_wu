<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'telegram-bot-api' => [
        'token' => env('TELEGRAM_BOT_TOKEN', '8268288509:AAFKSUB4twHJGF9FTnEozYgTSvRhx1VgYx0')
    ],
    'whatsapp' => [
        'phone_id' => env('WHATSAPP_PHONE_ID'),
        'token'    => env('WHATSAPP_TOKEN'),
        'test_to'  => env('WHATSAPP_TEST_TO', '9647501903720'),
        'template_sender' => env('WHATSAPP_SENDER_TEMPLATE', 'msg_v3'),
        'template_receiver' => env('WHATSAPP_RECEIVER_TEMPLATE', 'msg_v4'),
        'lang'     => env('WHATSAPP_TEMPLATE_LANG', 'en'),
    ],


];
