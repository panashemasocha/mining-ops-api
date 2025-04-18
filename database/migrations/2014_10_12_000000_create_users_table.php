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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number')->unique();
            $table->string('pin');
            $table->foreignId('job_position_id')
                ->constrained('job_positions')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->foreignId('branch_id')
                ->constrained('branches')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->foreignId('department_id')
                ->constrained('departments')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->foreignId('role_id')
                ->constrained('user_roles')
                ->onUpdate('cascade')
                ->onDelete('no action');
            $table->string('physical_address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('national_id')->nullable();
            $table->string('gender')->nullable();
            $table->tinyInteger('status')->default(1); // 1: active, 0: deactivated, 2: on-leave
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
