<?php

namespace App\Http\Controllers;
use App\Models\Store;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
class CustomerController extends Controller
{
    public function index(Store $store, Request $request)
    {
        QueryBuilder::for($store->customers())
                    ->allowedFilters(
                        AllowedFilter::scope('date_between')
                    )
                    ->defaultSort('created_at')
                    ->allowedSorts(['created_at'])
                    ->get();
    }
}
