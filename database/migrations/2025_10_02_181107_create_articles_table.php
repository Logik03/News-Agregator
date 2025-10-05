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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
        
            $table->string('external_id', 191)->nullable();
            $table->foreignId('source_id')->constrained('sources')->cascadeOnDelete();
        
            $table->string('title');
            $table->text('excerpt')->nullable(); 
            $table->longText('content')->nullable(); 
            $table->string('image_url')->nullable(); 
        
            // URL handling
            $table->string('url')->index();
            $table->string('canonical_url')->unique();
        
            // Relations
            $table->foreignId('author_id')->nullable()->constrained('authors')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
        
            // Metadata
            $table->char('language', 5)->nullable()->index();
            $table->timestamp('published_at')->nullable()->index();
        
            // Search optimization
            $table->fullText(['title', 'excerpt', 'content']);
        
            // Bookkeeping
            $table->timestamps();
            $table->softDeletes();
        
            // Prevent duplicate imports
            $table->unique(['source_id', 'external_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
