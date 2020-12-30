<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(Carbon $carbon)
    {
        $this->carbon = $carbon;

        //stats for this current week
        $this->dateQuery = [$this->carbon->startOfWeek(), $this->carbon->now()];
    }
    /**
     * Get stats on sales, customers and visits for the last week
     */
    public function getWeeklyStats(Store $store, Request $request){
        //sales involves where payment was confirmed
        $salesQuery = $store->orders()->where('payment_status', 'Paid')
                                ->whereBetween('created_at', $this->dateQuery);

        $response['sales_total'] = $salesQuery->pluck('total')->sum(); 
        $response['sales_link'] = 'orders?fulfilled=1&sort=Desc&from=last week&to=today';

        $response['new_orders'] = $store->orders()
                                    ->wherePaymentStatus('Paid')
                                    ->whereFulfilled(false)
                                    ->whereBetween('created_at', $this->dateQuery)->count();

        //last 7 days
        $response['customers'] = $store->customers()->whereBetween('created_at', $this->dateQuery)->count();

        $response['customers_link'] = 'customers?sort=Desc&from=last week&to=today';

        //TODO:set up google analytics

        return $response;
    }

    /**
     * Returns 3 recent order with other meta data 
     * to see all
     */
    public function getRecentOrders(Store $store, Request $request){
        $query = $store->orders()->where('fulfilled', false)
                            ->where('payment_status', 'Paid');
        $data['orders'] = $query->with(['products', 'customer'])
                                ->latest()    
                                ->get();
        $data['orders_count'] = $query->count();

        //max is 3. if more than 3, show link to see others
        if($data['orders_count'] > 3){
            $data['full_orders_link'] = "/orders?sort=DESC&fulfilled=0&paid=true";
        }

        return $data;
    }

    public function getMostViewedProducts(Store $store, Request $request){
        $query = $store->products()
                        ->whereBetween('created_at', $this->dateQuery)
                        ->orderByDesc('views')->limit(5);

        $response['products'] = $query->get();
        $response['date'] = $this->carbon->startOfWeek()->format("M d"). " - " .$this->carbon->now()->format("d");

        return $response;
    }
}
