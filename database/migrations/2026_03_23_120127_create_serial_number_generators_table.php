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
        Schema::create('serial_number_generators', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['marksheet', 'er_number', 'exam_code'])->unique();
            $table->bigInteger('current_number')->default(0);
            $table->string('prefix')->nullable(); // e.g., 'MS', 'ER', 'EXM'
            $table->integer('padding')->default(6); // Number of zeros to pad
            $table->string('separator')->default('-');
            $table->text('format')->nullable(); // Format pattern
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('serial_number_generators');
    }
};
