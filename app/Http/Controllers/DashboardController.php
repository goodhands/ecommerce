<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Carbon\Carbon;
use App\Repositories\StoreRepository;

class DashboardController extends Controller
{
    public function __construct(Carbon $carbon, StoreRepository $repository)
    {
        $this->carbon = $carbon;
        $this->repository = $repository;

        //stats for this current week
        $this->dateQuery = [$this->carbon->startOfWeek(0), $this->carbon->now()];
    }
    /**
     * Get stats on sales, customers and visits for the last week
     */
    public function getWeeklyStats(Store $store, Request $request)
    {
        //sales involves where payment was confirmed
        $salesQuery = $store->orders()->where('payment_status', 'Paid')
                                ->whereBetween('created_at', $this->dateQuery);

        $response['sales_sum'] = $salesQuery->pluck('total')->sum();
        $response['sales_count'] = $salesQuery->count();

        $response['new_orders'] = $store->orders()
                                    ->wherePaymentStatus('Paid')
                                    ->whereFulfilled(false)
                                    ->whereBetween('created_at', $this->dateQuery)->count();

        // Link for new orders that have not been fulfiled
        $response['sales_link'] = 'orders?filter[fulfilled]=1&sort=-created_at&filter[date_between]=last week,today';

        //last 7 days
        $response['customers_count'] = $store->customers()->whereBetween('created_at', $this->dateQuery)->count();

        $response['customers_link'] = 'customers?sort=-created_at&filter[date_between]=last week,today';

        $response['store_url'] = $store->url;
        $response['visits'] = $this->repository->getStoreVisits($store->id);

        return $response;
    }

    /**
     * Returns 3 recent order with other meta data
     * to see all
     */
    public function getRecentOrders(Store $store, Request $request)
    {
        $query = $store->orders()->where('fulfilled', false)
                            ->where('payment_status', 'Paid');

        $data['orders'] = $query->with(['products', 'customer'])
                                ->latest()
                                ->limit(5)
                                ->get();

        $data['orders_count'] = $query->count();

        //max is 3. if more than 3, show link to see others
        if ($data['orders_count'] > 5) {
            $data['full_orders_link'] = "orders?sort=DESC&filter[fulfilled]=0&filter[paid]=true";
        }

        return $data;
    }

    public function getMostViewedProducts(Store $store, Request $request)
    {
        $query = $store->products()
                        ->whereBetween('updated_at', $this->dateQuery)
                        ->orderByDesc('views')->limit(5);

        $response['products'] = $query->get();
        $response['date'] = $this->carbon->startOfWeek()->format("M d") . " - " . $this->carbon->now()->format("M d");

        return $response;
    }
}
