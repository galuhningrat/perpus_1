<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Book;
use App\Services\BookingService;
use App\Http\Requests\BookingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan ini
use Inertia\Inertia;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $activeBookings = $this->bookingService->getUserActiveBookings($user);
        $bookingHistory = $this->bookingService->getUserBookingHistory($user);

        return Inertia::render('Member/Bookings/Index', [
            'activeBookings' => $activeBookings,
            'bookingHistory' => $bookingHistory,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookingRequest $request)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $book = Book::findOrFail($request->book_id);

            $booking = $this->bookingService->createBooking(
                $user,
                $book,
                $request->notes
            );

            return redirect()
                ->route('member.bookings.show', $booking->id)
                ->with('success', 'Book booked successfully. Please pick it up within 24 hours.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        // Ensure user can only see their own bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load('book.category');

        return Inertia::render('Member/Bookings/Show', [
            'booking' => $booking,
            'canCancel' => $booking->status === 'pending' && !$booking->isExpired(),
        ]);
    }

    /**
     * Cancel booking
     */
    public function cancel(Booking $booking)
    {
        // Ensure user can only cancel their own bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $this->bookingService->cancelBooking($booking);

            return redirect()
                ->route('member.bookings.index')
                ->with('success', 'Booking cancelled successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get active bookings count
     */
    public function activeCount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $count = Booking::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        return response()->json([
            'count' => $count,
        ]);
    }

    /**
     * Check if user can book a specific book
     */
    public function canBook(Book $book)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $result = $this->bookingService->canUserBookBook($user, $book);

        return response()->json($result);
    }
}
