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
        Schema::create('odometer_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')
                ->constrained('vehicles')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('trip_id')
                ->nullable()
                ->constrained('trips')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->unsignedBigInteger('initial_value')->nullable();
            $table->unsignedBigInteger('trip_end_value')->nullable();
            $table->enum('reading_unit', ['Kilometre', 'Mile'])->nullable();
            $table->boolean('meter_not_working')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odometer_readings');
    }
};
