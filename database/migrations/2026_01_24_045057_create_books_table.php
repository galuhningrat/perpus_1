<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title', 500);
            $table->string('cover_image', 500)->nullable();
            $table->string('author', 255);
            $table->string('edition', 100)->nullable();
            $table->string('publisher', 255)->nullable();
            $table->integer('publication_year')->nullable();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('isbn', 20)->unique()->nullable();
            $table->integer('stock')->default(0);
            $table->integer('available_stock')->default(0);
            $table->string('shelf_location', 50)->nullable();
            $table->text('description')->nullable();
            $table->string('qr_code', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('category_id');
            $table->index('isbn');
            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
