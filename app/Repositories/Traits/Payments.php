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

    public function acceptPayment($data, $store, $order){
        //gives us the label of the payment provider
        $provider = $store->payment->where('id', $data->payment_method)->first()->label;

        return $this->{"payWith".$provider}($data, $store, $order);
    }

    public function payWithPaystack($data, $store, $order){

        $key = $store->secrets->where('provider_id', $data->payment_method)->first()->secret_key;

        $url = config('providers.payment.paystack.api');

        //payment methods: this should give us paystack's id
        $methods = $store->payment->where('id', $data->payment_method)->first()->methods;

        //retrieve customer details
        $amount = $order->total * 100; //we store the value in naira but convert it to kobo for Paystack
        $email = $order->customer->email;

        $res = paystack()->prepare($key, $url)->getAuthorizationResponse([
                    'amount' => $amount,
                    'reference' => paystack()->genTranxRef(),
                    'email' => $email,
                    'callback_url' => config('app.url') . '/checkout?order=' . $data->orderId . '&provider=paystack',
                    'channels' => (null != $methods) ? $methods : config('providers.payment.paystack.method')
                ]);
        
        return response()->json([$res, $amount]);
    }

    public function payWithFlutterwave($data, $store, $order){
        return "pay with flutterwave";
    }
}