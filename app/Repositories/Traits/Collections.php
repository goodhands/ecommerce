<?php 

namespace App\Repositories\Traits;

//renaming to avoid naming conflict
use App\Models\Store\Collections\Collections as Collection;

trait Collections{
    public function addCollection($data, $store){
        $this->userHasAccess($store);

        $collection = Collection::create($data);

        $store->collections()->save($collection);
        
        return $collection;
    }

    public function searchCollection($keyword, $store){
        return Collection::
                    where('store_id', $store->id)
                    ->where('name', 'LIKE', "%$keyword%")
                    ->get();
    }
}