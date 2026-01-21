<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_do_id')->constrained('sales_do')->cascadeOnDelete();
            $table->foreignId('collection_id')->constrained('fin_collections');
            $table->string('payment_number', 50)->unique();
            $table->date('payment_date');
            $table->decimal('payment_amount', 15, 2)->default(0);
            $table->enum('payment_method', ['cash', 'transfer', 'check', 'giro', 'other']);
            $table->string('bank_name', 100)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('sales_do_id');
            $table->index('collection_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_payments');
    }
};