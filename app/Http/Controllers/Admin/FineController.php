<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use App\Services\FineCalculationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FineController extends Controller
{
    protected $fineService;

    public function __construct(FineCalculationService $fineService)
    {
        $this->fineService = $fineService;
    }

    /**
     * Display a listing of fines
     */
    public function index(Request $request)
    {
        $query = Fine::with(['borrowing.user', 'borrowing.book']);

        // Filter by payment status
        if ($request->filled('is_paid')) {
            $isPaid = filter_var($request->is_paid, FILTER_VALIDATE_BOOLEAN);
            $query->where('is_paid', $isPaid);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->whereHas('borrowing', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }

        $fines = $query->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        $statistics = $this->fineService->getFineStatistics();

        return Inertia::render('Admin/Fines/Index', [
            'fines' => $fines,
            'statistics' => $statistics,
            'filters' => $request->only(['is_paid', 'user_id']),
        ]);
    }

    /**
     * Show fine detail
     */
    public function show(Fine $fine)
    {
        $fine->load(['borrowing.user', 'borrowing.book']);

        return Inertia::render('Admin/Fines/Show', [
            'fine' => $fine,
            'remainingAmount' => $fine->getRemainingAmount(),
        ]);
    }

    /**
     * Record payment
     */
    public function recordPayment(Request $request, Fine $fine)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        try {
            $this->fineService->recordPayment($fine, $request->amount);

            return redirect()
                ->route('admin.fines.show', $fine->id)
                ->with('success', 'Payment recorded successfully');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get unpaid fines
     */
    public function unpaid()
    {
        $fines = Fine::unpaid()
            ->with(['borrowing.user', 'borrowing.book'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Admin/Fines/Unpaid', [
            'fines' => $fines,
            'totalUnpaid' => $this->fineService->getTotalUnpaidFines(),
        ]);
    }

    /**
     * Calculate all overdue fines
     */
    public function calculateAll()
    {
        try {
            $count = $this->fineService->calculateAllOverdueFines();

            return back()->with('success', "Created {$count} new fines for overdue borrowings");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to calculate fines: ' . $e->getMessage());
        }
    }

    /**
     * Update all fines
     */
    public function updateAll()
    {
        try {
            $count = $this->fineService->updateAllFines();

            return back()->with('success', "Updated {$count} unpaid fines");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update fines: ' . $e->getMessage());
        }
    }
}
