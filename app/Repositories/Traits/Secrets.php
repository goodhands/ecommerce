<?php

namespace App\Repositories\Traits;

use App\Models\Store\Secrets as SecretModel;

trait Secrets{
    public function addSecret($data, $store){

        $this->userHasAccess($store);

        $secret = SecretModel::create($data);

        $store->secrets()->save($secret);
        
        return $secret;
    }
}