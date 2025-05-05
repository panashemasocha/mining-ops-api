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

            $table->unsignedBigInteger('category_id')->nullable();
            // nullable foreign keys
            $table->foreign('category_id', 'fk_vehicles_category1')
                ->references('id')
                ->on('vehicle_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('sub_type_id')->nullable()
                ->constrained('vehicle_sub_types')
                ->cascadeOnUpdate()
                ->cascadeOnUpdate();

            $table->foreignId('department_id')->nullable()
                ->constrained('departments')
                ->cascadeOnUpdate()
                ->cascadeOnUpdate();

            $table->foreignId('assigned_site_id')->nullable()
                ->constrained('mining_sites')
                ->cascadeOnUpdate()
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

            $table->enum('status', ['inactive', 'maintenance', 'active trip', 'off trip'])->default('off trip');

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
