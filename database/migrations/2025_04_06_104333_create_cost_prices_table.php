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
        Schema::create('cost_prices', function (Blueprint $table) {
            $table->id();
            $table->string('commodity'); // 'loading cost', 'ore cost','diesel cost'
            $table->string('ore_type')->nullable();
            $table->string('quality_type')->nullable(); // now nullable for loading cost
            $table->string('quality_grade')->nullable(); // now nullable for loading cost
            $table->decimal('price', 10, 2);
            $table->foreignId('created_by')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_prices');
    }
};
