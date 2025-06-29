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
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('case_number')->nullable()->unique();
            $table->string('type'); // 'civil', 'criminal', 'family', etc.
            $table->string('jurisdiction');
            $table->string('venue')->nullable();
            $table->enum('status', ['active', 'closed', 'pending', 'archived'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for common queries
            $table->index(['user_id', 'status']);
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
