<?php

return [
    'gateways' => [
        'credit_card' => [
            'class' => App\Payments\Gateways\CreditCardGateway::class,
            'config' => [
                'api_key' => env('CREDIT_CARD_API_KEY'),
            ],
        ],
        'paypal' => [
            'class' => App\Payments\Gateways\PaypalGateway::class,
            'config' => [
                'client_id' => env('PAYPAL_CLIENT_ID'),
                'client_secret' => env('PAYPAL_CLIENT_SECRET'),
            ],
        ],
    ],
];
