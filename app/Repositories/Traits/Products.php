<?php

namespace App\Repositories\Traits;
use App\Models\Store\Product;

trait Products{
    /**
     * Add products to a store
     */
    public function addProducts($data, $store, $productId = null){
        $productHelper = new Product;

        //require auth to create a product
        $this->userHasAccess($store);

        //create product
        if(!$productId){
            $product = $productHelper::create($data);
        }else{
            $product = $productHelper::whereId($productId)
                        ->update($data);
        }

        //attach product to store
        $store->products()->save($product);

        return $product;
    }
}