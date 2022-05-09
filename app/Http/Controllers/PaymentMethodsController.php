<?php

namespace App\Http\Controllers;

use App\Repositories\StoreRepository;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;

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
            'notes' => 'string',
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

    public function intent(Request $request)
    {
        $user = User::first();
        return $user->createSetupIntent();
    }
}
