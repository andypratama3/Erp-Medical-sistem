<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wqs_stock_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_do_id')->constrained('sales_do')->cascadeOnDelete();
            $table->enum('check_status', ['pending', 'checking', 'available', 'partial', 'unavailable'])->default('pending');
            $table->timestamp('checked_at')->nullable();
            $table->foreignId('checked_by')->nullable()->constrained('users');
            $table->text('stock_notes')->nullable();
            $table->json('stock_details')->nullable();
            $table->timestamps();

            $table->index('sales_do_id');
            $table->index('check_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wqs_stock_checks');
    }
};