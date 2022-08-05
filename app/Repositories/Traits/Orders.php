<?php

namespace App\Repositories\Traits;

use App\Models\Store\Order;

trait Orders
{
    public function calculateTotal($order, $delivery)
    {
        $productTotal = [];

        foreach ($order->products as $product) {
            $productTotal[] = $product->price * $product->pivot->quantity;
        }

        $total = array_sum($productTotal) + $delivery->pivot->flat_rate;

        $order->total = $total;

        $order->save();
    }

    public function findOrder($id)
    {
        return Order::find($id);
    }

    public function getOrderProductNamesGlued($order)
    {
        $products = $order->products->pluck('name')->toArray();
        return implode(", ", $products);
    }
}
