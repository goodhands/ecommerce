<?php

namespace App\Http\Controllers;

use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Store;
use App\Models\Store\Collections\Collections;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

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
            'shortname' => Str::slug($request->name .'-'. rand(0, 999)),
        ]);

        $collection = $this->store->addCollection($request->except('media'), $shortname);

        //this is hacky way to do it|we want to save the name returned by cloud
        $collection->media = $mediaName;
        $collection->save();
            
        return $collection;
    }

    public function index(Store $shortname){
        $response = QueryBuilder::for($shortname->collections())
        ->allowedFilters(
            AllowedFilter::exact('description'),
        )
        ->defaultSort('created_at')
        ->allowedSorts(['created_at'])
        ->withCount('products')
        ->get();

        return $response;
    }

    public function search(Store $shortname, $keyword){
        return $this->store->searchCollection($keyword, $shortname);
    }

    public function addProduct(Store $shortname, Request $request){
        $request->validate([
            'collectionId' => 'required|integer',
            'productId' => 'required|integer'
        ]);

        return $this->store->
                    addProduct($request->collectionId, $request->productId, $shortname);
    }

    public function show(Store $shortname, Collections $collection){
        return $shortname->collections->where('shortname', $collection->shortname);
    }
}
