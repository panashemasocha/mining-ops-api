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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->foreignId('vehicle_id')
                ->nullable()
                ->constrained('vehicles')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->foreignId('dispatch_id')
                ->nullable()
                ->constrained('dispatches')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->decimal('ore_quantity', 10, 2); // in tonnes
            $table->foreignId('diesel_allocation_id')
                ->nullable()
                ->constrained('diesel_allocations')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->decimal('initial_longitude', 10, 6)->nullable();
            $table->decimal('initial_latitude', 10, 6)->nullable();
            $table->decimal('initial_altitude', 10, 2)->nullable();
            $table->decimal('final_longitude', 10, 6);
            $table->decimal('final_latitude', 10, 6);
            $table->decimal('final_altitude', 10, 2);
            $table->string('status')->default('pending'); // 'fulfilled', 'pending', 'in-transit', 'failed'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
