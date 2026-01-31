<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Member\BookSearchController;
use App\Http\Controllers\Member\BookingController;
use App\Http\Controllers\Member\BorrowHistoryController;

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'member'])->name('dashboard');

// Book Search & Browse
Route::get('/books', [BookSearchController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookSearchController::class, 'show'])->name('books.show');
Route::post('/books/qr', [BookSearchController::class, 'getByQR'])->name('books.qr');
Route::get('/books-popular', [BookSearchController::class, 'popular'])->name('books.popular');
Route::get('/books-new', [BookSearchController::class, 'newArrivals'])->name('books.new');
Route::get('/category/{category}/books', [BookSearchController::class, 'byCategory'])->name('books.category');

// Bookings
Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
Route::get('/bookings-active-count', [BookingController::class, 'activeCount'])->name('bookings.active-count');
Route::get('/books/{book}/can-book', [BookingController::class, 'canBook'])->name('bookings.can-book');

// Borrow History
Route::get('/borrow-history', [BorrowHistoryController::class, 'index'])->name('borrow-history.index');
Route::get('/borrow-history/{borrowing}', [BorrowHistoryController::class, 'show'])->name('borrow-history.show');
Route::get('/borrow-history-overdue', [BorrowHistoryController::class, 'overdue'])->name('borrow-history.overdue');
Route::get('/borrow-history-active-count', [BorrowHistoryController::class, 'activeCount'])->name('borrow-history.active-count');

// Fines
Route::get('/fines', [BorrowHistoryController::class, 'fines'])->name('fines.index');

// Download Receipt
Route::get('/borrowing/{borrowing}/receipt', [BorrowHistoryController::class, 'downloadReceipt'])->name('borrowing.receipt');
