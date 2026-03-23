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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('er_number')->unique()->nullable(); // Unique ER number
            $table->string('enrollment_date')->nullable(); // Registration date
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->date('dob');
            $table->string('gender');
            $table->text('address');
            $table->string('city');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('country_id');
            $table->string('pincode');
            
            // Parent/Guardian Details
            $table->string('father_name')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_phone')->nullable();
            
            $table->string('qualification');
            $table->string('course_applied');
            $table->unsignedBigInteger('institute_id');
            
            // Document Tracking
            $table->json('kyc_documents')->nullable(); // Array of uploaded documents with paths
            $table->json('certificates')->nullable(); // Array of certificates
            $table->json('document_checklist')->nullable(); // Tracked document completion
            
            // Dual-Gate Approval Status
            $table->enum('document_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->unsignedBigInteger('verified_by')->nullable(); // Admin who verified
            $table->timestamp('verified_at')->nullable();
            $table->text('document_rejection_reason')->nullable();
            
            $table->enum('fee_status', ['pending', 'paid', 'rejected'])->default('pending');
            $table->unsignedBigInteger('fee_approved_by')->nullable(); // Accounts person
            $table->timestamp('fee_approved_at')->nullable();
            $table->text('fee_rejection_reason')->nullable();
            
            $table->enum('er_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('er_approved_at')->nullable();
            $table->unsignedBigInteger('er_approved_by')->nullable();
            
            // ID Card Details
            $table->string('id_card_path')->nullable();
            $table->string('id_card_serial')->nullable();
            $table->boolean('id_card_signed')->default(false);
            $table->unsignedBigInteger('id_card_signed_by')->nullable(); // TM signature
            
            // ER Form Details
            $table->string('er_form_path')->nullable();
            $table->boolean('er_form_signed')->default(false);
            $table->unsignedBigInteger('er_form_signed_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('institute_id')->references('id')->on('institutes');
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('verified_by')->references('id')->on('users');
            $table->foreign('fee_approved_by')->references('id')->on('users');
            $table->foreign('er_approved_by')->references('id')->on('users');
            $table->foreign('id_card_signed_by')->references('id')->on('users');
            $table->foreign('er_form_signed_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
};
