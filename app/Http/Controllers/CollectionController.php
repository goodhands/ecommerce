<?php

namespace App\Http\Controllers;

use App\Repositories\StoreRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Store;
use App\Models\Store\Collections\Collections;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Database\Eloquent\Builder;

class CollectionController extends Controller
{
    public function __construct(StoreRepository $store)
    {
        $this->store = $store;
    }

    public function store(Request $request, Store $shortname)
    {
        // Create an empty/dummy collection to get an ID
        if ($request->has('init')) {
            $collection = $this->store->addCollection([
                'name' => Str::random(rand(0, 4)),
                'shortname' => Str::random(rand(0, 4)),
                'hasAutomation' => false,
                'createdBy' => auth()->user()->id,
                'description' => Str::random(rand(20, 30))
            ], $shortname);

            return $collection;
        }

        $request->validate([
            'name' => 'required|string',
            'hasAutomation' => 'integer',
            'description'   => 'string',
            'media' => 'sometimes|file|max:2048', //max 2mb
            'media_url' => 'exclude_if:media,null|string|url',
        ]);

        //upload media
        if ($request->has('media')) {
            $mediaName = $request->file('media')->storeOnCloudinary('commerce')->getSecurePath();
        } else {
            $mediaName = $request->media_url;
        }

        //add auto generated details
        if(!$request->has('id')) {
            $request->request->add([
                'createdBy' => auth()->user()->id,
                'shortname' => Str::slug($request->name . '-' . rand(0, 999)),
            ]);
        }

        $collection = $this->store->addCollection($request->except(['media_url', 'media']), $shortname);

        //this is hacky way to do it|we want to save the name returned by cloud
        $collection->media = $mediaName;
        $collection->save();

        return $collection;
    }

    public function uploadMedia($request)
    {
        return $this->store->uploadMedia($request);
    }

    public function updateCollectionMedia(Request $request, Store $shortname, $id)
    {
        $files = $this->uploadMedia($request);

        $data = [
            'media' => $files
        ];

        $this->store->updateCollection($data, $id);

        return $files;
    }

    public function index(Store $shortname, Request $request)
    {

        if ($request->query('product_count')) {
            $response = $shortname->collections();
            $response = $response->withCount('products')->has('products', '>=', $request->product_count);

            $response = $request->has('limit') ? $response->limit($request->limit) : $response;

            return $response->get();
        }

        $response = QueryBuilder::for($shortname->collections())
        ->defaultSort('-created_at')
        ->allowedSorts(['created_at'])
        ->withCount('products');

        if ($request->has('paginate')) {
            $response = $response->paginate(10)->appends(request()->query());
        } else {
            $response = $response->get();
        }

        return $response;
    }

    public function search(Store $shortname, $keyword)
    {
        return $this->store->searchCollection($keyword, $shortname);
    }

    public function addProduct(Store $shortname, Request $request)
    {
        $request->validate([
            'collectionId' => 'required|integer',
            'productId' => 'required|integer'
        ]);

        return $this->store->
                    addProduct($request->collectionId, $request->productId, $shortname);
    }

    public function show(Store $shortname, Collections $collection)
    {
        return $collection->products;
    }

    public function products(Store $shortname, Collections $collection)
    {
        return $shortname->collections->where('shortname', $collection->shortname)->first();
    }
}
