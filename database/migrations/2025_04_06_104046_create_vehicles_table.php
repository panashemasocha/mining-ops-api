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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            // nullable foreign keys
            $table->foreignId('category_id')->nullable()
                ->constrained('categories')
                ->noActionOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('sub_type_id')->nullable()
                ->constrained('sub_types')
                ->noActionOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('department_id')->nullable()
                ->constrained('departments')
                ->noActionOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('assigned_site_id')->nullable()
                ->constrained('mining_sites')
                ->noActionOnDelete()
                ->cascadeOnUpdate();

            $table->string('reg_number');
            $table->string('vehicle_type')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->year('year_of_manufacture')->nullable();
            $table->string('vin')->nullable();

            $table->decimal('loading_capacity', 8, 2)->nullable();
            $table->integer('engine_hours')->nullable();

            $table->enum('fuel_type', ['petrol', 'diesel', 'electric', 'hybrid'])->nullable();
            $table->date('acquisition_date')->nullable();
            $table->date('next_service_date')->nullable();
            $table->date('insurance_expiry_date')->nullable();

            $table->decimal('last_known_longitude', 10, 6)->nullable();
            $table->decimal('last_known_latitude', 10, 6)->nullable();
            $table->decimal('last_known_altitude', 10, 2)->nullable();

            $table->enum('status', ['active', 'inactive', 'maintenance', 'decommissioned', 'active trip', 'off trip'])->default('off trip');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
