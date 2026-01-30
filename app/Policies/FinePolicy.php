<?php

namespace App\Policies;

use App\Models\Fine;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FinePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All users can view fines (filtered by ownership)
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Fine $fine): bool
    {
        // Admin can view all fines
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Users can only view their own fines
        return $fine->borrowing->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Fines are created automatically by the system
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Fine $fine): bool
    {
        // Only admin can update fines
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Fine $fine): bool
    {
        // Only super admin can delete fines
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Fine $fine): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Fine $fine): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can record payment for the fine.
     */
    public function recordPayment(User $user, Fine $fine): bool
    {
        // Only admin can record payments
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can view payment history.
     */
    public function viewPaymentHistory(User $user, Fine $fine): bool
    {
        // Admin can view all payment history
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Users can view their own payment history
        return $fine->borrowing->user_id === $user->id;
    }
}
