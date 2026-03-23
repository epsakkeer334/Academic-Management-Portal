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
        Schema::create('syllabus_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curriculum_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('institute_id');
            $table->decimal('coverage_percentage', 5, 2)->default(0); // Track exam readiness
            $table->enum('status', ['planned', 'in_progress', 'completed'])->default('planned');
            $table->date('start_date')->nullable();
            $table->date('planned_completion')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('curriculum_id')->references('id')->on('curriculums');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->foreign('institute_id')->references('id')->on('institutes');
            $table->unique(['curriculum_id', 'subject_id', 'institute_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('syllabus_mappings');
    }
};
