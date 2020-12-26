<?php 

namespace App\Repositories\Traits;

use App\Models\Store\Order;

trait Orders{
    public function calculateTotal($order, $delivery){

        $productTotal = [];

        foreach($order->products as $product){
            $productTotal[] = $product->price * $product->pivot->quantity;
        }
        
        $order->total = array_sum($productTotal) + $delivery->flat_rate;
        
        $order->save();
    }

    public function findOrder($id){
        return Order::find($id);
    }

    public function getOrderProductNamesGlued($order){
        $products = $order->products->pluck('name')->toArray();
        return implode(", ", $products);
    }
}