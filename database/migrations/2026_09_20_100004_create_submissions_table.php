<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 30)->unique();
            $table->string('transaction_reference', 64)->nullable();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dataset_id')->constrained()->cascadeOnDelete();
            $table->string('reporting_period', 20);
            $table->enum('channel', ['portal', 'upload', 'api'])->default('portal');
            $table->enum('status', ['draft', 'submitted', 'under_review', 'returned', 'accepted', 'rejected', 'published'])->default('draft');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_comments')->nullable();
            $table->json('validation_errors')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('records_count')->default(0);
            $table->timestamps();

            $table->unique(['institution_id', 'transaction_reference']);
            $table->index(['status', 'reporting_period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
