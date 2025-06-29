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
        Schema::create('case_deadlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->datetime('due_date');
            $table->datetime('reminder_date')->nullable();
            $table->enum('status', ['pending', 'completed', 'overdue', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->timestamps();
            
            // Indexes for common queries
            $table->index(['case_id', 'status']);
            $table->index(['due_date', 'status']);
            $table->index('priority');
            $table->index(['case_id', 'due_date']);
            $table->index('reminder_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_deadlines');
    }
};
