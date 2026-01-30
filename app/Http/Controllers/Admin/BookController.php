<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Services\BookService;
use App\Http\Requests\BookRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookController extends Controller
{
    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'category_id' => $request->query('category_id'),
            'available' => $request->query('available'),
            'sort_by' => $request->query('sort_by', 'title'),
            'sort_order' => $request->query('sort_order', 'asc'),
            'per_page' => $request->query('per_page', 15),
        ];

        $books = $this->bookService->getAllBooks($filters);
        $categories = Category::withBookCount()->get();

        return Inertia::render('Admin/Books/Index', [
            'books' => $books,
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return Inertia::render('Admin/Books/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookRequest $request)
    {
        try {
            $book = $this->bookService->createBook($request->validated());

            return redirect()
                ->route('admin.books.show', $book->id)
                ->with('success', 'Book created successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create book: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load(['category', 'borrowings.user', 'bookings.user']);

        return Inertia::render('Admin/Books/Show', [
            'book' => $book,
            'statistics' => [
                'total_borrowings' => $book->borrowings()->count(),
                'active_borrowings' => $book->borrowings()->active()->count(),
                'total_bookings' => $book->bookings()->count(),
                'active_bookings' => $book->bookings()->pending()->count(),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $categories = Category::orderBy('name')->get();

        return Inertia::render('Admin/Books/Edit', [
            'book' => $book->load('category'),
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookRequest $request, Book $book)
    {
        try {
            $this->bookService->updateBook($book, $request->validated());

            return redirect()
                ->route('admin.books.show', $book->id)
                ->with('success', 'Book updated successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update book: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        try {
            $this->bookService->deleteBook($book);

            return redirect()
                ->route('admin.books.index')
                ->with('success', 'Book deactivated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to deactivate book: ' . $e->getMessage());
        }
    }

    /**
     * Restore deactivated book
     */
    public function restore($id)
    {
        $book = Book::findOrFail($id);

        try {
            $this->bookService->restoreBook($book);

            return redirect()
                ->route('admin.books.show', $book->id)
                ->with('success', 'Book restored successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore book: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate QR Code for book
     */
    public function regenerateQR(Book $book)
    {
        try {
            $this->bookService->generateQRCode($book);

            return back()->with('success', 'QR Code regenerated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to regenerate QR Code: ' . $e->getMessage());
        }
    }
}
