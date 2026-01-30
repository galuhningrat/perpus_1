<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim_nidn', 20)->unique()->after('id');
            $table->enum('role', ['super_admin', 'pustakawan', 'member'])->default('member')->after('password');
            $table->enum('prodi', ['Informatika', 'Elektro', 'Mesin', 'Umum'])->nullable()->after('role');
            $table->string('phone', 20)->nullable()->after('prodi');
            $table->boolean('is_active')->default(true)->after('phone');

            $table->index('nim_nidn');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nim_nidn', 'role', 'prodi', 'phone', 'is_active']);
        });
    }
};
