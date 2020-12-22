<?php 

namespace App\Repositories\Traits;

use App\Models\Store\Order;

trait Orders{
    public function calculateTotal($order){

        $productTotal = [];

        foreach($order->products as $product){
            $productTotal[] = $product->price * $product->pivot->quantity;
        }
        
        $order->total = array_sum($productTotal);
        
        $order->save();
    }
}