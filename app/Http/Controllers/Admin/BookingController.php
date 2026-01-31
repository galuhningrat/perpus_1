<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Book;
use App\Models\User;
use App\Services\BookingService;
use App\Http\Requests\BookingRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display a listing of bookings (Admin view)
     */
    public function index(Request $request)
    {
        $filters = [
            'status' => $request->query('status'),
            'user_id' => $request->query('user_id'),
            'book_id' => $request->query('book_id'),
            'per_page' => $request->query('per_page', 15),
        ];

        $bookings = $this->bookingService->getAllBookings($filters);

        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => $bookings,
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'book.category']);

        return Inertia::render('Admin/Bookings/Show', [
            'booking' => $booking,
            'canConfirm' => $booking->isValid(),
            'canCancel' => $booking->status === 'pending',
        ]);
    }

    /**
     * Confirm booking (convert to borrowing)
     */
    public function confirm(Booking $booking)
    {
        try {
            $borrowing = $this->bookingService->confirmBooking($booking);

            return redirect()
                ->route('admin.borrowings.show', $borrowing->id)
                ->with('success', 'Booking berhasil dikonfirmasi dan dikonversi menjadi peminjaman');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel booking
     */
    public function cancel(Booking $booking)
    {
        try {
            $this->bookingService->cancelBooking($booking);

            return redirect()
                ->route('admin.bookings.index')
                ->with('success', 'Booking berhasil dibatalkan');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get pending bookings
     */
    public function pending()
    {
        $bookings = Booking::pending()
            ->with(['user', 'book.category'])
            ->orderBy('expiry_date', 'asc')
            ->paginate(15);

        return Inertia::render('Admin/Bookings/Pending', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * Get expired bookings
     */
    public function expired()
    {
        $bookings = Booking::where('status', 'expired')
            ->with(['user', 'book.category'])
            ->orderBy('expiry_date', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/Bookings/Expired', [
            'bookings' => $bookings,
        ]);
    }
}
