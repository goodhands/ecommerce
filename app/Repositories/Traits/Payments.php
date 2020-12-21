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

    public function acceptPayment(){

    }

    public function payWithPaystack($data, $store){

        $key = $store->secrets->where('provider_id', $data->providerId)->first()->secret_key;

        $url = config('providers.payment.paystack.api');

        $methods = $store->payment->where('label', 'Paystack')->first()->methods;

        $provider = $data->provider_label;

        $res = paystack()->prepare($key, $url)->getAuthorizationResponse([
                    'amount' => '23000',
                    'reference' => rand(0000,10000),
                    'email' => 'sam@gmail.com',
                    'callback_url' => config('app.url') . '/checkout?order=' . $data->orderId . '&provider=paystack',
                    'channels' => (null != $methods) ? $methods : config('providers.payment.paystack.method')
                ]);
    }
}