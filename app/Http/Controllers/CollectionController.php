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
        
        //add auto generated details
        $request->request->add([
            'createdBy' => auth()->user()->name,
            'shortname' => Str::slug($request->name),
        ]);

        $collection = $this->store->addCollection($request->except('media'), $shortname);

        //this is hacky way to do it|we want to save the name returned by cloud
        $collection->media = $mediaName;
        $collection->save();
            
        return $collection;
    }

    public function index(Store $shortname){
        return $shortname->collections;
    }

    public function search(Store $shortname, $keyword){
        return $this->store->searchCollection($keyword, $shortname);
    }
}
