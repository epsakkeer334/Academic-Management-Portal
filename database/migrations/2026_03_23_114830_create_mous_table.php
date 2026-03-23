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
        Schema::create('mous', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('practical_accessor_name'); // Organization/MRO name
            $table->text('scope_of_training'); // B1.1, B2, Engine, Avionics, etc.
            $table->date('validity_start');
            $table->date('validity_end');
            $table->unsignedBigInteger('institute_id');
            $table->enum('status', ['active', 'expired', 'terminated', 'renewal_pending'])->default('active');
            $table->text('termination_reason')->nullable();

            // Renewal Tracking
            $table->boolean('renewal_alert_sent')->default(false);
            $table->timestamp('renewal_alert_sent_at')->nullable();
            $table->unsignedBigInteger('renewal_initiated_by')->nullable();
            $table->timestamp('renewal_initiated_at')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('institute_id')->references('id')->on('institutes');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('renewal_initiated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mous');
    }
};
