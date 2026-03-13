<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('institute_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('staff'); // e.g., 'admin', 'manager', 'staff', 'accounts'
            $table->boolean('is_primary')->default(false); // If user is primary contact for institute
            $table->json('permissions')->nullable(); // Additional custom permissions
            $table->string('status')->default('active'); // active, inactive, suspended
            $table->timestamps();

            // Ensure unique combination of institute and user
            $table->unique(['institute_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institute_user');
    }
};
