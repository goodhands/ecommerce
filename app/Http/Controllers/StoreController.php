<?php

namespace App\Http\Controllers;

use App\Http\Resources\StoreResource;
use App\Http\Resources\StoreUsersCollection;
use App\Http\Resources\UserCollection;
use App\Repositories\StoreRepository;
use App\Models\Store;
use App\Models\Store\User;
use Illuminate\Http\Request;

class StoreController extends Controller
{

    public function __construct(StoreRepository $storeModel)
    {
        $this->storeModel = $storeModel;
    }

    /**
     * Create a store as an old user
     */
    public function store(Request $request)
    {
        $request->validate([
            'size' => 'required|string',
            'category' => 'required|string',
            'industry' => 'required|string',
            'shortname' => 'required|string|max:100|alpha_dash|unique:stores,shortname',
        ]);

        $store = $this->storeModel->createStore($request->all());

        $store->users()->save(auth()->user(), ['role' => 'owner']);

        return $store;
    }

    public function update(Request $request){
        //we need id to update the store, of course
        $request->validate([
            'storeId' => 'required|integer'
        ]);

        $store = $this->storeModel
                ->updateStore($request->except('storeId'), $request->storeId, true);

        return new StoreResource($store);
    }

    public function show(Store $shortname){
        return new StoreResource($shortname);
    }
    
}
