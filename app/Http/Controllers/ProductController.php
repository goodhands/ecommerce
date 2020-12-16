<?php

namespace App\Http\Controllers;

use App\Jobs\HandleProductMedia;
use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Store;
use App\Models\Store\Product;

class ProductController extends Controller
{
    public function __construct(StoreRepository $store)
    {
        $this->store = $store;
    }

    /**
     * Needed endpoints:
     * 1) Initiate product creation, set draft status & return id for later use
     * 2) Upload product images library and dispatch event once it's finished
     * 3) Add other product details including:
     * - name, price, discount, type (physical/digital), description
     * - 
     */
    public function initialize(){
        $product = Product::create([
            'status' => 'draft'
        ]);

        return $product;
    }

    public function upload(Request $request, Store $shortname){
        dispatch(new HandleProductMedia($request->files, $shortname));
    }

    public function store(Request $request, Store $shortname){
        $request->validate([
            "name" => "required|string|max:200",
            "price" => "required|integer",
            "description" => "required|string",
        ]);

        //automatically generate shortname based on name and random string
        $request->request->add([
            'shortname' => Str::slug($request->name) .'-'. rand(0001, 9999)
        ]);

        $product = $this->store->addProducts($request->all(), $shortname);
        
        return $product;
    }
}
