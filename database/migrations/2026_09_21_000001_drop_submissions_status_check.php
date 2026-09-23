<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * PostgreSQL keeps the old CHECK constraint from the original enum('status')
 * column even after it was widened to string(30), so new workflow states
 * (internal_review, accounting_review, ...) are rejected with SQLSTATE 23514.
 * Drop the constraint; the application layer validates statuses.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE submissions DROP CONSTRAINT IF EXISTS submissions_status_check');
        }
    }

    public function down(): void
    {
        // Intentionally not re-adding the check: the status list is workflow-driven.
    }
};
