<?php

$paystackOptions = ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer'];
$flutterOptions = ["account", "card", "banktransfer", "mpesa", "qr", "ussd", "credit", "barter", "paga"];

return [
    'paystack' => [
        'name' => 'Pay with Paystack',
        'website' => 'https://dashboard.paystack.com/',
        'id' => 'paystack-pay',
        'api' => 'https://api.paystack.co',
        'description' => 'Pay with Paystack',
        'method' => $paystackOptions,
        'type' => '3rd party',
        'rates' => '2%',
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
        'name' => 'Pay with Flutterwave',
        'website' => 'https://dashboard.flutterwave.co',
        'id' => 'flutterwave-pay',
        'api' => 'https://api.flutterwave.co',
        'method' => $flutterOptions,
        'description' => 'Pay with Flutterwave',
        'type' => '3rd party',
        'rates' => '2%',
        'settings' => [
            'require' => [
                ['label' => 'Public Key', 'key' => 'public_key'],
                ['label' => 'Secret key', 'key' => 'secret_key'],
                ['label' => 'Notes', 'key' => 'notes'],
                [
                    'label' => 'Methods', 'key' => 'methods',
                    'options' => $flutterOptions
                ],
            ]
        ]
    ],
    'cod' => [
        'name' => 'Cash on Delivery',
        'id' => 'cod-pay', //cash-on-delivery
        'description' => 'Pay with Cash on Delivery',
        'type' => 'manual',
        'settings' => [
            'require' => [
                ['label' => 'Notes', 'key' => 'notes'],
            ]
        ]
    ],
];