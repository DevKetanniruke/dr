<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_settings', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_name');
            $table->string('tagline')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address');
            $table->string('logo_path')->nullable();
            $table->string('currency')->default('$');
            $table->string('invoice_prefix')->default('INV-');
            $table->string('patient_prefix')->default('PAT-');
            $table->integer('slot_duration_minutes')->default(15);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_settings');
    }
};
