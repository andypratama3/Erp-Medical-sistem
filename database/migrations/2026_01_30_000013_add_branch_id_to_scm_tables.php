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
        // Add branch_id to scm_drivers
        if (!Schema::hasColumn('scm_drivers', 'branch_id')) {
            Schema::table('scm_drivers', function (Blueprint $table) {
                $table->foreignId('branch_id')->after('id')->nullable()
                    ->constrained('master_branches')->onDelete('cascade');
                $table->index('branch_id');
            });
        }

        // Add branch_id to scm_deliveries
        if (!Schema::hasColumn('scm_deliveries', 'branch_id')) {
            Schema::table('scm_deliveries', function (Blueprint $table) {
                $table->foreignId('branch_id')->after('id')->nullable()
                    ->constrained('master_branches')->onDelete('cascade');
                $table->index('branch_id');
            });
        }

        // Add additional fields to scm_drivers if not exist
        Schema::table('scm_drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('scm_drivers', 'email')) {
                $table->string('email', 100)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('scm_drivers', 'license_expiry')) {
                $table->date('license_expiry')->nullable()->after('license_number');
            }
            if (!Schema::hasColumn('scm_drivers', 'vehicle_capacity')) {
                $table->decimal('vehicle_capacity', 8, 2)->nullable()->after('vehicle_number');
            }
            if (!Schema::hasColumn('scm_drivers', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });

        // Add additional fields to scm_deliveries if not exist
        Schema::table('scm_deliveries', function (Blueprint $table) {
            if (!Schema::hasColumn('scm_deliveries', 'shipping_address')) {
                $table->text('shipping_address')->nullable()->after('delivery_status');
            }
            if (!Schema::hasColumn('scm_deliveries', 'tracking_number')) {
                $table->string('tracking_number', 100)->nullable()->after('shipping_address');
            }
            if (!Schema::hasColumn('scm_deliveries', 'notes')) {
                $table->text('notes')->nullable()->after('delivery_notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove branch_id from tables
        if (Schema::hasColumn('scm_drivers', 'branch_id')) {
            Schema::table('scm_drivers', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasColumn('scm_deliveries', 'branch_id')) {
            Schema::table('scm_deliveries', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        // Remove additional fields
        Schema::table('scm_drivers', function (Blueprint $table) {
            $columns = ['email', 'license_expiry', 'vehicle_capacity', 'notes'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('scm_drivers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('scm_deliveries', function (Blueprint $table) {
            $columns = ['shipping_address', 'tracking_number', 'notes'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('scm_deliveries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
