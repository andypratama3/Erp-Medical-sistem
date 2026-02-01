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
        // Add branch_id to sales_do
        if (!Schema::hasColumn('sales_do', 'branch_id')) {
            Schema::table('sales_do', function (Blueprint $table) {
                $table->foreignId('branch_id')->after('id')->nullable()
                    ->constrained('master_branches')->onDelete('cascade');
                $table->index('branch_id');
            });
        }

        // Add branch_id to document_uploads (untuk multi-branch document management)
        if (!Schema::hasColumn('document_uploads', 'branch_id')) {
            Schema::table('document_uploads', function (Blueprint $table) {
                $table->foreignId('branch_id')->after('id')->nullable()
                    ->constrained('master_branches')->onDelete('cascade');
                $table->index('branch_id');
            });
        }

        // Add branch_id to task_boards
        if (!Schema::hasColumn('task_boards', 'branch_id')) {
            Schema::table('task_boards', function (Blueprint $table) {
                $table->foreignId('branch_id')->after('id')->nullable()
                    ->constrained('master_branches')->onDelete('cascade');
                $table->index('branch_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['sales_do', 'document_uploads', 'task_boards'];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'branch_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropForeign(['branch_id']);
                    $table->dropColumn('branch_id');
                });
            }
        }
    }
};
