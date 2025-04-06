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
        Schema::create('cost_prices', function (Blueprint $table) {
            $table->id();
            $table->string('commodity'); // 'loading cost', 'ore cost'
            $table->string('ore_type');
            $table->string('quality');
            $table->decimal('price', 10, 2);
            $table->date('date_created');
            $table->foreignId('created_by')->constrained('users');
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
