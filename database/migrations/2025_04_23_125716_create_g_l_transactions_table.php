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
            $table->date('trans_date');
            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('suppliers')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->foreignId('trip_id')
                ->nullable()
                ->constrained('trips')
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->enum('trans_type', [
                'invoice',
                'payment',
            ]);
            $table->string('description');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('set null');
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
