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
        Schema::create('gl_transactions', function (Blueprint $table) {
            $table->id();

            // Transaction date
            $table->date('trans_date')->index();

            // Optional links to supplier and trip
            $table->foreignId('supplier_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnUpdate()
                  ->nullOnDelete();

            $table->foreignId('trip_id')
                  ->nullable()
                  ->constrained()
                  ->cascadeOnUpdate()
                  ->nullOnDelete();

            // Type of transaction
            $table->enum('trans_type', ['invoice', 'payment', 'requisition'])
                  ->comment('Invoice, payment, or requisition')
                  ->index();

            // Description of the transaction
            $table->string('description');

            // Who created it
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->cascadeOnUpdate()
                  ->nullOnDelete();

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gl_transactions');
    }
};
