<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_do', function (Blueprint $table) {
            $table->id();

            // Core identity
            $table->string('do_code', 50)->unique();
            $table->string('tracking_code', 50)->nullable();

            // Dates & relations
            $table->date('do_date');
            $table->foreignId('customer_id')->constrained('master_customers');
            $table->foreignId('office_id')->constrained('master_offices');

            // Shipping & customer info
            $table->text('shipping_address');
            $table->string('pic_customer', 100)->nullable();

            // Payment & tax
            $table->foreignId('payment_term_id')
                ->nullable()
                ->constrained('master_payment_terms');

            $table->foreignId('tax_id')
                ->nullable()
                ->constrained('master_tax');

            // Amounts
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            // Status flow (CRM → WQS → SCM → FIN)
            $table->enum('status', [
                'crm_to_wqs',
                'wqs_ready',
                'wqs_on_hold',
                'scm_on_delivery',
                'scm_delivered',
                'act_tukar_faktur',
                'act_invoiced',
                'fin_on_collect',
                'fin_paid',
                'fin_overdue',
            ])->default('crm_to_wqs');

            // Notes
            $table->text('notes_crm')->nullable();

            // Audit
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('do_code');
            $table->index('tracking_code');
            $table->index('customer_id');
            $table->index('office_id');
            $table->index('status');
            $table->index('do_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_do');
    }
};
