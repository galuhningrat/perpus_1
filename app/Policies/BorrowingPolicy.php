<?php

namespace App\Policies;

use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BorrowingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Borrowing $borrowing): bool
    {
        // Admin can view all borrowings
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Users can only view their own borrowings
        return $borrowing->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin (pustakawan) can create borrowing records
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Borrowing $borrowing): bool
    {
        // Only admin can update borrowing records
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Borrowing $borrowing): bool
    {
        // Only super admin can delete borrowings
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Borrowing $borrowing): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Borrowing $borrowing): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can return the book.
     */
    public function return(User $user, Borrowing $borrowing): bool
    {
        // Only admin can process book returns
        if (!$user->hasAdminAccess()) {
            return false;
        }

        // Can only return active borrowings
        return in_array($borrowing->status, ['borrowed', 'overdue']);
    }

    /**
     * Determine whether the user can extend the borrowing.
     */
    public function extend(User $user, Borrowing $borrowing): bool
    {
        // Only admin can extend borrowings
        if (!$user->hasAdminAccess()) {
            return false;
        }

        // Can only extend active borrowings that are not overdue
        return $borrowing->status === 'borrowed' && !$borrowing->isOverdue();
    }

    /**
     * Determine whether the user can view borrowing receipt.
     */
    public function viewReceipt(User $user, Borrowing $borrowing): bool
    {
        // Admin can view all receipts
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Users can only view their own receipts
        return $borrowing->user_id === $user->id;
    }
}
