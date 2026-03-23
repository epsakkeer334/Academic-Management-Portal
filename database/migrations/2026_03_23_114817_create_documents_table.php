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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('document_code')->unique(); // MTOE001, SOP001, etc.
            $table->string('issue_number'); // Issue 02
            $table->string('revision_number'); // Rev 05
            $table->date('issue_date');
            $table->enum('type', ['mtoe', 'sop', 'training_manual', 'other']);
            $table->unsignedBigInteger('institute_id')->nullable(); // Null = Global
            $table->text('description')->nullable();

            // Draft Phase
            $table->string('draft_file_path');
            $table->unsignedBigInteger('uploaded_by');
            $table->timestamp('uploaded_at');

            // HoT Approval Phase
            $table->enum('hot_status', ['pending', 'approved', 'returned'])->default('pending');
            $table->unsignedBigInteger('hot_approved_by')->nullable();
            $table->timestamp('hot_approved_at')->nullable();
            $table->text('hot_remarks')->nullable();

            // External Approval Tracking
            $table->enum('external_status', ['pending', 'submitted', 'approved'])->default('pending');
            $table->timestamp('external_submitted_at')->nullable();
            $table->timestamp('external_approved_at')->nullable();
            $table->string('external_approver_name')->nullable();

            // Final Archive (7-Day Rule)
            $table->string('final_file_path')->nullable();
            $table->timestamp('final_uploaded_at')->nullable();
            $table->timestamp('seven_day_deadline')->nullable(); // External approved date + 7 days
            $table->boolean('seven_day_compliance_met')->default(false);

            // Overall Status
            $table->enum('status', ['draft', 'submitted_hot', 'approved_hot', 'submitted_external', 'approved_external', 'final_archived'])->default('draft');

            // Metadata
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('institute_id')->references('id')->on('institutes');
            $table->foreign('uploaded_by')->references('id')->on('users');
            $table->foreign('hot_approved_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
