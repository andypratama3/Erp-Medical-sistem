<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wqs_stock_check_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_check_id')
                  ->constrained('wqs_stock_checks')
                  ->onDelete('cascade');
            $table->foreignId('product_id')
                  ->constrained('master_products')
                  ->onDelete('restrict');
            $table->enum('stock_status', ['available', 'partial', 'not_available'])->default('available');
            $table->integer('available_qty')->default(0);
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['stock_check_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wqs_stock_check_items');
    }
};
