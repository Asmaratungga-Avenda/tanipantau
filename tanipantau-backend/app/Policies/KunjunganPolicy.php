<?php

namespace App\Policies;

use App\Models\Kunjungan;
use App\Models\User;

class KunjunganPolicy
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
    public function view(User $user, Kunjungan $kunjungan): bool
    {
        if ($user->role === 'admin' || $user->role === 'manajer') {
            return true;
        }
        
        if ($user->role === 'petugas') {
            return $kunjungan->petugas_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'petugas']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Kunjungan $kunjungan): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'petugas') {
            return $kunjungan->petugas_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Kunjungan $kunjungan): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'petugas') {
            return $kunjungan->petugas_id === $user->id;
        }
        
        return false;
    }
}
