<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StorePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Store  $store
     * @return mixed
     */
    public function view(User $user, Store $store)
    {
        return $user->id !== $store->owner->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     * 1) The user has to be the owner
     * 2) The user has to actually be a member of the store
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Store  $store
     * @return mixed
     */
    public function update(User $user, Store $store)
    {
        return $store->users
                ->where('pivot.role', 'owner')
                ->where('id', $user->id)
                ->count() > 0;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Store  $store
     * @return mixed
     */
    public function delete(User $user, Store $store)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Store  $store
     * @return mixed
     */
    public function restore(User $user, Store $store)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Store  $store
     * @return mixed
     */
    public function forceDelete(User $user, Store $store)
    {
        //
    }
}
