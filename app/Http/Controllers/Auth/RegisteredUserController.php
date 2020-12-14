<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use App\Repositories\StoreRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

/**
 * This page manages registration for new users
 */
class RegisteredUserController extends Controller
{

    protected $storeModel;

    public function __construct(StoreRepository $store)
    {
        $this->storeModel = $store;    
    }
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function stepOne(Request $request)
    {
        $request->validate([
            'store' => 'required|string|max:100|alpha_dash|unique:stores,shortname',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        //Initialize Store
        $store = $this->storeModel->initialize($request->store);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        //attach the user to the store as the owner
        $store->users()->save($user, ['role' => 'owner']);

        event(new Registered($user));

        return response()->json([
            "status" => "Success",
            "message" => "Registration successful",
            "user" => $user,
            "storeName" => $store->shortname,
            "storeId" => $store->id
        ], 201);
    }

    public function stepTwo(Request $request){
        $request->validate([
            'size' => 'required|string',
            'category' => 'required|string',
            'industry' => 'required|string',
            'storeId' => 'required|integer'
        ]);

        Store::whereId($request->storeId)
                ->update([
                    "size" => $request->size,
                    "category" => $request->category,
                    "industry" => $request->industry
                ]);

        return Store::find($request->storeId);
    }

    public function createStore(Request $request){
        //step of the registration
        $step = $request->query('step');
        
        if($step == 1) return $this->stepOne($request);
        if($step == 2) return $this->stepTwo($request);
    }
}
