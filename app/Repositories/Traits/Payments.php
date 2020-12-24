<?php

namespace App\Repositories\Traits;

use App\Models\Store\Payments\Methods;
use Illuminate\Support\Str;
use Exception;

trait Payments{

    /**
     * We need to create a job that will handle this.
     * 
     * The job will be triggered by some endpoint that 
     * will be called whenever a new config has been added
     */
    public function loadPaymentProvidersFromConfig(){
        $providers = config('providers.payment');

        foreach($providers as $provider){
            $data['name'] = $provider['name'];
            $data['description'] = $provider['description'];
            $data['label'] = ucfirst(Str::before($provider['id'], '-'));
            $data['type'] = $provider['type'];

            //add 3rd party constraints
            if($provider['type'] == "3rd party"){

                if(!$provider['website'] || !$provider['rates']){
                    throw new Exception("To register a 3rd party provider, you must provide the website and rate of the provider. In " . $provider['name']);
                }

                $data['website'] = $provider['website'];
                $data['rates'] = $provider['rates'];
            }else{
                $data['website'] = null;
                $data['rates'] = null;
            }

            //since label is unique, we will add a constraint with it
            if(Methods::where('label', $data['label'])->exists()){
                return false;
            }

            $result[] = Methods::create($data);
        }

        return $result;
    }

    public function addPaymentMethod($data, $store){

        //fetch the method from db
        $method = Methods::where('label', Str::before($data->id, '-'))->first();

        if(!$method) throw new Exception("An error occured while handling this request");

        //check if payment provider has already been added
        if($store->payment->contains('id', $method->id)){
            throw new Exception("That payment provider has already been setup for this store.");
        }

        //attach payment method to store
        $store->payment()->save($method, [
            'notes' => $data->notes,
            'channels' => $data->channels
        ]);     
        
        //if type is 3rd party and secrets don't exist
        if($data->type == "3rd party"){
            if(!$data->hasAny(['public_key', 'secret_key', 'api_key'])){
                throw new Exception("A secret key or api must be passed along for payment methods using a 3rd party");
            }
        }
        
        //only need secrets for third parties
        if($data->type == "3rd party"){
            $secrets = [
                'provider_type' => 'payment',
                'provider_id' => $method->id
            ];
    
            $secrets['public_key'] = ($data->has('public_key')) ? $data->public_key : '';
            $secrets['secret_key'] = ($data->has('secret_key')) ? $data->secret_key : '';
            $secrets['api_key'] = ($data->has('api_key')) ? $data->api_key : '';
    
            $this->addSecret($secrets, $store);
        }

        return $method;
    }

    public function acceptPayment($data, $store, $order){
        //gives us the label of the payment provider
        $provider = $store->payment->where('id', $data->payment_method)->first()->label;

        return $this->{"payWith".$provider}($data, $store, $order);
    }

    public function payWithCoD($data, $store, $order){}

    public function payWithPaystack($data, $store, $order){

        $key = $store->secrets->where('provider_id', $data->payment_method)->first()->secret_key;

        //payment methods: this should give us paystack's id
        $methods = (array) $store->payment->where('id', $data->payment_method)->first()->pivot->channels;

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
        
        return $res;
    }

    public function payWithFlutterwave($data, $store, $order){
        return "pay with flutterwave";
    }

    public function verify($store, $request){
        //call the right verifier
        $provider = $store->payment->where('id', $request->provider)->pluck('label')->first();

        return $this->{"verify".$provider}($store, $request);
    }

    public function verifyPaystack($store, $request){
        $key = $store->secrets->where('provider_id', $request->provider)->first()->secret_key;

        return paystack()->prepare($key)->getPaymentData();
    }
}