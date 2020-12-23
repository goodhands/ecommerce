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

        //payment methods: this should give us paystack's id
        $methods = $store->payment->where('id', $data->payment_method)->first()->methods;

        //retrieve customer details
        $amount = $order->total * 100; //we store the value in naira but convert it to kobo for Paystack
        $email = $order->customer->email;

        $res = paystack()->prepare($key)->getAuthorizationResponse([
                    'amount' => $amount,
                    'reference' => paystack()->genTranxRef(),
                    'email' => $email,
                    'callback_url' => config('app.url') . '/api/v1/checkout/verify?store='. $store->shortname .'&order=' . $order->id . '&provider='.$data->payment_method,
                    'channels' => (null != $methods) ? $methods : config('providers.payment.paystack.method'),
                    'metadata' => json_encode([ 'custom_fields' => [
                            ['display_name' => "Order Id", "variable_name" => "order_id", "value" => $order->id],
                            ['display_name' => "Products", "variable_name" => "products", "value" => $this->getOrderProductNamesGlued($order)],
                            ['display_name' => "Customer", "variable_name" => "customer_name", "value" => $order->customer->firstname .' '. $order->customer->lastname],
                        ]
                    ])
                ]);
        
        return response()->json([$res, $amount]);
    }

    public function payWithFlutterwave($data, $store, $order){
        return "pay with flutterwave";
    }

    public function verify($store, $request){
        //call the right verifier
        $provider = PaymentMethods::find($request->provider);

        return $this->{"verify".$provider->label}($store, $request);
    }

    public function verifyPaystack($store, $request){
        $key = $store->secrets->where('provider_id', $request->provider)->first()->secret_key;

        return paystack()->prepare($key)->getPaymentData();
    }
}