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
    }
    /**
     * Get stats on sales, customers and visits for the week
     */
    public function getWeeklyStats(Store $store, Request $request){
        //sales involves where payment was confirmed
        $salesQuery = $store->orders()->where('payment_status', 'Paid');

        $response['sales_total'] = $salesQuery->pluck('total')->sum(); 
        $response['sales_link'] = 'orders?fulfiled=1&sort=Desc&from=last week&to=today';

        $response['new_orders'] = $store->orders()
                                    ->wherePaymentStatus('Paid')
                                    ->whereFulfilled(false)
                                    ->whereDate('created_at', $this->carbon->subWeek())->count();

        //last 7 days
        $response['customers'] = $store->customers()->whereDate('created_at', $this->carbon->subWeek())->count();

        $response['customers_link'] = 'customers?sort=Desc&from=last week&to=today';

        //TODO:set up google analytics

        return collect($response)->toArray();
    }
}
