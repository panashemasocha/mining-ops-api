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
        Schema::create('driver_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade') 
                  ->unique();
            $table->string('license_number')->nullable();
            $table->decimal('last_known_longitude', 10, 6)->nullable();
            $table->decimal('last_known_latitude', 10, 6)->nullable();
            $table->decimal('last_known_altitude', 10, 2)->nullable();
            $table->string('status')->default('off trip'); // active trip, off trip
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_info');
    }
};
