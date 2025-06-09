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
            Schema::create('funding_requests', function (Blueprint $table) {
                  $table->id();
                  $table->decimal('amount', 15, 2);
                  $table->foreignId('payment_method_id')
                        ->nullable()
                        ->constrained('payment_methods')
                        ->onUpdate('cascade')
                        ->onDelete('set null');
                  $table->foreignId('account_id')
                        ->nullable()
                        ->constrained('accounts')
                        ->onUpdate('cascade')
                        ->onDelete('set null');
                  $table->string('purpose', 255);
                  $table->text('approval_notes')->nullable();
                  $table->foreignId('department_id')
                        ->nullable()
                        ->constrained('departments')
                        ->onUpdate('cascade')
                        ->onDelete('set null');
                  $table->foreignId('mining_site_id')
                        ->nullable()
                        ->constrained('mining_sites')
                        ->onUpdate('cascade')
                        ->onDelete('cascade');
                  $table->foreignId('accountant_id')
                        ->nullable()
                        ->constrained('users')
                        ->onUpdate('cascade')
                        ->onDelete('set null');
                  $table->foreignId('approved_by')
                        ->nullable()
                        ->constrained('users')
                        ->onUpdate('cascade')
                        ->onDelete('set null');
                  $table->dateTime('decision_date')->nullable();
                  $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
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
