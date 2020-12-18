<?php

namespace App\Repositories\Traits;

use App\Models\Store\Customer;
use Exception;

trait Customers{
    public function addCustomers($data, $store){
        $customer = Customer::create($data);

        $store->customers()->save($customer);

        return $customer;
    }

    public function updateCustomers($data, $customer, $store){

        $didUpdate = Customer::where('store_id', $store->id)
                            ->where('id', $customer->id)
                            ->update($data);    
        if($didUpdate){
            return Customer::find($customer->id);
        }else{
            throw new Exception("Failed to update existing customer");
        }
    }
}