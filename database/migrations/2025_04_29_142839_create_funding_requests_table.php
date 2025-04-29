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
        Schema::create('funding_requests', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2);
            $table->foreignId('payment_method_id')
                  ->constrained('payment_methods')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('account_id')
                  ->constrained('accounts')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('purpose', 255);
            $table->text('approval_notes')->nullable();
            $table->foreignId('department_id')
                  ->constrained('departments')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('mining_site_id')
                  ->constrained('mining_sites')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('accountant_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->dateTime('decision_date')->nullable();
            $table->enum('status', ['pending','accepted','rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funding_requests');
    }
};
