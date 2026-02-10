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
        Schema::create('property_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('saved_property_id')->constrained()->cascadeOnDelete();
            $table->string('item_key');
            $table->string('assessment')->nullable(); // like, dislike, neutral
            $table->boolean('is_auto_assessed')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['saved_property_id', 'item_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_assessments');
    }
};
