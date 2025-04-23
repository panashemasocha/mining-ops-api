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
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ore_id')
                ->constrained('ores')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->foreignId('site_clerk_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->string('loading_method')->nullable(); // 'manual', 'mechanic'
            $table->decimal('ore_cost_per_tonne', 10, 2);
            $table->decimal('loading_cost_per_tonne', 10, 2);
            $table->decimal('ore_quantity', 10, 2);
            $table->decimal('max_quantity_per_trip', 10, 2);
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
