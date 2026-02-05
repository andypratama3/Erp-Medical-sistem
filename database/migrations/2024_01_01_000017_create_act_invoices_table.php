<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('act_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_do_id')->constrained('sales_do')->cascadeOnDelete();
            $table->string('invoice_number', 50)->unique();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->string('faktur_pajak_number', 50)->nullable();
            $table->date('faktur_pajak_date')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('invoice_status', ['draft', 'issued', 'tukar_faktur', 'completed'])->default('draft');
            $table->timestamp('tukar_faktur_at')->nullable();
            $table->string('tukar_faktur_pic', 200)->nullable();
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'overdue'])->default('unpaid');

            $table->date('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('sales_do_id');
            $table->index('invoice_number');
            $table->index('invoice_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('act_invoices');
    }
};
