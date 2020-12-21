<?php

namespace App\Http\Controllers;

use App\Jobs\CalculateOrderTotal;
use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Store\Customer;
use App\Models\Store\Order;
use App\Repositories\StoreRepository;
class OrdersController extends Controller
{
    private $paymentService;

    public function __construct(StoreRepository $store)
    {    
        $this->storeModel = $store;
    }

    /**
     * Call order methods based on step
     */
    public function checkout(Request $request, Store $store){
        if($request->step == "customer"){
            return $this->addCustomer($request, $store);
        }

        if($request->step == "products"){
            return $this->store($request, $store);
        }

        if($request->step == "pay"){
            return $this->pay($request, $store);
        }
    }

    /**
     * Create a new order
     */
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

    /**
     * Show all orders
     */
    public function index(Store $store){
        //make sure only the admin can do this
        $this->storeModel->userHasAccess($store);

        return $store->orders()->paginate(20);
    }

    /**
     * Show a single order
     */
    public function show(Store $store, Order $order){
        return $order;
    }

    public function addCustomer($request, $store){
        $request->validate([
            'firstname' => 'required_without:lastname|string',
            'lastname' => 'required_without:firstname|string',
            'email' => 'required_without:phone|email',
            'phone' => 'required_without:email',
            'address' => 'string',
            'city' => 'string',
            'state' => 'string',
            'country' => 'string',
            'apartment' => 'string',
            'postal' => 'string',
            'promotionals' => 'boolean'
        ]);

        //if customer doesn't already exist, we will create one.
        $customer = Customer::where('email', $request->email)
                            ->orWhere('phone', $request->phone)
                            ->where('store_id', $store->id)
                            ->first();
        
        if(!$customer){
            return $this->storeModel->addCustomers($request->except('step'), $store);
        }else{
            //pass the customer found and update it
            return $this->storeModel->updateCustomers($request->except('step'), $customer, $store);
        }
    }

    public function pay($request, $store){

        $key = "sk_test_8d1d7201827d550e467186b1610506da7a739551";
        $url = "https://api.paystack.co";

        $methods = $store->payment->where('label', 'Paystack')->first()->methods;

        $key = $store->secrets->where('provider_id', '9')->first()->secret_key;

        $provider = $request->provider_label;

        $res = paystack()->prepare($key, $url)->getAuthorizationResponse([
                    'amount' => '23000',
                    'reference' => rand(0000,10000),
                    'email' => 'sam@gmail.com',
                    'callback_url' => 'http://127.0.0.1:8000/checkout?order=120940353058&provider=paystack',
                    'channels' => (null != $methods) ? $methods : config('providers.payment.paystack.method')
                ]);
        
        return response()->json($res);
    }

}
