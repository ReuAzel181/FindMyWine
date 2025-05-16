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
        Schema::create('wines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('vintage')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('grape_variety')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->text('flavor_profile')->nullable();
            $table->text('food_pairings')->nullable();
            $table->text('tasting_notes')->nullable();
            $table->string('alcohol_content')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
            
            // Add unique constraint for name + vintage combination
            $table->unique(['name', 'vintage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wines');
    }
}; 