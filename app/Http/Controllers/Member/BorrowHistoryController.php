<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Fine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk benerin user/id
use Barryvdh\DomPDF\Facade\Pdf; // Untuk benerin PDF yang merah
use Inertia\Inertia;

class BorrowHistoryController extends Controller
{
    /**
     * Show borrowing history
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $activeBorrowings = $user->borrowings()
            ->active()
            ->with('book.category')
            ->orderBy('due_date', 'asc')
            ->get();

        $borrowingHistory = $user->borrowings()
            ->where('status', 'returned')
            ->with(['book.category', 'fine'])
            ->orderBy('return_date', 'desc')
            ->paginate(10);

        return Inertia::render('Member/BorrowHistory/Index', [
            'activeBorrowings' => $activeBorrowings,
            'borrowingHistory' => $borrowingHistory,
        ]);
    }

    /**
     * Show borrowing detail
     */
    public function show(Borrowing $borrowing)
    {
        // Ensure user can only see their own borrowings
        if ($borrowing->user_id !== Auth::id()) {
            abort(403);
        }

        $borrowing->load(['book.category', 'fine']);

        return Inertia::render('Member/BorrowHistory/Show', [
            'borrowing' => $borrowing,
            'isOverdue' => $borrowing->isOverdue(),
            'daysOverdue' => $borrowing->getDaysOverdue(),
        ]);
    }

    /**
     * Show overdue borrowings
     */
    public function overdue()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $overdueBorrowings = $user->borrowings()
            ->overdue()
            ->with(['book.category', 'fine'])
            ->orderBy('due_date', 'asc')
            ->get();

        return Inertia::render('Member/BorrowHistory/Overdue', [
            'overdueBorrowings' => $overdueBorrowings,
            'totalFines' => $user->getTotalUnpaidFines(),
        ]);
    }

    /**
     * Show fines
     */
    public function fines()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $unpaidFines = Fine::unpaid()
            ->whereHas('borrowing', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('borrowing.book')
            ->get();

        $paidFines = Fine::paid()
            ->whereHas('borrowing', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('borrowing.book')
            ->orderBy('payment_date', 'desc')
            ->paginate(10);

        return Inertia::render('Member/BorrowHistory/Fines', [
            'unpaidFines' => $unpaidFines,
            'paidFines' => $paidFines,
            'totalUnpaid' => $user->getTotalUnpaidFines(),
        ]);
    }

    /**
     * Download borrowing receipt
     */
    public function downloadReceipt(Borrowing $borrowing)
    {
        // Ensure user can only download their own receipt
        if ($borrowing->user_id !== Auth::id()) {
            abort(403);
        }

        // Gunakan Pdf facade yang sudah di-import di atas
        $pdf = Pdf::loadView('receipts.borrowing', [
            'borrowing' => $borrowing->load(['book', 'user']),
        ]);

        return $pdf->download("borrowing_receipt_{$borrowing->id}.pdf");
    }

    /**
     * Get active borrowings count
     */
    public function activeCount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $count = $user->activeBorrowings()->count();

        return response()->json([
            'count' => $count,
        ]);
    }
}
