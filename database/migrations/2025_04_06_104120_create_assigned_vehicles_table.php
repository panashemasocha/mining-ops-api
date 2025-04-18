<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assigned_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->foreignId('vehicle_id')
                ->constrained('vehicles')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assigned_vehicles');
    }
};
