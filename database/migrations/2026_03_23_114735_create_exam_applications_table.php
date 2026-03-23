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
        Schema::create('exam_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('exam_id');
            $table->json('subjects_applied'); // Array of subject IDs

            // ACCOUNTS Approval Gate
            $table->enum('accounts_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('accounts_rejection_reason')->nullable();
            $table->unsignedBigInteger('accounts_approved_by')->nullable();
            $table->timestamp('accounts_approved_at')->nullable();

            // TRAINING MANAGER (TM) Approval Gate
            $table->enum('tm_approval', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('tm_rejection_reason')->nullable();
            $table->unsignedBigInteger('tm_approved_by')->nullable();
            $table->timestamp('tm_approved_at')->nullable();
            $table->decimal('attendance_percentage', 5, 2)->nullable();

            // BiC (Base In-Charge) Override - Can bypass Accounts approval with reason
            $table->enum('bic_override', ['none', 'approved', 'rejected'])->default('none');
            $table->text('bic_reason')->nullable();
            $table->unsignedBigInteger('bic_by')->nullable();
            $table->timestamp('bic_at')->nullable();

            // Final Status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('admit_card_path')->nullable();
            $table->string('admit_card_serial')->nullable();

            // Admit Card Signature Requirements
            $table->boolean('admit_card_signed_by_em')->default(false);
            $table->unsignedBigInteger('signed_by_em')->nullable();
            $table->boolean('admit_card_signed_by_tm')->default(false);
            $table->unsignedBigInteger('signed_by_tm')->nullable();

            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students');
            $table->foreign('exam_id')->references('id')->on('exams');
            $table->foreign('accounts_approved_by')->references('id')->on('users');
            $table->foreign('tm_approved_by')->references('id')->on('users');
            $table->foreign('bic_by')->references('id')->on('users');
            $table->foreign('signed_by_em')->references('id')->on('users');
            $table->foreign('signed_by_tm')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exam_applications');
    }
};
