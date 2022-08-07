<?php

namespace App\Repositories;

use App\Events\StoreCreated;
use App\Events\TrackNewUser;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use Illuminate\Support\Str;
use App\Repositories\Traits\Products as HasProducts;
use App\Repositories\Traits\Collections as HasCollections;
use App\Repositories\Traits\Customers as HasCustomers;
use App\Repositories\Traits\Delivery as HasDelivery;
use App\Repositories\Traits\Orders as HasOrders;
use App\Repositories\Traits\Payments as HasPayments;
use App\Repositories\Traits\Secrets as HasSecrets;
use App\Repositories\Traits\Media as HasMedia;
use App\Repositories\Traits\Analytics as HasAnalytics;
use Exception;

class StoreRepository
{
    use HasProducts;
    use HasCollections;
    use HasCustomers;
    use HasSecrets;
    use HasPayments;
    use HasOrders;
    use HasDelivery;
    use HasMedia;
    use HasAnalytics;

    public function __construct(Store $store)
    {
        $this->storeModel = $store;
    }

    public function initialize(array $data)
    {
        $store = Store::create([
            'shortname' => $data['store'],
            'url' => isset($data['url']) && $data['url'] !== "" ? $data['url'] : $this->createUniqueStoreUrl($data['store']),
        ]);

        //queue a job that will run if the user doesn't complete sign up
        event(new TrackNewUser($store));

        return $store;
    }

    /**
     * $data = [
     *  "name" => "New name",
     *  "size" => "0-5"
     * ]
     *
     * @throws Exception
     * @return App\Models\Store
     */
    public function updateStore(array $data, int $storeId, bool $requiresAuth)
    {
        //get the store
        $store = Store::find($storeId);

        //verify user has the right permissions
        if ($requiresAuth) {
            $this->userHasAccess($store);
        }

        $didUpdate = Store::whereId($storeId)
            ->update($data);

        if (1 == $didUpdate) {
            //we want to return the newly updated details to
            //the user and not the old details
            return Store::find($storeId);
        } else {
            throw new Exception("Failed to update store" . $didUpdate);
        }
    }

    public function createStore(array $data)
    {
        $store = Store::create($data);

        event(new StoreCreated($data));

        return new StoreResource($store);
    }

    /**
     * Checks if user has access to interact
     * with this store
     */
    public function userHasAccess($store)
    {
        if (!auth()->user()) throw new Exception("User is not authenticated");

        if (auth()->user()->cannot('update', $store)) {
            throw new Exception("You do not have the right permissions for that action");
        }
    }

    /**
     * Find a store by shortname.
     * Shortname is our primary key
     */
    public function findStore($shortname)
    {
        return Store::where('shortname', $shortname)->first();
    }

    public function createUniqueStoreUrl($shortname)
    {
        $shortname = "https://" . $shortname . "myduxstore.com";
        $urlExists = Store::where('url', $shortname)->exists;

        if (!$urlExists) {
            return $shortname;
        }

        $url = $shortname . Str::random(4);

        return $this->createUniqueStoreUrl($url);
    }
}
