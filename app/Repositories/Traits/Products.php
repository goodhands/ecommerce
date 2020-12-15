<?php

namespace App\Repositories\Traits;
use App\Models\Store\Product;

trait Products{
    /**
     * Add products to a store
     */
    public function addProducts($data, $store){
        $productHelper = new Product;

        //require auth to create a product
        $this->userHasAccess($store);

        //create product
        $product = $productHelper::create($data);

        //attach product to store
        $store->products()->save($product);

        return $product;
    }
}