<?php

namespace App\Http\Controllers;

use App\Models\Store\Secrets;
use App\Repositories\StoreRepository;
use Exception;
use Illuminate\Http\Request;

class SecretsController extends Controller
{
    public function __construct(StoreRepository $store)
    {
        $this->store = $store;
    }

    public function store(Request $request){

        $request->validate([
            'provider_id' => 'required|integer',
            'provider_type' => 'required|string',
            'public_key' => 'string',
            'api_key' => 'string',
            'secret_key' => 'string'
        ]);
        
        $secret = $this->store->addSecret($request->all());

        if($secret instanceof Secrets){
            return true;
        }else{
            throw new Exception("Failed to create new secret");
        }
    }
}
