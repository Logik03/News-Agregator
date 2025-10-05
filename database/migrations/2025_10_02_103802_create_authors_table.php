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
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('external_id', 191)->nullable()->index(); 
            $table->string('name')->index();
            $table->string('email')->nullable()->unique(); 
            $table->string('profile_url')->nullable();    
            $table->string('avatar_url')->nullable(); 
            $table->text('bio')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['external_id']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
