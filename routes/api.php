<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Get authenticated user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Quick stats for dashboard widgets
    Route::get('/stats/quick', function (Request $request) {
        $user = $request->user();

        if ($user->hasAdminAccess()) {
            return response()->json([
                'total_books' => \App\Models\Book::active()->count(),
                'total_members' => \App\Models\User::where('role', 'member')->where('is_active', true)->count(),
                'active_borrowings' => \App\Models\Borrowing::active()->count(),
                'overdue_borrowings' => \App\Models\Borrowing::overdue()->count(),
            ]);
        }

        return response()->json([
            'active_borrowings' => $user->activeBorrowings()->count(),
            'overdue_borrowings' => $user->borrowings()->overdue()->count(),
            'active_bookings' => $user->activeBookings()->count(),
            'total_unpaid_fines' => $user->getTotalUnpaidFines(),
        ]);
    });

    // Search books (autocomplete)
    Route::get('/books/search', function (Request $request) {
        $query = $request->get('q');

        $books = \App\Models\Book::active()
            ->search($query)
            ->limit(10)
            ->get(['id', 'title', 'author', 'isbn']);

        return response()->json($books);
    });

    // Search users (autocomplete)
    Route::middleware('role:super_admin,pustakawan')->get('/users/search', function (Request $request) {
        $query = $request->get('q');

        $users = \App\Models\User::where('role', 'member')
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'ILIKE', "%{$query}%")
                    ->orWhere('nim_nidn', 'ILIKE', "%{$query}%")
                    ->orWhere('email', 'ILIKE', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'nim_nidn', 'name', 'email', 'prodi']);

        return response()->json($users);
    });

    // Check book availability
    Route::get('/books/{book}/availability', function (\App\Models\Book $book) {
        return response()->json([
            'available' => $book->isAvailable(),
            'stock' => $book->stock,
            'available_stock' => $book->available_stock,
            'status' => $book->status,
        ]);
    });

    // Get notifications
    Route::get('/notifications', function (Request $request) {
        $user = $request->user();

        $notifications = [];

        // Overdue books notification
        $overdueCount = $user->borrowings()->overdue()->count();
        if ($overdueCount > 0) {
            $notifications[] = [
                'type' => 'overdue',
                'message' => "Anda memiliki {$overdueCount} buku yang terlambat dikembalikan",
                'severity' => 'error',
            ];
        }

        // Unpaid fines notification
        if ($user->hasUnpaidFines()) {
            $amount = $user->getTotalUnpaidFines();
            $notifications[] = [
                'type' => 'fine',
                'message' => "Anda memiliki denda yang belum dibayar sebesar Rp " . number_format($amount, 0, ',', '.'),
                'severity' => 'warning',
            ];
        }

        // Expiring bookings notification
        $expiringBookings = $user->bookings()
            ->where('status', 'pending')
            ->where('expiry_date', '<=', now()->addHours(2))
            ->where('expiry_date', '>', now())
            ->count();

        if ($expiringBookings > 0) {
            $notifications[] = [
                'type' => 'booking_expiring',
                'message' => "{$expiringBookings} booking akan segera kadaluarsa",
                'severity' => 'info',
            ];
        }

        return response()->json($notifications);
    });
});
