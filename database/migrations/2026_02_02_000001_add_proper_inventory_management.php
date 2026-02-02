<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration adds proper inventory tracking to the system
     * with support for multi-branch stock management
     */
    public function up(): void
    {
        // Add stock_quantity to master_products for total inventory
        if (!Schema::hasColumn('master_products', 'stock_quantity')) {
            Schema::table('master_products', function (Blueprint $table) {
                $table->integer('stock_quantity')->default(0)->after('max_stock');
                $table->integer('reserved_quantity')->default(0)->after('stock_quantity');
                $table->integer('available_quantity')->default(0)->after('reserved_quantity');
                
                $table->index('stock_quantity');
                $table->index('available_quantity');
            });
        }

        // Create branch_product_stock table for per-branch inventory
        Schema::create('branch_product_stock', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('branch_id')
                ->constrained('master_branches')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            
            $table->foreignId('product_id')
                ->constrained('master_products')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            
            // Stock quantities
            $table->integer('stock_quantity')->default(0);
            $table->integer('reserved_quantity')->default(0); // Reserved for pending SalesDOs
            $table->integer('available_quantity')->default(0); // Stock - Reserved
            
            // Minimum and maximum levels per branch
            $table->integer('min_stock')->default(0);
            $table->integer('max_stock')->default(0);
            $table->integer('reorder_point')->default(0);
            
            // Location in warehouse
            $table->string('warehouse_location', 100)->nullable();
            
            $table->timestamps();
            
            // Unique constraint: one record per branch per product
            $table->unique(['branch_id', 'product_id'], 'unique_branch_product');
            
            // Indexes for fast queries
            $table->index('branch_id');
            $table->index('product_id');
            $table->index('stock_quantity');
            $table->index('available_quantity');
        });

        // Add indexes to sales_do_items for better performance
        if (!Schema::hasColumn('sales_do_items', 'qty_packed')) {
            Schema::table('sales_do_items', function (Blueprint $table) {
                $table->integer('qty_packed')->default(0)->after('qty_delivered');
                $table->enum('item_status', [
                    'pending',      // Waiting for stock check
                    'confirmed',    // Stock available
                    'packed',       // Items packed for delivery
                    'delivered',    // Items delivered
                    'cancelled'     // Item cancelled
                ])->default('pending')->after('line_total');
                
                $table->index('item_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_do_items', function (Blueprint $table) {
            if (Schema::hasColumn('sales_do_items', 'item_status')) {
                $table->dropIndex(['item_status']);
                $table->dropColumn(['qty_packed', 'item_status']);
            }
        });

        Schema::dropIfExists('branch_product_stock');

        Schema::table('master_products', function (Blueprint $table) {
            if (Schema::hasColumn('master_products', 'stock_quantity')) {
                $table->dropIndex(['stock_quantity']);
                $table->dropIndex(['available_quantity']);
                $table->dropColumn([
                    'stock_quantity',
                    'reserved_quantity',
                    'available_quantity'
                ]);
            }
        });
    }
};
