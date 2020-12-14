<?php

namespace App\Repositories;

use App\Models\Store;
use Exception;

class StoreRepository{
    public function initialize(string $storeName){
        $store = Store::create([
            'shortname' => $storeName
        ]);

        //queue a job that will run if the user doesn't complete sign up

        return $store;
    }

    /**
     * $data = [
     *  "name" => "New name",
     *  "size" => "New size"
     * ]
     * 
     * @throws Exception
     * @return App\Models\Store
     */
    public function updateStore(array $data, int $storeId){
        $didUpdate = Store::whereId($storeId)
                ->update($data);

        if(1 == $didUpdate){
            return Store::find($storeId);
        }else{
            throw new Exception("Failed to update store");
        }
    }
}