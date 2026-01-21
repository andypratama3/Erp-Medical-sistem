<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reg_alkes_case_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('reg_alkes_cases')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('master_products');
            $table->string('product_name', 255);
            $table->string('catalog_number', 100)->nullable();
            $table->string('akl_akd_number', 100)->nullable();
            $table->date('akl_akd_expiry')->nullable();
            $table->enum('registration_type', ['AKL', 'AKD'])->nullable();
            $table->enum('item_status', ['pending', 'registered', 'active', 'expired'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('case_id');
            $table->index('product_id');
            $table->index('item_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reg_alkes_case_items');
    }
};