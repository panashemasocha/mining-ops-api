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
        Schema::create('ores', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // e.g., 'Kyanite'
            $table->string('quality_type'); // 'Gem-Quality' or 'Industrial-Grade'
            $table->string('quality_grade'); // 'A', 'B', or 'C' for Gem-Quality, High,Medium,Low for Industrial-Grade
            $table->decimal('quantity', 10, 2);
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('created_by')->constrained('users');
            $table->string('location_name')->nullable();
            $table->decimal('longitude', 10, 6);
            $table->decimal('latitude', 10, 6);
            $table->decimal('altitude', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ores');
    }
};
