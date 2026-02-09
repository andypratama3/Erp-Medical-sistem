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
        Schema::create('master_employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('employee_code');
            $table->string('employee_name');
            $table->string('dept_code');
            $table->enum('level_type', ['level_1', 'level_2', 'level_3', 'level_4', 'level_5', 'level_6', 'level_7', 'level_8', 'level_9', 'level_10'])->default('level_1');
            $table->string('grade')->nullable();
            $table->enum('payroll_status', ['active', 'inactive'])->default('active');
            $table->string('payroll_level');
            $table->string('job_title');
            $table->string('nik');
            $table->string('npwp');
            $table->string('bpjs_tk_no');
            $table->string('bpjs_kes_no');
            $table->string('education');
            $table->string('office_code');
            $table->string('join_year');
            $table->string('join_month');
            $table->string('phone');
            $table->string('email');
            $table->string('bank_name');
            $table->string('bank_branch');
            $table->string('bank_account_name');
            $table->string('bank_account_number');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('note')->nullable();

            $table->index(['employee_code','employee_name']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_employees');
    }
};
