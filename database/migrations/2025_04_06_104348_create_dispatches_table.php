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
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ore_id')->constrained('ores');
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('site_clerk_id')->constrained('users');
            $table->string('loading_method')->nullable(); // 'manual', 'mechanic'
            $table->decimal('ore_cost_per_tonne', 10, 2);
            $table->decimal('loading_cost_per_tonne', 10, 2);
            $table->decimal('ore_quantity_remaining', 10, 2);
            $table->string('status')->default('pending'); // 'pending', 'accepted', 'rejected'
            $table->string('payment_status')->default('n/a'); // 'fully-paid', 'pending', 'partially-paid', 'n/a'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatches');
    }
};
