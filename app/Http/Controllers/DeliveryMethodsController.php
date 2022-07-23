<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Repositories\StoreRepository;
use Illuminate\Http\Request;

class DeliveryMethodsController extends Controller
{
    public function __construct(StoreRepository $storeModel)
    {
        $this->providers = config('providers.delivery');
        $this->storeModel = $storeModel;
    }

    public function loadMethods()
    {
        return $this->storeModel->loadMethodFromConfigFile();
    }

    public function providers()
    {
        return $this->providers;
    }

    public function store(Request $request, Store $store)
    {
        $request->validate([
            'label' => 'string|required',
            'flat_rate' => 'integer',
            'notes' => 'string',
            'conditional_pricing' => 'integer'
        ]);

        return $this->storeModel->addDeliveryMethods($request->all(), $store);
    }

    public function index(Store $store)
    {
        return $store->delivery;
    }
}
