<?php

namespace App\Http\Controllers;

use App\Jobs\CalculateOrderTotal;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Store\Order;
use App\Repositories\StoreRepository;

class OrdersController extends Controller
{
    public function __construct(StoreRepository $store)
    {
        $this->storeModel = $store;
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
            'payment_method' => 'string|required', //ideally should be the id of the payment method in the store_payments table
            'delivery_method' => 'string|required', //same as above
            'customer_id' => 'integer|required',
        ]);

        $request->request->add([
            'fulfilled' => false,
        ]);

        // create order
        $order = Order::create($request->except('products'));

        $store->orders()->save($order);

        //dispatch job to calculate order total
        CalculateOrderTotal::dispatch($order);

        //save products
        foreach((array) $request->products as $product){
            $order->products()->attach($product['product'], ['quantity' => $product['qty']]);
        }

        return $order->products;
    }

    public function index(Store $store){
        return $store->orders()->paginate(20);
    }

    public function show(Store $store, Order $order){
        return response()->json($order);
    }
}
