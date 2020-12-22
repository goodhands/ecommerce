<?php

namespace App\Http\Controllers;

use App\Repositories\StoreRepository;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
    public function store(Store $store, Request $request){
        $request->validate([
            'public_key' => 'string|required_without:api_key',
            'secret_key' => 'string|required_without:api_key',
            'api_key' => 'string|required_without:secret_key,public_key',
            'methods' => 'required|array',
            'id' => 'string|required', //the internal id of the provider [name + pay]
        ]);

        $request->request->add([
            'label' => ucfirst(Str::before($request->id, '-')),
            'active' => true
        ]);

        $data = $this->storeModel->addPaymentMethod($request, $store);

        return $data;
    }

    public function index(Store $store){
        return $store->payment;
    }

    /**
     * Get paystack's options
     */
    public function paystack(){
        return $this->providers['paystack'];
    }

    /**
     * Return all providers supported so 
     * they can choose which to setup
     */
    public function providers(){
        return $this->providers;
    }

}
