<?php

namespace App\Policies;

use App\Models\Petani;
use App\Models\User;

class PetaniPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'petugas', 'manajer']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Petani $petani): bool
    {
        if ($user->role === 'admin' || $user->role === 'manajer') {
            return true;
        }
        
        if ($user->role === 'petugas') {
            return $petani->lahans()->where('petugas_id', $user->id)->exists();
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Petani $petani): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Petani $petani): bool
    {
        return $user->role === 'admin';
    }
}
