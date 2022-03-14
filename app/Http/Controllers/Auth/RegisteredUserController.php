<?php

namespace App\Http\Controllers\Auth;

use App\Events\StoreCreated;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\StoreRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
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

        // Create a token to be used for auth
        $token = $user->createToken('auth_token');

        //attach the user to the store as the owner
        $store->users()->save($user, ['role' => 'owner']);

        event(new Registered($user));

        return response()->json([
            "status" => "Success",
            "message" => "Registration successful",
            "user" => $user,
            "storeName" => $store->shortname,
            "storeId" => $store->id,
            "token" => $token->plainTextToken
        ], 201);
    }

    public function stepTwo(Request $request)
    {
        $request->validate([
            'size' => 'required|string',
            'category' => 'required|string',
            'industry' => 'required|string',
            'storeId' => 'required|integer'
        ]);

        $store = $this->storeModel
                    ->updateStore($request->except(['storeId', 'step']), $request->storeId, false);

        //emit event for newly created store
        event(new StoreCreated($store));

        return $store;
    }

    public function createStore(Request $request)
    {
        //step of the registration
        $step = $request->query('step');

        if ($step == 1) {
            return $this->stepOne($request);
        }

        if ($step == 2) {
            return $this->stepTwo($request);
        }
    }
}
