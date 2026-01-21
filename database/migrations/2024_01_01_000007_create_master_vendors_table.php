<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->string('legal_name', 255)->nullable();
            $table->string('npwp', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('contact_person', 200)->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->foreignId('payment_term_id')->nullable()->constrained('master_payment_terms');
            $table->enum('vendor_type', ['manufacturer', 'distributor', 'supplier', 'service_provider', 'other'])->default('distributor');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('code');
            $table->index('vendor_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_vendors');
    }
};