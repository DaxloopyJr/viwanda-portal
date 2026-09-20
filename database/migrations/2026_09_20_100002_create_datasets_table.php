<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('frequency', 30)->default('Quarterly');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->string('source_system')->nullable();
            $table->string('consumers')->nullable();
            $table->json('fields');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datasets');
    }
};
