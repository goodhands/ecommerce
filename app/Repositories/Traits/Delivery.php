<?php

namespace App\Repositories\Traits;

use App\Models\Store\Delivery\Methods;
use Illuminate\Support\Str;
use Exception;

trait Delivery{
    
    public function loadMethodFromConfigFile(){
        $methods = config('providers.delivery');

        foreach($methods as $key => $method){
            $data = [];
            $data['name'] = $method['name'];
            $data['description'] = $method['description'];
            $data['type'] = $method['type'];

            //remove the first portion of the string and uppercase it 
            $data['label'] = ucfirst(Str::first($method['label'], "-"));
            
            if(array_key_exists("website", $method)){
                $data['website'] = $method['website'];
            }

            //create method
            $created = Methods::create($data);

            if(!$created){
                throw new Exception("Failed to create delivery method");
            }
        }
    }

    public function addDeliveryMethods($data, $store){
        //ensure user has access
        $this->userHasAccess($store);

        //find the delivery method based on the unique label
        $delivery = Methods::where('label', $data['label'])->first();

        //add method to store
        $storeDelivery = $store->delivery()->save($delivery, [
            'notes' => $data['notes'],
            'flat_rate' => $data['flat_rate'],
            'conditional_pricing' => isset($data['conditional_pricing']) ?? null,
        ]);

        return $storeDelivery;
    }
}