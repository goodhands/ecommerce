<?php

namespace App\Http\Controllers;

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
     * 1) Once "new product" is clicked => Initiate product creation, set draft status & return id for later use
     * 2) Upload product images library and dispatch event once it's finished
     * 3) Add other product details including:
     * - name, price, discount, type (physical/digital), description
     * - 
     */
    public function createProduct(Request $request, Store $shortname){
        //returns the product id
        if($request->query('step') == 'init') return $this->initialize();
        //emits event to the frontend and updates the product media
        if($request->query('step') == 'upload') return $this->uploadMedia($request);
        //saves all details and attaches to store
        if($request->query('step') == 'save') return $this->store($request, $shortname);
    }

    public function initialize(){
        $product = Product::create([
            'status' => 'draft'
        ]);

        return $product;
    }

    /**
     * 1) Upload the products to cloudinary
     * 2) Return file names to the frontend to use for displaying the images
     * 3) The file names are added to an hidden input as 
     *  `media_library` and submitted once save is clicked
     */
    public function uploadMedia($request){
        $request->validate([
            'productId' => 'required|integer'
        ]);
        
        $responses = array();

        foreach($request->file('files') as $file){
            $responses[] = $file->storeOnCloudinary('commerce')->getSecurePath();
        }

        return $responses;
    }

    public function store($request, $shortname){
        $request->validate([
            "name" => "required|string|max:200",
            "productId" => "required|integer",
            "price" => "required|integer",
            "description" => "required|string"
        ]);

        //automatically generate shortname based on name and random string
        $request->request->add([
            'shortname' => Str::slug($request->name) .'-'. rand(0001, 9999)
        ]);

        $product = $this->store->addProducts($request->except('productId', 'step'), $shortname, $request->productId);
        
        return $product;
    }

    public function getProductByShortname(Request $request, Store $shortname, $slug){
        return $shortname->products->where('shortname', $slug)->first();
    }

    /**
     * Get all products
     */
    public function index(Store $shortname){
        return $shortname->products;
    }
}
