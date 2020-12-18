<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Store\Order;

class OrdersController extends Controller
{
    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function checkout(Request $request, Store $store){
        if($request->step == "customer"){

        }

        if($request->step == "products"){
            return $this->store($request, $store);
        }
    }

    public function store($request, $store){
        $request->validate([
            'products' => "array|required",
            'payment_method' => 'string|required',
            'delivery_method' => 'string|required',
            'customer_id' => 'integer|required',
        ]);

        $request->request->add([
            'fulfilled' => false,
        ]);

        // create order
        $order = Order::create($request->except('products'));

        $store->orders()->save($order);

        //save products
        foreach((array) $request->products as $product){
            $order->products()->attach($product['product'], ['quantity' => $product['qty']]);
        }

        return $order->products;
    }
}
