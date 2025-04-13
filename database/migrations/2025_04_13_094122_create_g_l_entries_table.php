<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('gl_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trans_id')
                ->constrained('gl_transactions')
                ->onDelete('cascade');
            $table->foreignId('account_id')
                ->constrained('accounts');
            $table->decimal('debit_amt', 12, 2)->default(0);
            $table->decimal('credit_amt', 12, 2)->default(0);
            $table->timestamps();
        });

        // Add CHECK constraints (MySQL 8+)
        DB::statement(<<<SQL
            ALTER TABLE gl_entries
            ADD CONSTRAINT chk_gl_entries_non_negative
              CHECK (debit_amt >= 0 AND credit_amt >= 0),
            ADD CONSTRAINT chk_gl_entries_one_side_zero
              CHECK ((debit_amt = 0) OR (credit_amt = 0));
        SQL
        );
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE gl_entries DROP CHECK chk_gl_entries_non_negative');
        DB::statement('ALTER TABLE gl_entries DROP CHECK chk_gl_entries_one_side_zero');

        Schema::dropIfExists('gl_entries');
    }
};
