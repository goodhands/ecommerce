<?php

    return [
        'standard' => [
            'name' => 'Standard Delivery',
            'description' => 'Standard delivery, we will ship the order to your location. 
                                Price range differs based on your location',
            'label' => 'standard-delivery',
            'type' => 'manual',
            'cost' => '',
        ],
        'local' => [
            'name' => 'Local Delivery',
            'description' => 'Shipping description here',
            'label' => 'local-delivery',
            'type' => 'manual',
        ],
        'gokada' => [
            'name' => 'Gokada Delivery',
            'label' => 'gokada-delivery',
            'description' => 'Shipping description here',
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