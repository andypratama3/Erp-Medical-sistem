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
            $table->foreignId('sales_do_id')->constrained('sales_do')->cascadeOnDelete();
            $table->enum('module', ['wqs', 'scm', 'act', 'fin']);
            $table->enum('task_status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->text('task_description')->nullable();
            $table->date('due_date')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('sales_do_id');
            $table->index('module');
            $table->index('task_status');
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_boards');
    }
};