<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeliveryMethodsController extends Controller
{
    public function __construct()
    {
        return config('providers.delivery');
    }
}
