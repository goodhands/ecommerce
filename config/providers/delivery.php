<?php

    return [
        'standard' => [
            'name' => 'Standard Delivery',
            'description' => 'Ship orders based to customers, set prices based on their location/region',
            'label' => 'standard-delivery',
            'type' => 'regional',
            'cost' => '',
            'settings' => [
                'require' => [
                    ['label' => 'API Key', 'key' => 'api_key'],
                ]
            ]
        ],
        'gokada' => [
            'name' => 'Gokada Delivery',
            'label' => 'gokada-delivery',
            'description' => 'Let Gokada handle your delivery',
            'website' => 'https://gokada.ng/',
            'api' => 'https://api.gokada.ng/',
            'type' => '3rd party',
            'settings' => [
                'require' => [
                    ['label' => 'API Key', 'key' => 'api_key'],
                ]
            ]
        ],
        'pickup' => [
            'name' => 'Pickup',
            'description' => 'Shipping description here',
            'label' => 'local-delivery',
            'type' => 'manual',
            'cost' => 'Free',
            'options' => [
                'time' => ['1 hr', '2 hr', '5 hr', '24hrs', '1 day', '1-2 day', '3-4 days', '5+ days']
            ],
        ],
    ];
