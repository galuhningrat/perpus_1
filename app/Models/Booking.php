<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'booking_date',
        'expiry_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    /**
     * Relasi: Booking belongs to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Booking belongs to Book
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Scope: Pending bookings
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Confirmed bookings
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope: Expired bookings
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', 'expired')
            ->orWhere(function ($q) {
                $q->where('status', 'pending')
                  ->where('expiry_date', '<', Carbon::now());
            });
    }

    /**
     * Check if booking is expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' ||
               ($this->status === 'pending' && Carbon::parse($this->expiry_date)->isPast());
    }

    /**
     * Check if booking is still valid
     */
    public function isValid(): bool
    {
        return $this->status === 'pending' && Carbon::parse($this->expiry_date)->isFuture();
    }

    /**
     * Confirm booking (convert to borrowing)
     */
    public function confirm(): ?Borrowing
    {
        if (!$this->isValid()) {
            return null;
        }

        // Create borrowing
        $borrowing = Borrowing::create([
            'user_id' => $this->user_id,
            'book_id' => $this->book_id,
            'borrow_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays(7), // 7 hari peminjaman
            'status' => 'borrowed',
        ]);

        // Update booking status
        $this->status = 'confirmed';
        $this->save();

        return $borrowing;
    }

    /**
     * Cancel booking
     */
    public function cancel(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->status = 'cancelled';
        $saved = $this->save();

        // Return stock to book
        if ($saved) {
            $this->book->increaseStock();
        }

        return $saved;
    }

    /**
     * Mark booking as expired
     */
    public function markAsExpired(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->status = 'expired';
        $saved = $this->save();

        // Return stock to book
        if ($saved) {
            $this->book->increaseStock();
        }

        return $saved;
    }

    /**
     * Auto-set expiry date (24 hours from now)
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (!$booking->expiry_date) {
                $booking->expiry_date = Carbon::now()->addHours(24);
            }
        });
    }
}
