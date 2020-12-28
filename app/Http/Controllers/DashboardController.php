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

        $response['total'] = $salesQuery->pluck('total')->sum(); 
        
        $response['customers'] = $store->customers()->whereDate('created_at', $this->carbon->subWeek())->paginate(20);

        return $response;
    }
}
