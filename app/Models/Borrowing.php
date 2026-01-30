<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Borrowing extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'fine_amount',
        'fine_paid',
        'notes',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'fine_amount' => 'decimal:2',
        'fine_paid' => 'boolean',
    ];

    /**
     * Relasi: Borrowing belongs to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Borrowing belongs to Book
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Relasi: Borrowing has one Fine
     */
    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class);
    }

    /**
     * Scope: Active borrowings (borrowed or overdue)
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['borrowed', 'overdue']);
    }

    /**
     * Scope: Overdue borrowings
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Scope: Returned borrowings
     */
    public function scopeReturned(Builder $query): Builder
    {
        return $query->where('status', 'returned');
    }

    /**
     * Check if borrowing is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'overdue' ||
               (!$this->return_date && Carbon::parse($this->due_date)->isPast());
    }

    /**
     * Calculate days overdue
     */
    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        $endDate = $this->return_date ?? Carbon::now();
        return Carbon::parse($this->due_date)->diffInDays($endDate);
    }

    /**
     * Calculate fine amount
     */
    public function calculateFine(int $finePerDay = 1000): float
    {
        $daysOverdue = $this->getDaysOverdue();
        return $daysOverdue * $finePerDay;
    }

    /**
     * Mark as returned
     */
    public function markAsReturned(): bool
    {
        $this->return_date = Carbon::now();

        // Check if overdue
        if (Carbon::parse($this->due_date)->isPast()) {
            $this->status = 'returned';
            $this->fine_amount = $this->calculateFine();

            // Create fine record if overdue
            if ($this->fine_amount > 0) {
                Fine::create([
                    'borrowing_id' => $this->id,
                    'days_overdue' => $this->getDaysOverdue(),
                    'fine_per_day' => 1000,
                    'total_fine' => $this->fine_amount,
                ]);
            }
        } else {
            $this->status = 'returned';
        }

        return $this->save();
    }

    /**
     * Update overdue status
     */
    public function updateOverdueStatus(): void
    {
        if ($this->status === 'borrowed' && Carbon::parse($this->due_date)->isPast()) {
            $this->status = 'overdue';
            $this->fine_amount = $this->calculateFine();
            $this->save();
        }
    }
}
