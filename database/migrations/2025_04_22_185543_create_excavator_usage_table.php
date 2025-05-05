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
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('driver_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();
                $table->foreignId('dispatch_id')
                ->constrained('dispatches')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->dateTime('start');
            $table->dateTime('end');
            $table->foreignId('diesel_allocation_id')
                ->constrained('diesel_allocations')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('excavator_usage');
    }
};
