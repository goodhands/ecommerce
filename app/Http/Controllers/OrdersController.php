<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Store\Customer;
use App\Models\Store\Order;
use App\Repositories\StoreRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class OrdersController extends Controller
{
    private $paymentService;

    public function __construct(StoreRepository $store, Carbon $carbon)
    {
        $this->storeModel = $store;
        $this->carbon = $carbon;
    }

    /**
     * Call order methods based on step
     */
    public function checkout(Request $request, Store $store)
    {
        if ($request->step == "customer") {
            return $this->addCustomer($request, $store);
        }

        if ($request->step == "products") {
            return $this->store($request, $store);
        }
    }

    /**
     * This is the final step in creating an
     * order. This returns a url (by calling $this->pay())
     * that the user
     * will be redirected to for payment based on
     * the provider they selected.
     */
    public function store($request, $store)
    {
        $request->validate([
            'products' => "array|required",
            'payment_method' => 'integer|required', //ID of provider, e.g: Paystack, Flutterwave, etc
            'delivery_method' => 'integer|required', //ID of provider, e.g: Gokada, Max.ng, etc
            'customer_id' => 'integer|required',
        ]);

        $request->request->add([
            'fulfilled' => false,
        ]);

        // create order
        $order = Order::create($request->except('products'));

        $store->orders()->save($order);

        //save products to the order
        foreach ((array) $request->products as $product) {
            $order->products()->attach($product['product'], ['quantity' => $product['qty']]);
        }

        //////////////////////////////////////////
        //calculate order total and update the db
        //////////////////////////////////////////

        //get the delivery fees
        $delivery = $this->storeModel->resolveDeliveryProvider($store, $request->delivery_method);

        $this->storeModel->calculateTotal($order, $delivery);

        //returns some values based on the payment provider selected.
        //3rd parties return a url to checkout
        //manual returns payment instructions
        return $this->storeModel->acceptPayment($request, $store, $order);
    }

    /**
     * Endpoint to store a customer details during
     * checkout
    */
    public function addCustomer($request, $store)
    {
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

        if (!$customer) {
            return $this->storeModel->addCustomers($request->except('step'), $store);
        } else {
            //pass the customer found and update it
            return $this->storeModel->updateCustomers($request->except('step'), $customer, $store);
        }
    }

    /**
     * Show all orders
     */
    public function index(Store $store, Request $request)
    {
        //make sure only the admin can do this
        $this->storeModel->userHasAccess($store);

        $response = QueryBuilder::for($store->orders())
                    ->allowedFilters(
                        AllowedFilter::exact('fulfilled'),
                        AllowedFilter::scope('paid'),
                        AllowedFilter::scope('date_between')
                    )
                    ->defaultSort('created_at')
                    ->allowedSorts(['created_at'])
                    ->with(['products', 'customer'])
                    ->get();

        return $response;
    }

    /**
     * Show a single order
     */
    public function show(Store $store, Order $order)
    {
        $order->load(['customer', 'products', 'delivery', 'payment']);

        return $order;
    }

    /**
     * Verify order payment made using a 3rd party
     */
    public function verify(Request $request)
    {
        //find store by shortname and update payment status
        //if valid ref was found
        $store = $this->storeModel->findStore($request->store);
        $order = $this->storeModel->findOrder($request->order);

        //confirm money has been received for the order
        $order->payment_status = "Paid";

        $verifiedPayment = json_decode(json_encode($this->storeModel->verify($store, $request)));

        //update transaction reference
        $order->reference = $verifiedPayment->data->reference;

        $order->save();

        $checkout = $store->url . $store->shortname . "/checkout/?order=" . $order->id . "&status=complete";

        if ($verifiedPayment->data->status == "success") {
            return redirect($checkout);
        } else {
            return redirect(str_replace("complete", "failed", $checkout));
        }
    }
}
