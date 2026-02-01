<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('master_branches');
            $table->foreignId('product_id')->constrained('master_products');
            $table->enum('adjustment_type', ['increase', 'decrease', 'correction']);
            $table->integer('quantity');
            $table->integer('old_quantity');
            $table->integer('new_quantity');
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
            $table->index('branch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_adjustments');
    }
};
