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
        Schema::create('reports', function (Blueprint $table) {
            $table->id('report_id');
            $table->text('content');
            $table->string('status', 100)->nullable()->default('pending');
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('lecturer_id')->nullable();
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->foreign('student_id')->references('student_id')->on('students');
            $table->foreign('lecturer_id')->references('lecturer_id')->on('lecturers');
            $table->foreign('technician_id')->references('technician_id')->on('technicians');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
