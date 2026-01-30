<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\QRCodeService;
use App\Models\Book;

class RegenerateQRCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:regenerate
                            {--book-id= : Regenerate for specific book ID}
                            {--all : Regenerate for all books}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate QR codes for books';

    protected $qrService;

    public function __construct(QRCodeService $qrService)
    {
        parent::__construct();
        $this->qrService = $qrService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('book-id')) {
            $this->regenerateSingle($this->option('book-id'));
        } elseif ($this->option('all')) {
            $this->regenerateAll();
        } else {
            $this->error('Please specify --book-id or --all option');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function regenerateSingle($bookId)
    {
        $book = Book::find($bookId);

        if (!$book) {
            $this->error("Book with ID {$bookId} not found");
            return;
        }

        $this->info("Regenerating QR code for: {$book->title}");

        $bookData = [
            'id' => $book->id,
            'title' => $book->title,
            'isbn' => $book->isbn,
            'category' => $book->category->name,
            'shelf_location' => $book->shelf_location,
        ];

        $qrPath = $this->qrService->generateBookQRCode($bookData);
        $book->update(['qr_code' => $qrPath]);

        $this->info("✅ QR code regenerated successfully");
    }

    protected function regenerateAll()
    {
        $books = Book::all();
        $bar = $this->output->createProgressBar($books->count());

        $this->info("Regenerating QR codes for {$books->count()} books...");
        $bar->start();

        foreach ($books as $book) {
            $bookData = [
                'id' => $book->id,
                'title' => $book->title,
                'isbn' => $book->isbn,
                'category' => $book->category->name,
                'shelf_location' => $book->shelf_location,
            ];

            $qrPath = $this->qrService->generateBookQRCode($bookData);
            $book->update(['qr_code' => $qrPath]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ All QR codes regenerated successfully");
    }
}
