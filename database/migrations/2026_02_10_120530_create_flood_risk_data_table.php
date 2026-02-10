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
        Schema::create('flood_risk_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('flood_risk_level')->nullable();
            $table->string('flood_zone')->nullable();
            $table->string('river_and_sea_risk')->nullable();
            $table->string('surface_water_risk')->nullable();
            $table->string('reservoir_risk')->nullable();
            $table->json('active_warnings')->nullable();
            $table->json('raw_response')->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flood_risk_data');
    }
};
