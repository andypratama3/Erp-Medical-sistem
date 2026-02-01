<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add branch_id to wqs_stock_checks
        if (!Schema::hasColumn('wqs_stock_checks', 'branch_id')) {
            Schema::table('wqs_stock_checks', function (Blueprint $table) {
                $table->foreignId('branch_id')->after('id')->nullable()
                    ->constrained('master_branches')->onDelete('cascade');
                $table->index('branch_id');
            });
        }

        // Add branch_id to wqs_stock_check_items
        if (Schema::hasTable('wqs_stock_check_items') && !Schema::hasColumn('wqs_stock_check_items', 'branch_id')) {
            Schema::table('wqs_stock_check_items', function (Blueprint $table) {
                $table->foreignId('branch_id')->after('id')->nullable()
                    ->constrained('master_branches')->onDelete('cascade');
                $table->index('branch_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('wqs_stock_checks', 'branch_id')) {
            Schema::table('wqs_stock_checks', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasTable('wqs_stock_check_items') && Schema::hasColumn('wqs_stock_check_items', 'branch_id')) {
            Schema::table('wqs_stock_check_items', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
