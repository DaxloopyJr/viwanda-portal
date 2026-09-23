<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('institution_id')
                ->constrained()->nullOnDelete();
        });

        Schema::table('datasets', function (Blueprint $table) {
            // Ministry department that owns the dataset (ministry-internal catalogue entries).
            $table->foreignId('department_id')->nullable()->after('institution_id')
                ->constrained()->nullOnDelete();
            // Free-text owning department label from the source data-dictionary (e.g. "Legal Department").
            $table->string('department')->nullable()->after('department_id');
        });

        // Ministry-department datasets have no institution.
        Schema::table('datasets', function (Blueprint $table) {
            $table->unsignedBigInteger('institution_id')->nullable()->change();
        });

        Schema::table('submissions', function (Blueprint $table) {
            // Groups the per-dataset submissions created together in one batch.
            $table->string('batch_reference', 30)->nullable()->after('reference')->index();
            // Target data consumers chosen at submission time.
            $table->json('consumers')->nullable()->after('channel');
        });

        // Ministry-department submissions have no institution either.
        Schema::table('submissions', function (Blueprint $table) {
            $table->unsignedBigInteger('institution_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex(['batch_reference']);
            $table->dropColumn(['batch_reference', 'consumers']);
        });
        Schema::table('datasets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
            $table->dropColumn('department');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
