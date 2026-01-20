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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->enum('type', ['SINGLE', 'BUNDLE'])->default('SINGLE');
            $table->string('unit');
            $table->string('barcode')->nullable();
            $table->foreignId('manufacture_id')->nullable()->constrained('manufactures')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('product_group_id')->nullable()->constrained('product_groups')->nullOnDelete();
            $table->integer('stock_qty')->default(0);
            $table->integer('current_stock')->default(0);
            $table->string('akl_akd')->nullable();
            $table->string('akl_reg_no')->nullable();
            $table->date('expired_registration')->nullable();
            $table->string('general_name')->nullable();
            $table->string('licence_number')->nullable();
            $table->string('listing_level')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->json('photos')->nullable();
            $table->json('videos')->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('sku');
            $table->index('status');
            $table->index('type');
            $table->index('manufacture_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
