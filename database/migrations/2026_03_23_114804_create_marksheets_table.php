<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marksheets', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique(); // Unique across all institutions
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('curriculum_id')->nullable();
            $table->json('results'); // Array of exam_result IDs
            $table->decimal('total_marks', 8, 2);
            $table->decimal('obtained_marks', 8, 2);
            $table->decimal('percentage', 5, 2);
            $table->enum('overall_result', ['pass', 'fail'])->default('fail');

            // PDF generation
            $table->string('pdf_path')->nullable();
            $table->boolean('is_signed')->default(false);

            // Signature Requirements
            $table->boolean('signed_by_tm')->default(false);
            $table->unsignedBigInteger('tm_signature_user')->nullable();
            $table->timestamp('tm_signed_at')->nullable();

            $table->boolean('signed_by_em')->default(false);
            $table->unsignedBigInteger('em_signature_user')->nullable();
            $table->timestamp('em_signed_at')->nullable();

            // Consolidated Marksheet
            $table->boolean('is_consolidated')->default(false);
            $table->unsignedBigInteger('consolidated_from')->nullable(); // ID of program

            $table->unsignedBigInteger('generated_by');
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('exam_id')->references('id')->on('exams');
            $table->foreign('curriculum_id')->references('id')->on('curriculums');
            $table->foreign('generated_by')->references('id')->on('users');
            $table->foreign('tm_signature_user')->references('id')->on('users');
            $table->foreign('em_signature_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marksheets');
    }
};
