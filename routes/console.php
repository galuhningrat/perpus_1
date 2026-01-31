<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Test commands
Artisan::command('test:services', function () {
    $this->info('Testing Library Services...');

    // Test BookService
    $this->info('📚 Testing BookService...');
    $bookService = app(\App\Services\BookService::class);
    $books = $bookService->getAvailableBooks();
    $this->info("Available books: {$books->count()}");

    // Test BorrowingService
    $this->info('📋 Testing BorrowingService...');
    $borrowingService = app(\App\Services\BorrowingService::class);
    $stats = $borrowingService->getOverdueStatistics();
    $this->info("Overdue borrowings: {$stats['total_overdue']}");

    // Test FineCalculationService
    $this->info('💰 Testing FineCalculationService...');
    $fineService = app(\App\Services\FineCalculationService::class);
    $fineStats = $fineService->getFineStatistics();
    $this->info("Total fines: {$fineStats['total_fines']}");

    $this->info('✅ All services working!');
})->purpose('Test all library services');

// Quick database reset for development
Artisan::command('dev:reset', function () {
    if (app()->environment('production')) {
        $this->error('Cannot run this command in production!');
        return;
    }

    $this->warn('This will reset the entire database!');

    if (!$this->confirm('Do you wish to continue?')) {
        return;
    }

    $this->info('Resetting database...');
    $this->call('migrate:fresh', ['--seed' => true]);
    $this->info('✅ Database reset complete!');

    $this->newLine();
    $this->info('Default credentials:');
    $this->table(
        ['Role', 'Email', 'Password'],
        [
            ['Super Admin', 'admin@stti.ac.id', 'admin123'],
            ['Pustakawan', 'pustaka1@stti.ac.id', 'pustaka123'],
            ['Mahasiswa', 'rizki.if23@student.stti.ac.id', 'mahasiswa123'],
        ]
    );
})->purpose('Reset database with fresh seed (development only)');
