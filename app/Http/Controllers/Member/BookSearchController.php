<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Ditambahkan agar Auth::user() dikenali
use Inertia\Inertia;

class BookSearchController extends Controller
{
    /**
     * Show book search page
     */
    public function index(Request $request)
    {
        $query = Book::with('category')
            ->active()
            ->available();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'title');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $books = $query->paginate($request->per_page ?? 12);
        $categories = Category::withBookCount()->get();

        return Inertia::render('Member/Books/Search', [
            'books' => $books,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category_id', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Show book detail
     */
    public function show(Book $book)
    {
        if (!$book->is_active) {
            abort(404);
        }

        $book->load('category');

        // Check if current user can book/borrow this book
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $canBook = false;
        $canBookReasons = [];

        if ($user) {
            $bookingService = app(\App\Services\BookingService::class);
            $checkResult = $bookingService->canUserBookBook($user, $book);
            $canBook = $checkResult['can_book'];
            $canBookReasons = $checkResult['reasons'];
        }

        $canBorrow = false;
        $canBorrowReasons = [];

        if ($user) {
            $borrowingService = app(\App\Services\BorrowingService::class);
            $checkResult = $borrowingService->canUserBorrow($user);
            $canBorrow = $checkResult['can_borrow'];
            $canBorrowReasons = $checkResult['reasons'];
        }

        return Inertia::render('Member/Books/Show', [
            'book' => $book,
            'canBook' => $canBook,
            'canBookReasons' => $canBookReasons,
            'canBorrow' => $canBorrow,
            'canBorrowReasons' => $canBorrowReasons,
        ]);
    }

    /**
     * Get book by QR code
     */
    public function getByQR(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            $qrData = json_decode($request->qr_data, true);

            if (!isset($qrData['id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code data',
                ], 400);
            }

            $book = Book::with('category')->findOrFail($qrData['id']);

            if (!$book->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Book is not active',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'book' => $book,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Book not found',
            ], 404);
        }
    }

    /**
     * Get popular books
     */
    public function popular()
    {
        $books = Book::with('category')
            ->active()
            ->withCount('borrowings')
            ->orderBy('borrowings_count', 'desc')
            ->limit(12)
            ->get();

        return Inertia::render('Member/Books/Popular', [
            'books' => $books,
        ]);
    }

    /**
     * Get new arrivals
     */
    public function newArrivals()
    {
        $books = Book::with('category')
            ->active()
            ->latest()
            ->limit(12)
            ->get();

        return Inertia::render('Member/Books/NewArrivals', [
            'books' => $books,
        ]);
    }

    /**
     * Get books by category
     */
    public function byCategory(Category $category)
    {
        $books = Book::with('category')
            ->active()
            ->where('category_id', $category->id)
            ->orderBy('title')
            ->paginate(12);

        return Inertia::render('Member/Books/ByCategory', [
            'category' => $category,
            'books' => $books,
        ]);
    }
}
