<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_boards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_do_id')
                  ->constrained('sales_do')
                  ->onDelete('cascade');

            // Task info
            $table->enum('module', ['crm', 'wqs', 'scm', 'act', 'fin'])
                  ->default('wqs')
                  ->index();
            $table->string('task_type')
                  ->comment('wqs_stock_check, scm_delivery, etc');
            $table->enum('task_status', ['pending', 'in_progress', 'on_hold', 'completed', 'rejected'])
                  ->default('pending')
                  ->index();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                  ->default('medium')
                  ->index();

            $table->text('task_description');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();

            // Assignment & tracking
            $table->foreignId('assigned_to')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Audit
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->onDelete('restrict');
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for query performance
            $table->index(['module', 'task_status']);
            $table->index(['sales_do_id', 'module']);
            $table->index(['assigned_to', 'task_status']);
            $table->index(['due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_boards');
    }
};
