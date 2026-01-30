<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view books list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Book $book): bool
    {
        return true; // All authenticated users can view book details
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Book $book): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Book $book): bool
    {
        // Only admin can delete, and only if book has no active borrowings
        if (!$user->hasAdminAccess()) {
            return false;
        }

        $hasActiveBorrowings = $book->borrowings()
            ->whereIn('status', ['borrowed', 'overdue'])
            ->exists();

        return !$hasActiveBorrowings;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Book $book): bool
    {
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Book $book): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can book the book.
     */
    public function book(User $user, Book $book): bool
    {
        // Only members can book
        if (!$user->isMember()) {
            return false;
        }

        // Check if book is available
        if (!$book->isAvailable()) {
            return false;
        }

        // Check if user already has active booking for this book
        $hasActiveBooking = \App\Models\Booking::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasActiveBooking) {
            return false;
        }

        // Check if user has reached booking limit
        $activeBookingsCount = \App\Models\Booking::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        if ($activeBookingsCount >= 3) {
            return false;
        }

        // Check if user has overdue books or unpaid fines
        if ($user->hasOverdueBooks() || $user->hasUnpaidFines()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can borrow the book.
     */
    public function borrow(User $user, Book $book): bool
    {
        // Only admin can create borrowing records
        if (!$user->hasAdminAccess()) {
            return false;
        }

        // Check if book is available
        if (!$book->isAvailable()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can regenerate QR code.
     */
    public function regenerateQR(User $user, Book $book): bool
    {
        return $user->hasAdminAccess();
    }
}
