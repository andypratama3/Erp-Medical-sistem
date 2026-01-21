<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_do_id')->constrained('sales_do')->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('act_invoices');
            $table->string('collection_number', 50)->unique();
            $table->date('collection_date');
            $table->decimal('amount_collected', 15, 2)->default(0);
            $table->enum('payment_method', ['cash', 'transfer', 'check', 'giro', 'other'])->default('transfer');
            $table->string('payment_reference', 100)->nullable();
            $table->enum('collection_status', ['pending', 'partial', 'completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('collected_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('sales_do_id');
            $table->index('invoice_id');
            $table->index('collection_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_collections');
    }
};