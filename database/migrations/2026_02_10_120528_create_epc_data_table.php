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
        Schema::create('epc_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('current_energy_rating')->nullable();
            $table->string('potential_energy_rating')->nullable();
            $table->integer('current_energy_efficiency')->nullable();
            $table->integer('potential_energy_efficiency')->nullable();
            $table->integer('environment_impact_current')->nullable();
            $table->integer('environment_impact_potential')->nullable();
            $table->integer('energy_consumption_current')->nullable();
            $table->integer('energy_consumption_potential')->nullable();
            $table->decimal('co2_emissions_current', 8, 2)->nullable();
            $table->decimal('co2_emissions_potential', 8, 2)->nullable();
            $table->integer('lighting_cost_current')->nullable();
            $table->integer('lighting_cost_potential')->nullable();
            $table->integer('heating_cost_current')->nullable();
            $table->integer('heating_cost_potential')->nullable();
            $table->integer('hot_water_cost_current')->nullable();
            $table->integer('hot_water_cost_potential')->nullable();
            $table->string('main_heating_description')->nullable();
            $table->string('main_fuel_type')->nullable();
            $table->date('lodgement_date')->nullable();
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
        Schema::dropIfExists('epc_data');
    }
};
