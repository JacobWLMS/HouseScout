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
        Schema::table('properties', function (Blueprint $table) {
            $table->string('lsoa')->nullable();
            $table->string('msoa')->nullable();
            $table->string('ward')->nullable();
            $table->string('constituency')->nullable();
            $table->integer('easting')->nullable();
            $table->integer('northing')->nullable();
            $table->string('local_authority')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['lsoa', 'msoa', 'ward', 'constituency', 'easting', 'northing', 'local_authority']);
        });
    }
};
