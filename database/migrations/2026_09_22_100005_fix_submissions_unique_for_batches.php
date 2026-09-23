<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Batch submissions share one transaction_reference across several
     * datasets, so uniqueness must be enforced per dataset within a batch
     * rather than per institution alone.
     */
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropUnique(['institution_id', 'transaction_reference']);
            $table->unique(['institution_id', 'transaction_reference', 'dataset_id'], 'submissions_tx_ref_dataset_unique');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropUnique('submissions_tx_ref_dataset_unique');
            $table->unique(['institution_id', 'transaction_reference']);
        });
    }
};
