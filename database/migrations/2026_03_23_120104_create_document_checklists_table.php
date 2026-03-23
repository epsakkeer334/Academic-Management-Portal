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
        Schema::create('document_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'Medical Certificate', '10th Marksheet'
            $table->text('description')->nullable();
            $table->unsignedBigInteger('institute_id')->nullable(); // Null = Global
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->foreign('institute_id')->references('id')->on('institutes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_checklists');
    }
};
