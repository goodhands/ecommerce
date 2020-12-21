<?php

$paystackOptions = ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer'];
$flutterOptions = ["account", "card", "banktransfer", "mpesa", "qr", "ussd", "credit", "barter", "paga"];

return [
    'paystack' => [
        'website' => 'https://dashboard.paystack.com/',
        'id' => 'paystack-pay',
        'api' => 'https://api.paystack.co',
        'description' => '',
        'method' => $paystackOptions,
        'settings' => [
            'require' => [
                ['label' => 'Public Key', 'key' => 'public_key'],
                ['label' => 'Secret key', 'key' => 'secret_key'],
                [
                    'label' => 'Methods', 'key' => 'methods',
                    'options' => $paystackOptions
                ]
            ]
        ]
    ],
    'flutterwave' => [
        'website' => 'https://dashboard.flutterwave.com/',
        'id' => 'flutter-pay',
        'api' => 'https://api.paystack.co',
        'method' => $flutterOptions,
        'description' => '',
        'settings' => [
            'require' => [
                ['label' => 'Public Key', 'key' => 'public_key'],
                ['label' => 'Secret key', 'key' => 'secret_key'],
                [
                    'label' => 'Methods', 'key' => 'methods',
                    'options' => $flutterOptions
                ]
            ]
        ]
    ]
];