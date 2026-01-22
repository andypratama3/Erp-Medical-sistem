<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_do_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sales_do_id')
                ->constrained('sales_do')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('master_products');

            // Line info
            $table->unsignedInteger('line_number');

            // Snapshot product info
            $table->string('product_sku', 100);
            $table->string('product_name', 255);
            $table->string('unit', 50);

            // Quantities
            $table->unsignedInteger('qty_ordered');
            $table->unsignedInteger('qty_delivered')->default(0);

            // Pricing
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2);

            $table->timestamps();

            // Indexes
            $table->index(['sales_do_id', 'line_number']);
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_do_items');
    }
};
