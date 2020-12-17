<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;

class OrdersController extends Controller
{
    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function store(Request $request, Store $store){
        // $request->validate([
        //     'productId' => 'array|required',
        //     ''
        // ]);
    }
}
