<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->cascadeOnDelete();
            $table->string('medicine_name');
            $table->string('dosage'); // e.g. "500mg" or "1 tablet"
            $table->string('frequency'); // e.g. "1-0-1" or "Once daily"
            $table->string('duration'); // e.g. "5 days"
            $table->string('instructions')->nullable(); // e.g. "After meals"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
