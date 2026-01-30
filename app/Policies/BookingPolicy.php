<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view their bookings
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking): bool
    {
        // Admin can view all bookings
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Users can only view their own bookings
        return $booking->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only members can create bookings
        if (!$user->isMember()) {
            return false;
        }

        // Check if user has overdue books or unpaid fines
        if ($user->hasOverdueBooks() || $user->hasUnpaidFines()) {
            return false;
        }

        // Check booking limit
        $activeBookingsCount = \App\Models\Booking::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        return $activeBookingsCount < 3;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking): bool
    {
        // Only admin can update booking status
        return $user->hasAdminAccess();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Booking $booking): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Booking $booking): bool
    {
        return $user->isSuperAdmin();
    }

    /**
     * Determine whether the user can cancel the booking.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        // Admin can cancel any booking
        if ($user->hasAdminAccess()) {
            return true;
        }

        // Users can only cancel their own pending bookings
        return $booking->user_id === $user->id
            && $booking->status === 'pending'
            && !$booking->isExpired();
    }

    /**
     * Determine whether the user can confirm the booking (convert to borrowing).
     */
    public function confirm(User $user, Booking $booking): bool
    {
        // Only admin can confirm bookings
        if (!$user->hasAdminAccess()) {
            return false;
        }

        // Can only confirm valid pending bookings
        return $booking->isValid();
    }
}
