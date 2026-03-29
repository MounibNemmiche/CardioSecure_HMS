<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->date('visit_date');
            $table->string('visit_type', 100);
            $table->json('symptoms')->nullable(); // array of symptom keys
            $table->smallInteger('bp_systolic')->unsigned()->nullable();
            $table->smallInteger('bp_diastolic')->unsigned()->nullable();
            $table->smallInteger('heart_rate')->unsigned()->nullable();
            $table->smallInteger('oxygen_saturation')->unsigned()->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->text('recommendations')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->timestamps();

            $table->index('patient_id');
            $table->index('doctor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
