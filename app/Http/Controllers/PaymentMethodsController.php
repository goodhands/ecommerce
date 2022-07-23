<?php

namespace App\Http\Controllers;

use App\Repositories\StoreRepository;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentMethodsController extends Controller
{
    /**
     * @var mixed
     */
    public $providers;

    public function __construct(StoreRepository $storeModel)
    {
        $this->providers = config('providers.payment');
        $this->storeModel = $storeModel;
    }

    /**
     * Activate a new provider for a store
     */
    public function store(Store $store, Request $request)
    {
        $request->validate([
            'public_key' => 'string|required_if:type,3rd party',
            'secret_key' => 'string|required_if:type,3rd party',
            'api_key' => 'string|required_without_all:secret_key,public_key|sometimes',
            'channels' => 'array',
            'active' => 'bool',
            'notes' => 'string|max:200',
            //this will be submitted with the form. the value is
            //gotten from the config settings for the provider.
            //although we are not saving it in the db, it's useful
            //for determining how we should handle the request
            'type' => 'string|required',
            'id' => 'string|required', //the internal id of the provider [name-pay => paystack-pay]
        ]);

        $data = $this->storeModel->addPaymentMethod($request, $store);

        return $data;
    }

    /**
     * Get all payment methods belonging to the store
     */
    public function index(Store $store)
    {
        return $store->payment;
    }

    /**
     * Get paystack's options
     */
    public function paystack()
    {
        return $this->providers['paystack'];
    }

    /**
     * Return all providers supported so
     * they can choose which to setup
     */
    public function providers()
    {
        return $this->providers;
    }

    /**
     * Setup payment intent for this user
     *
     * @return Stripe\SetupIntent
     */
    public function intent(Request $request)
    {
        $user = User::first();

        Auth::loginUsingId($user->id);

        $intent = $user->createSetupIntent();

        Log::debug("Intent " . print_r($intent, true));

        return $intent;
    }

    /**
     * Pay for items in your cart
     *
     * @queryString cardToken string required The tokenization id of the user's card
     * @bodyParam amount integer required The amount to charge the user's card
     *
     * @throws \Laravel\Cashier\Exceptions\IncompletePayment
     */
    public function pay(Request $request)
    {
        $user           = User::first();
        // $user           = $request->user();
        $paymentMethod  = $request->payment_method;
        $storeCard      = $request->storeCard;
        $cardholderName = $request->name;
        $address = $request->billing_address;
        $amount         = 5000; // Cart::total();

        try {
            $user->createOrGetStripeCustomer();
            $user->updateDefaultPaymentMethod($paymentMethod);
            $amount = $amount * 100; //convert to cent
            $payment = $user->charge($amount, $paymentMethod);

            if ($payment->status === 'succeeded') {
                $user->billing_address = $address;
                $user->save();
            }

            Log::debug("Payment " . print_r($payment, true));

            return response()->json(['status' => 'success', 'data' => ['payment' => $payment, 'user' => $user, 'cardHolderName' =>  $cardholderName]]);
        } catch (\Throwable $th) {
            throw $th;
            // return response()->json(['status' => 'error', 'message' => $th]);
        }
    }
}
