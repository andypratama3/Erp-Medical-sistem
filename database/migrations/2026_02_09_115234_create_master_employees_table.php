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
            $table->enum('payroll_status', ['permanent', 'contract', 'probation'])->default('permanent');
            $table->string('payroll_level')->nullable();
            $table->string('job_title');
            $table->string('nik');
            $table->string('npwp')->nullable();
            $table->string('bpjs_tk_no')->nullable();
            $table->string('bpjs_kes_no')->nullable();
            $table->string('education')->nullable();
            $table->string('office_code')->nullable();
            $table->string('join_year');
            $table->string('join_month');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
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
