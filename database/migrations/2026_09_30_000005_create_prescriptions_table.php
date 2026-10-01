<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->date('prescription_date');
            $table->text('general_instructions')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'prescription_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
