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
            'store_name' => 'sometimes|string|max:500',
            'store' => 'required|string|max:150|alpha_dash|unique:stores,shortname',
            // Custom URLs will be a paid feature: if empty, we will generate it
            'url' => 'sometimes|string|max:300|alpha_dash|unique:stores,url',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        //Initialize Store | Frontend should send a full URL like https://fairydaisy.myduxstore.com
        $store = $this->storeModel->initialize($request->only(['url', 'store', 'store_name']));

        $user = User::firstOrCreate([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create a token to be used for auth.
        // Token abilities may be granted based on the user's plan or role in a store
        $token = $user->createToken('auth_token', ['']);

        //attach the user to the store as the owner
        $store->users()->save($user, [
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'role' => 'owner'
        ]);

        // Google analytics for this store
        $measurement_id = $this->storeModel->createGAProperty($store);

        event(new Registered($user));

        return response()->json([
            "status" => "Success",
            "message" => "Registration successful",
            "user" => $user,
            "storename" => $store->name,
            "shortname" => $store->shortname,
            "url" => $store->url,
            "storeId" => $store->id,
            "measurement_id" => $measurement_id,
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

        $store = $this
            ->storeModel
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
