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
        Schema::create('gl_payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_trans_id')
                ->constrained('gl_transactions')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->foreignId('invoice_trans_id')
                ->constrained('gl_transactions')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->decimal('allocated_amount', 12, 2);
            $table->timestamps();

            // Indexes
            $table->index('payment_trans_id');
            $table->index('invoice_trans_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gl_payment_allocations');
    }
};
