<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Book;
use App\Models\User;
use App\Services\BorrowingService;
use App\Http\Requests\BorrowingRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BorrowingController extends Controller
{
    protected $borrowingService;

    public function __construct(BorrowingService $borrowingService)
    {
        $this->borrowingService = $borrowingService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = [
            'status' => $request->query('status'),
            'user_id' => $request->query('user_id'),
            'book_id' => $request->query('book_id'),
            'overdue' => $request->query('overdue'),
            'per_page' => $request->query('per_page', 15),
        ];

        $borrowings = $this->borrowingService->getAllBorrowings($filters);

        return Inertia::render('Admin/Borrowings/Index', [
            'borrowings' => $borrowings,
            'filters' => $filters,
            'statistics' => $this->borrowingService->getOverdueStatistics(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $members = User::where('role', 'member')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $books = Book::active()
            ->available()
            ->with('category')
            ->orderBy('title')
            ->get();

        return Inertia::render('Admin/Borrowings/Create', [
            'members' => $members,
            'books' => $books,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BorrowingRequest $request)
    {
        try {
            $user = User::findOrFail($request->user_id);
            $book = Book::findOrFail($request->book_id);

            $borrowing = $this->borrowingService->createBorrowing(
                $user,
                $book,
                $request->notes
            );

            return redirect()
                ->route('admin.borrowings.show', $borrowing->id)
                ->with('success', 'Borrowing created successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create borrowing: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['user', 'book.category', 'fine']);

        return Inertia::render('Admin/Borrowings/Show', [
            'borrowing' => $borrowing,
            'can_extend' => $borrowing->status === 'borrowed' && !$borrowing->isOverdue(),
            'can_return' => in_array($borrowing->status, ['borrowed', 'overdue']),
        ]);
    }

    /**
     * Return book
     */
    public function return(Borrowing $borrowing)
    {
        try {
            $this->borrowingService->returnBook($borrowing);

            return redirect()
                ->route('admin.borrowings.show', $borrowing->id)
                ->with('success', 'Book returned successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to return book: ' . $e->getMessage());
        }
    }

    /**
     * Extend borrowing period
     */
    public function extend(Request $request, Borrowing $borrowing)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:14',
        ]);

        try {
            $this->borrowingService->extendBorrowing($borrowing, $request->days);

            return redirect()
                ->route('admin.borrowings.show', $borrowing->id)
                ->with('success', "Borrowing extended by {$request->days} days");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to extend borrowing: ' . $e->getMessage());
        }
    }

    /**
     * Show overdue borrowings
     */
    public function overdue()
    {
        $borrowings = Borrowing::overdue()
            ->with(['user', 'book.category', 'fine'])
            ->orderBy('due_date', 'asc')
            ->paginate(15);

        return Inertia::render('Admin/Borrowings/Overdue', [
            'borrowings' => $borrowings,
            'statistics' => $this->borrowingService->getOverdueStatistics(),
        ]);
    }
}
