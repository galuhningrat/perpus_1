<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Booking;
use App\Models\User;
use App\Models\Fine;

class DashboardController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function admin()
    {
        $statistics = [
            'total_books' => Book::active()->count(),
            'total_members' => User::where('role', 'member')->where('is_active', true)->count(),
            'active_borrowings' => Borrowing::active()->count(),
            'overdue_borrowings' => Borrowing::overdue()->count(),
            'pending_bookings' => Booking::pending()->count(),
            'unpaid_fines' => Fine::unpaid()->count(),
            'total_unpaid_amount' => Fine::unpaid()->sum(DB::raw('total_fine - paid_amount')),
        ];

        // Recent activities
        $recentBorrowings = Borrowing::with(['user', 'book'])
            ->latest()
            ->limit(10)
            ->get();

        $recentBookings = Booking::with(['user', 'book'])
            ->latest()
            ->limit(10)
            ->get();

        // Most borrowed books
        $popularBooks = Book::withCount('borrowings')
            ->orderBy('borrowings_count', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'statistics' => $statistics,
            'recentBorrowings' => $recentBorrowings,
            'recentBookings' => $recentBookings,
            'popularBooks' => $popularBooks,
        ]);
    }

    /**
     * Member Dashboard
     */
    public function member()
    {
        $user = Auth::user();

        $statistics = [
            'active_borrowings' => $user->activeBorrowings()->count(),
            'overdue_borrowings' => $user->borrowings()->overdue()->count(),
            'active_bookings' => $user->activeBookings()->count(),
            'total_unpaid_fines' => $user->getTotalUnpaidFines(),
        ];

        // User's active borrowings
        $borrowings = $user->borrowings()
            ->active()
            ->with('book.category')
            ->get();

        // User's active bookings
        $bookings = $user->bookings()
            ->pending()
            ->with('book.category')
            ->get();

        // User's unpaid fines
        $fines = Fine::unpaid()
            ->whereHas('borrowing', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('borrowing.book')
            ->get();

        // Recommended books
        $recommendedBooks = Book::active()
            ->available()
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return Inertia::render('Member/Dashboard', [
            'statistics' => $statistics,
            'borrowings' => $borrowings,
            'bookings' => $bookings,
            'fines' => $fines,
            'recommendedBooks' => $recommendedBooks,
        ]);
    }
}
