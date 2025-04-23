<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('excavator_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')
                ->constrained()
                ->noActionOnDelete()
                ->cascadeOnDelete();
            $table->foreignId('driver_id')
                ->constrained('users')
                ->noActionOnDelete()
                ->cascadeOnDelete();
                $table->foreignId('dispatch_id')
                ->constrained('dispatches')
                ->noActionOnDelete()
                ->cascadeOnDelete();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->foreignId('diesel_allocation_id')
                ->constrained('diesel_allocations')
                ->noActionOnDelete()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('excavator_usage');
    }
};
