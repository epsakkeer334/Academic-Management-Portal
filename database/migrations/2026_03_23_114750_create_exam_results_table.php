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
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_application_id');
            $table->unsignedBigInteger('subject_id');
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->decimal('total_marks', 8, 2);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->enum('grade', ['A+', 'A', 'B+', 'B', 'C+', 'C', 'D', 'F'])->nullable();
            $table->enum('status', ['pass', 'fail'])->nullable();
            $table->enum('exam_type', ['online', 'offline', 'practical'])->default('online');
            $table->text('remarks')->nullable();

            // Marking Details
            $table->unsignedBigInteger('marked_by'); // Faculty member
            $table->timestamp('marked_at');

            // Super Admin Override
            $table->boolean('is_overridden')->default(false);
            $table->decimal('overridden_marks', 8, 2)->nullable();
            $table->unsignedBigInteger('overridden_by')->nullable();
            $table->timestamp('overridden_at')->nullable();
            $table->text('override_reason')->nullable();

            $table->timestamps();

            $table->foreign('exam_application_id')->references('id')->on('exam_applications');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->foreign('marked_by')->references('id')->on('users');
            $table->foreign('overridden_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exam_results');
    }
};
