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
            $table->string('do_number', 50)->unique();
            $table->date('do_date');
            $table->foreignId('customer_id')->constrained('master_customers');
            $table->foreignId('office_id')->constrained('master_offices');
            $table->text('customer_address')->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->string('customer_pic', 200)->nullable();
            $table->foreignId('payment_term_id')->nullable()->constrained('master_payment_terms');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', [
                'crm_to_wqs',
                'wqs_ready',
                'scm_on_delivery',
                'scm_delivered',
                'act_tukar_faktur',
                'act_invoiced',
                'fin_on_collect',
                'fin_paid',
                'cancelled'
            ])->default('crm_to_wqs');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();

            $table->index('do_number');
            $table->index('customer_id');
            $table->index('status');
            $table->index('do_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_do');
    }
};