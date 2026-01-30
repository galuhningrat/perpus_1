<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Booking;
use App\Models\Fine;

class LibraryStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:stats
                            {--detailed : Show detailed statistics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display library statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('📚 LIBRARY STATISTICS - STTI CIREBON');
        $this->info('=' . str_repeat('=', 50));
        $this->newLine();

        // Books Statistics
        $this->displayBookStats();
        $this->newLine();

        // Users Statistics
        $this->displayUserStats();
        $this->newLine();

        // Borrowings Statistics
        $this->displayBorrowingStats();
        $this->newLine();

        // Bookings Statistics
        $this->displayBookingStats();
        $this->newLine();

        // Fines Statistics
        $this->displayFineStats();
        $this->newLine();

        if ($this->option('detailed')) {
            $this->displayDetailedStats();
        }

        return Command::SUCCESS;
    }

    protected function displayBookStats()
    {
        $totalBooks = Book::count();
        $activeBooks = Book::where('is_active', true)->count();
        $totalStock = Book::sum('stock');
        $availableStock = Book::sum('available_stock');
        $borrowedStock = $totalStock - $availableStock;

        $this->info('📖 BOOKS:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Books', $totalBooks],
                ['Active Books', $activeBooks],
                ['Total Stock', $totalStock],
                ['Available', $availableStock],
                ['Currently Borrowed', $borrowedStock],
                ['Availability Rate', number_format(($availableStock / max($totalStock, 1)) * 100, 1) . '%'],
            ]
        );
    }

    protected function displayUserStats()
    {
        $totalUsers = User::where('role', 'member')->count();
        $activeUsers = User::where('role', 'member')->where('is_active', true)->count();
        $byProdi = User::where('role', 'member')
            ->selectRaw('prodi, count(*) as count')
            ->groupBy('prodi')
            ->pluck('count', 'prodi');

        $this->info('👥 MEMBERS:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Members', $totalUsers],
                ['Active Members', $activeUsers],
                ['Informatika', $byProdi['Informatika'] ?? 0],
                ['Elektro', $byProdi['Elektro'] ?? 0],
                ['Mesin', $byProdi['Mesin'] ?? 0],
            ]
        );
    }

    protected function displayBorrowingStats()
    {
        $totalBorrowings = Borrowing::count();
        $activeBorrowings = Borrowing::whereIn('status', ['borrowed', 'overdue'])->count();
        $overdueBorrowings = Borrowing::where('status', 'overdue')->count();
        $returnedBorrowings = Borrowing::where('status', 'returned')->count();

        $this->info('📋 BORROWINGS:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Borrowings', $totalBorrowings],
                ['Active Borrowings', $activeBorrowings],
                ['Overdue', $overdueBorrowings],
                ['Returned', $returnedBorrowings],
                ['Return Rate', number_format(($returnedBorrowings / max($totalBorrowings, 1)) * 100, 1) . '%'],
            ]
        );
    }

    protected function displayBookingStats()
    {
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $expiredBookings = Booking::where('status', 'expired')->count();

        $this->info('📅 BOOKINGS:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Bookings', $totalBookings],
                ['Pending', $pendingBookings],
                ['Confirmed', $confirmedBookings],
                ['Expired', $expiredBookings],
            ]
        );
    }

    protected function displayFineStats()
    {
        $totalFines = Fine::count();
        $unpaidFines = Fine::where('is_paid', false)->count();
        $totalUnpaidAmount = Fine::where('is_paid', false)->sum(\DB::raw('total_fine - paid_amount'));
        $totalPaidAmount = Fine::sum('paid_amount');

        $this->info('💰 FINES:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Fines', $totalFines],
                ['Unpaid Fines', $unpaidFines],
                ['Total Unpaid Amount', 'Rp ' . number_format($totalUnpaidAmount, 0, ',', '.')],
                ['Total Paid Amount', 'Rp ' . number_format($totalPaidAmount, 0, ',', '.')],
            ]
        );
    }

    protected function displayDetailedStats()
    {
        $this->info('📊 DETAILED STATISTICS:');
        $this->newLine();

        // Most borrowed books
        $mostBorrowed = Book::withCount('borrowings')
            ->orderBy('borrowings_count', 'desc')
            ->limit(5)
            ->get();

        $this->info('🔥 Top 5 Most Borrowed Books:');
        $this->table(
            ['Title', 'Times Borrowed'],
            $mostBorrowed->map(function ($book) {
                return [$book->title, $book->borrowings_count];
            })
        );
        $this->newLine();

        // Most active members
        $mostActive = User::where('role', 'member')
            ->withCount('borrowings')
            ->orderBy('borrowings_count', 'desc')
            ->limit(5)
            ->get();

        $this->info('⭐ Top 5 Most Active Members:');
        $this->table(
            ['Name', 'Total Borrowings'],
            $mostActive->map(function ($user) {
                return [$user->name, $user->borrowings_count];
            })
        );
    }
}
