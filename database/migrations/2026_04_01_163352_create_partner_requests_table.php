<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // No-op: this migration duplicated table creation in this codebase.
        // The table is created by 2026_02_12_144117 and user_read is added later.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op.
    }
};
