<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FineCalculationService;
use App\Services\BorrowingService;
use App\Services\BookingService;

class CalculateOverdueFines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fines:calculate-overdue
                            {--update : Update existing fines instead of creating new ones}
                            {--verbose : Show detailed output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and create/update fines for overdue borrowings';

    protected $fineService;
    protected $borrowingService;
    protected $bookingService;

    public function __construct(
        FineCalculationService $fineService,
        BorrowingService $borrowingService,
        BookingService $bookingService
    ) {
        parent::__construct();
        $this->fineService = $fineService;
        $this->borrowingService = $borrowingService;
        $this->bookingService = $bookingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Starting overdue fines calculation...');
        $this->newLine();

        // Step 1: Update borrowing statuses
        $this->info('📋 Updating overdue borrowing statuses...');
        $updatedBorrowings = $this->borrowingService->updateOverdueBorrowings();
        $this->info("✅ Updated {$updatedBorrowings} borrowings to overdue status");
        $this->newLine();

        // Step 2: Calculate and create/update fines
        if ($this->option('update')) {
            $this->info('💰 Updating existing fines...');
            $updatedFines = $this->fineService->updateAllFines();
            $this->info("✅ Updated {$updatedFines} fines");
        } else {
            $this->info('💰 Creating new fines for overdue borrowings...');
            $createdFines = $this->fineService->calculateAllOverdueFines();
            $this->info("✅ Created {$createdFines} new fines");
        }
        $this->newLine();

        // Step 3: Mark expired bookings
        $this->info('📅 Marking expired bookings...');
        $expiredBookings = $this->bookingService->markExpiredBookings();
        $this->info("✅ Marked {$expiredBookings} bookings as expired");
        $this->newLine();

        // Show statistics if verbose
        if ($this->option('verbose')) {
            $this->showStatistics();
        }

        $this->info('✨ Overdue fines calculation completed!');
        return Command::SUCCESS;
    }

    /**
     * Show detailed statistics
     */
    protected function showStatistics()
    {
        $this->info('📊 Statistics:');
        $this->newLine();

        // Fine statistics
        $fineStats = $this->fineService->getFineStatistics();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Fines', $fineStats['total_fines']],
                ['Unpaid Fines', $fineStats['unpaid_fines']],
                ['Paid Fines', $fineStats['paid_fines']],
                ['Total Unpaid Amount', 'Rp ' . number_format($fineStats['total_unpaid_amount'], 0, ',', '.')],
                ['Total Paid Amount', 'Rp ' . number_format($fineStats['total_paid_amount'], 0, ',', '.')],
                ['Total Fine Amount', 'Rp ' . number_format($fineStats['total_fine_amount'], 0, ',', '.')],
            ]
        );
        $this->newLine();

        // Overdue borrowing statistics
        $overdueStats = $this->borrowingService->getOverdueStatistics();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Overdue Borrowings', $overdueStats['total_overdue']],
                ['Total Fine Amount', 'Rp ' . number_format($overdueStats['total_fine_amount'], 0, ',', '.')],
                ['Affected Users', $overdueStats['unique_users']],
            ]
        );
        $this->newLine();
    }
}