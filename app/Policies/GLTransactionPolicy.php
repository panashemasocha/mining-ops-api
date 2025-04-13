<?php

namespace App\Policies;

use App\Models\GLTransaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GLTransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any transactions.
     */
    public function viewAny(User $user): bool
    {
        return $user->department?->name === 'Finance';
    }

    /**
     * Determine whether the user can view a specific transaction.
     */
    public function view(User $user, GLTransaction $transaction): bool
    {
        return $user->department?->name === 'Finance';
    }

    /**
     * Determine whether the user can create transactions.
     */
    public function create(User $user): bool
    {
        return $user->department?->name === 'Finance';
    }

    /**
     * Determine whether the user can update transactions.
     */
    public function update(User $user, GLTransaction $transaction): bool
    {
        return $user->department?->name === 'Finance';
    }

    /**
     * Determine whether the user can delete transactions.
     */
    public function delete(User $user, GLTransaction $transaction): bool
    {
        return $user->department?->name === 'Finance';
    }

    /**
     * Determine whether the user can restore transactions.
     */
    public function restore(User $user, GLTransaction $transaction): bool
    {
        return $user->department?->name === 'Finance';
    }

    /**
     * Determine whether the user can permanently delete transactions.
     */
    public function forceDelete(User $user, GLTransaction $transaction): bool
    {
        return $user->department?->name === 'Finance';
    }
}
