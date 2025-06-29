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
        Schema::create('case_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Document title
            $table->string('file_name'); // Original file name
            $table->string('file_path'); // Storage path
            $table->bigInteger('file_size')->nullable(); // File size in bytes
            $table->string('mime_type')->nullable();
            $table->string('category')->nullable(); // 'complaint', 'motion', 'order', 'evidence', etc.
            $table->json('tags')->nullable(); // Array of tags for organization
            $table->integer('version')->default(1);
            $table->enum('processing_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamp('ai_processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for common queries
            $table->index(['case_id', 'category']);
            $table->index('processing_status');
            $table->index('category');
            $table->index(['case_id', 'processing_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_documents');
    }
};
