<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\StoreRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

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
    public function store(Request $request)
    {
        try{

            $request->validate([
                'store' => 'required|string|max:100|alpha_dash|unique:stores,shortname',
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|confirmed|min:8',
            ]);

            //Initialize Store
            $storeName = $this->storeModel->initialize($request->store);

            Auth::login($user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]));

            //attach the user to the store as the owner
            $storeName->users()->save($user, ['role' => 'owner']);

        }catch(ValidationException $e){
            return response()->json($e->errors(), 422);
        }

        event(new Registered($user));

        return response()->json([
            "status" => "Success",
            "message" => "Registration successful",
            "user" => $user,
            "storeName" => $storeName
        ], 201);
    }
}
