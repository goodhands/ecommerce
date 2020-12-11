<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Paystack;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        //First charge the user to get the authorization details to store in the DB
        $data = Paystack::getAuthorizationResponse($request->all());

        return $data;
    }
}
