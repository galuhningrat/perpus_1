<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Builder;

    class Book extends Model
    {
        protected $fillable = [
            'title',
            'cover_image',
            'author',
            'edition',
            'publisher',
            'publication_year',
            'category_id',
            'isbn',
            'stock',
            'available_stock',
            'shelf_location',
            'description',
            'qr_code',
            'is_active',
        ];

        protected $casts = [
            'publication_year' => 'integer',
            'stock' => 'integer',
            'available_stock' => 'integer',
            'is_active' => 'boolean',
        ];

        /**
         * Relasi: Book belongs to Category
         */
        public function category(): BelongsTo
        {
            return $this->belongsTo(Category::class);
        }

        /**
         * Relasi: Book has many Borrowings
         */
        public function borrowings(): HasMany
        {
            return $this->hasMany(Borrowing::class);
        }

        /**
         * Relasi: Book has many Bookings
         */
        public function bookings(): HasMany
        {
            return $this->hasMany(Booking::class);
        }

        /**
         * Scope: Only active books
         */
        public function scopeActive(Builder $query): Builder
        {
            return $query->where('is_active', true);
        }

        /**
         * Scope: Books yang tersedia (available_stock > 0)
         */
        public function scopeAvailable(Builder $query): Builder
        {
            return $query->where('available_stock', '>', 0);
        }

        /**
         * Scope: Search by title, author, or ISBN
         */
        public function scopeSearch(Builder $query, ?string $search): Builder
        {
            if (!$search) {
                return $query;
            }

            return $query->where(function ($q) use ($search) {
                $q->where('title', 'ILIKE', "%{$search}%")
                ->orWhere('author', 'ILIKE', "%{$search}%")
                ->orWhere('isbn', 'ILIKE', "%{$search}%");
            });
        }

        /**
         * Scope: Filter by category
         */
        public function scopeByCategory(Builder $query, ?int $categoryId): Builder
        {
            if (!$categoryId) {
                return $query;
            }

            return $query->where('category_id', $categoryId);
        }

        /**
         * Accessor: Get status badge (Available/Borrowed/Out of Stock)
         */
        public function getStatusAttribute(): string
        {
            if ($this->available_stock > 0) {
                return 'available';
            }

            if ($this->stock > 0 && $this->available_stock == 0) {
                return 'borrowed';
            }

            return 'out_of_stock';
        }

        /**
         * Check if book is available for borrowing
         */
        public function isAvailable(): bool
        {
            return $this->is_active && $this->available_stock > 0;
        }

        /**
         * Decrease available stock (when borrowed/booked)
         */
        public function decreaseStock(int $quantity = 1): bool
        {
            if ($this->available_stock < $quantity) {
                return false;
            }

            $this->decrement('available_stock', $quantity);
            return true;
        }

        /**
         * Increase available stock (when returned/booking cancelled)
         */
        public function increaseStock(int $quantity = 1): bool
        {
            if ($this->available_stock + $quantity > $this->stock) {
                return false;
            }

            $this->increment('available_stock', $quantity);
            return true;
        }
    }
