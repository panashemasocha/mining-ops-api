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
        Schema::create('ores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type')
                ->constrained('ore_types')
                ->onUpdate('cascade')
                ->onDelete('no action'); // e.g., 'Kyanite'
            $table->foreignId('quality_type')
                ->constrained('ore_quality_types')
                ->onUpdate('cascade')
                ->onDelete('no action'); // 'Gem-Quality' or 'Industrial-Grade'
            $table->foreignId('quality_grade')
                ->constrained('ore_quality_grades')
                ->onUpdate('cascade')
                ->onDelete('no action'); // 'A', 'B', or 'C' for Gem-Quality, High,Medium,Low for Industrial-Grade
            $table->decimal('quantity', 10, 2);
            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->foreignId('created_by')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('no action');
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
