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

    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'token' => env('TWILIO_TOKEN'),
        'phone_number' => env('TWILIO_PHONE_NUMBER'),
    ],
    'url' => [
        'admin' => env('ADMIN_URL'),
        'web' => env('WEB_URL'),
        'merchant' => env('MERCHANT_URL'),
    ],
    'plaid' => [
        'url' => env('PLAID_URL'),
        'client_id' => env('PLAID_CLIENT_ID'),
        'secret' => env('PLAID_SECRET'),
    ],
    'dwolla' => [
        'url' => env('DWOLLA_URL'),
        'client_id' => env('DWOLLA_CLIENT_ID'),
        'secret' => env('DWOLLA_SECRET'),
    ],
    'fireBase' => [
        'token' => env('FCM_SERVER_KEY') ?? 'AAAAiDvkuVA:APA91bGdU3ybWZPxUXVJftLwnTd-CchNyqBq65XMQWGRuub1mQFyoKiv_pC4vekWuzrUac0zhpkddGKqgJPNg90GXBWHJ_CdOV6AQTU1TqCYtqMd2yBs41y3zw-j0Ngmt5g6eBy7K-CI',
        'SendUrl' => env('FCM_SERVER_KEY') ?? 'https://fcm.googleapis.com/fcm/send',
    ],

];
