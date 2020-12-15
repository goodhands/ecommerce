<?php

namespace App\Http\Controllers;

use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use App\Models\Store;

class ProductController extends Controller
{
    public function __construct(StoreRepository $store)
    {
        $this->store = $store;
    }

    public function store(Request $request, Store $shortname){
        return $this->store->addProducts($request->all(), $shortname);
    }
}
