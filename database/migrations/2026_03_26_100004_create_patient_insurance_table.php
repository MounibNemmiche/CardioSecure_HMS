<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_insurance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('insurance_plan_id')->constrained()->restrictOnDelete();
            $table->text('policy_number')->nullable(); // AES-256 encrypted via cast
            $table->text('holder_name')->nullable(); // AES-256 encrypted via cast
            $table->text('tessera_sanitaria_number')->nullable(); // AES-256 encrypted via cast
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_insurance');
    }
};
