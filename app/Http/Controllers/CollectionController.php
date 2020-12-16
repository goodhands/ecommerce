<?php

namespace App\Http\Controllers;

use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Store;
class CollectionController extends Controller
{
    public function __construct(StoreRepository $store)
    {
        $this->store = $store;
    }

    public function store(Request $request, Store $shortname){
        $request->validate([
            'name' => 'required|string',
            'hasAutomation' => 'integer',
            'media' => 'file|max:2048', //max 2mb
        ]);

        //upload media
        $mediaName = $request->file('media')->storeOnCloudinary('commerce')->getSecurePath();
        
        $request->request->add([
            'createdBy' => auth()->user()->name,
            'shortname' => Str::slug($request->name),
            'media' => $mediaName
        ]);
        
        $collection = $this->store->addCollection($request->except('store_id'), $shortname);
            
        return $collection;
    }
}
