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
        Schema::table('act_invoices', function (Blueprint $table) {
            $table->timestamp('overdue_notified_at')->nullable()->after('approved_at');
            
            // Add payment_status if not exists
            if (!Schema::hasColumn('act_invoices', 'payment_status')) {
                $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'overdue'])
                    ->default('unpaid')
                    ->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('act_invoices', function (Blueprint $table) {
            $table->dropColumn('overdue_notified_at');
            
            // Only drop payment_status if we added it
            if (Schema::hasColumn('act_invoices', 'payment_status')) {
                $table->dropColumn('payment_status');
            }
        });
    }
};
