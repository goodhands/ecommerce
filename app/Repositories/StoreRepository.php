<?php

namespace App\Repositories;

use App\Models\Store;

class StoreRepository{
    public function initialize(string $storeName){
        $store = Store::create([
            'shortname' => $storeName
        ]);

        //emit event to send an email to the user or something

        return $store;
    }
}