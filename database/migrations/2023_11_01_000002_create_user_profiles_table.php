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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->json('preferred_flavors')->nullable();
            $table->json('preferred_food_pairings')->nullable();
            $table->decimal('preferred_price_min', 8, 2)->nullable();
            $table->decimal('preferred_price_max', 8, 2)->nullable();
            $table->json('preferred_types')->nullable();
            $table->json('preferred_regions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
}; 