<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('visit_code')->unique();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->dateTime('visit_date');
            
            // Vitals
            $table->integer('systolic_bp')->nullable();
            $table->integer('diastolic_bp')->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();
            
            // Clinical Notes
            $table->text('chief_complaint')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->text('advice')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->enum('status', ['in_progress', 'completed'])->default('completed');
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('visit_code');
            $table->index(['patient_id', 'visit_date']);
            $table->index(['doctor_id', 'visit_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
