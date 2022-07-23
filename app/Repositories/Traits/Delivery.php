<?php

namespace App\Repositories\Traits;

use App\Models\Store\Delivery\Methods;
use Illuminate\Support\Str;
use Exception;

trait Delivery
{

    public function loadMethodFromConfigFile()
    {
        $methods = config('providers.delivery');

        foreach ($methods as $key => $method) {
            $data = [];
            $data['name'] = $method['name'];
            $data['description'] = $method['description'];
            $data['type'] = $method['type'];

            //remove the first portion of the string and uppercase it
            $data['label'] = ucfirst(Str::after($method['label'], "-"));

            if (array_key_exists("website", $method)) {
                $data['website'] = $method['website'];
            }

            //create method
            $created = Methods::create($data);

            if (!$created) {
                throw new Exception("Failed to create delivery method");
            }
        }
    }

    public function addDeliveryMethods($data, $store)
    {
        //ensure user has access
        $this->userHasAccess($store);

        //find the delivery method based on the unique label
        $delivery = Methods::where('label', $data['label'])->first();

        if (!$delivery) {
            throw new Exception("An error occurred while handling this request {$delivery}");
        }

        //check if payment provider has already been added
        if ($store->delivery->contains('id', $delivery->id)) {
            throw new Exception("That payment provider has already been setup for this store.");
        }

        //add method to store
        $storeDelivery = $store->delivery()->save($delivery, [
            'notes' => $data['notes'],
            'flat_rate' => $data['flat_rate'],
            'conditional_pricing' => isset($data['conditional_pricing']) ?? null,
        ]);

        //if type is 3rd party and secrets don't exist
        if ($data->type === "3rd party") {
            if (!$data->hasAny(['public_key', 'secret_key', 'api_key'])) {
                throw new Exception("A secret key or api must be passed along for payment methods using a 3rd party");
            }
        }

        //only need secrets for third parties
        if ($data->type == "3rd party") {
            $secrets = [
                'provider_type' => 'payment',
                'provider_id' => $delivery->id
            ];

            $secrets['public_key'] = ($data->has('public_key')) ? $data->public_key : '';
            $secrets['secret_key'] = ($data->has('secret_key')) ? $data->secret_key : '';
            $secrets['api_key'] = ($data->has('api_key')) ? $data->api_key : '';

            $this->addSecret($secrets, $store);
        }

        return $storeDelivery;
    }

    public function resolveDeliveryProvider($store, $id)
    {
        //returns an instance of the Methods class
        return $store->delivery->where('id', $id)->first();
    }
}
