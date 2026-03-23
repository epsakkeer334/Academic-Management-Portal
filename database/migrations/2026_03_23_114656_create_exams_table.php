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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['mid-sem', 'semester', 'practical']);
            $table->unsignedBigInteger('institute_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('curriculum_id');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('passing_percentage', 5, 2)->default(75.00);
            $table->enum('status', ['draft', 'scheduled', 'ongoing', 'completed', 'cancelled'])->default('draft');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('institute_id')->references('id')->on('institutes');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->foreign('curriculum_id')->references('id')->on('curriculums');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams');
    }
};
