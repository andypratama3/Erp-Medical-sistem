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
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('price_code')->unique();
            $table->decimal('buy_price', 15, 2)->comment('Harga beli - hanya untuk PQP, Finance, Admin & Superadmin');
            $table->decimal('sell_price', 15, 2)->comment('Harga jual - untuk CRM & MPR');
            $table->decimal('margin', 15, 2)->nullable()->comment('Margin keuntungan');
            $table->decimal('discount', 15, 2)->nullable()->default(0)->comment('Diskon dalam persen');
            $table->date('effective_date')->nullable()->comment('Tanggal efektif harga');
            $table->date('expired_date')->nullable()->comment('Tanggal kadaluarsa harga');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('product_id');
            $table->index('price_code');
            $table->index('status');
            $table->index('effective_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_lists');
    }
};
