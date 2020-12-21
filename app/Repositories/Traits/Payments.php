<?php

namespace App\Repositories\Traits;

use App\Models\Store\PaymentMethods;
use Exception;

trait Payments{
    public function addPaymentMethod($data, $store){

        $method = PaymentMethods::create($data->only(['active', 'methods', 'label']));
        
        if(!$method) throw new Exception("An error occured while handling this request");

        //attach to store
        $store->payment()->save($method);

        //since the parent is using the Secrets trait, we can just call it here
        $secrets = [
            'provider_type' => 'payment',
            'provider_id' => $method->id
        ];

        $secrets['public_key'] = ($data->has('public_key')) ? $data->public_key : '';
        $secrets['secret_key'] = ($data->has('secret_key')) ? $data->secret_key : '';
        $secrets['api_key'] = ($data->has('api_key')) ? $method->api_key : '';

        $secret = $this->addSecret($secrets, $store);

        return $method;
    }
}