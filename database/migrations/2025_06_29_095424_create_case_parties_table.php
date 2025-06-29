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
        Schema::create('case_parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('role', ['plaintiff', 'defendant', 'attorney', 'judge', 'witness']);
            $table->enum('party_type', ['individual', 'organization', 'court_official'])->default('individual');
            $table->json('contact_info')->nullable(); // Flexible contact data
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
            
            // Indexes for common queries
            $table->index(['case_id', 'role']);
            $table->index('role');
            $table->index('party_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_parties');
    }
};
