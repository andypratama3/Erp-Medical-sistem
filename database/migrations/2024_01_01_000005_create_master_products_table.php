<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('product_group_id')->constrained('product_groups');
            $table->foreignId('manufacture_id')->constrained('master_manufactures');
            $table->string('sku', 100)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('unit', 50)->default('PCS');
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->integer('min_stock')->default(0);
            $table->integer('max_stock')->default(0);
            $table->string('barcode', 100)->nullable();
            $table->enum('product_type', ['medical_device', 'pharmaceutical', 'consumable', 'other'])->default('medical_device');
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_importable')->default(false);
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->softDeletes();
            $table->timestamps();

            $table->index('sku');
            $table->index('category_id');
            $table->index('product_group_id');
            $table->index('manufacture_id');
            $table->index('status');
            $table->index('product_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_products');
    }
};