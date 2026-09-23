<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_periods', function (Blueprint $table) {
            $table->id();
            // Null institution = a ministry-wide period (ministry departments report against it).
            $table->foreignId('institution_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 40);
            $table->string('frequency', 20); // quarterly | monthly | annually | weekly
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('quarter')->nullable();
            $table->unsignedTinyInteger('month')->nullable();
            $table->date('opens_at')->nullable();
            $table->date('closes_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['institution_id', 'frequency', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_periods');
    }
};
