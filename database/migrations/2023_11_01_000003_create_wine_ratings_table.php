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
        Schema::create('wine_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('wine_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating'); // 1-5 stars
            $table->text('comment')->nullable();
            $table->timestamps();
            
            // Each user can rate a wine only once
            $table->unique(['user_profile_id', 'wine_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wine_ratings');
    }
}; 