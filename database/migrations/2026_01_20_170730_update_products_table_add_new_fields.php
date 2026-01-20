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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->unique()->after('id');
            }
            if (!Schema::hasColumn('products', 'type')) {
                $table->enum('type', ['SINGLE', 'BUNDLE'])->default('SINGLE')->after('name');
            }
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->after('unit');
            }
            if (!Schema::hasColumn('products', 'manufacture_id')) {
                $table->foreignId('manufacture_id')->nullable()->constrained('manufactures')->nullOnDelete()->after('barcode');
            }
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete()->after('manufacture_id');
            }
            if (!Schema::hasColumn('products', 'product_group_id')) {
                $table->foreignId('product_group_id')->nullable()->constrained('product_groups')->nullOnDelete()->after('category_id');
            }
            if (!Schema::hasColumn('products', 'stock_qty')) {
                $table->integer('stock_qty')->default(0)->after('product_group_id');
            }
            if (!Schema::hasColumn('products', 'current_stock')) {
                $table->integer('current_stock')->default(0)->after('stock_qty');
            }
            if (!Schema::hasColumn('products', 'akl_akd')) {
                $table->string('akl_akd')->nullable()->after('current_stock');
            }
            if (!Schema::hasColumn('products', 'akl_reg_no')) {
                $table->string('akl_reg_no')->nullable()->after('akl_akd');
            }
            if (!Schema::hasColumn('products', 'expired_registration')) {
                $table->date('expired_registration')->nullable()->after('akl_reg_no');
            }
            if (!Schema::hasColumn('products', 'general_name')) {
                $table->string('general_name')->nullable()->after('expired_registration');
            }
            if (!Schema::hasColumn('products', 'licence_number')) {
                $table->string('licence_number')->nullable()->after('general_name');
            }
            if (!Schema::hasColumn('products', 'listing_level')) {
                $table->string('listing_level')->nullable()->after('licence_number');
            }
            if (!Schema::hasColumn('products', 'photos')) {
                $table->json('photos')->nullable()->after('status');
            }
            if (!Schema::hasColumn('products', 'videos')) {
                $table->json('videos')->nullable()->after('photos');
            }
            if (!Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('videos');
            }
            if (!Schema::hasColumn('products', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'sku', 'type', 'barcode', 'manufacture_id', 'category_id',
                'product_group_id', 'stock_qty', 'current_stock', 'akl_akd',
                'akl_reg_no', 'expired_registration', 'general_name',
                'licence_number', 'listing_level', 'photos', 'videos',
                'description', 'deleted_at'
            ]);
        });
    }
};
