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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('master_branches')->onDelete('cascade');
            $table->string('plate_number')->unique();
            $table->string('brand');
            $table->string('model');
            $table->integer('year');
            $table->string('color')->nullable();
            $table->decimal('capacity_weight', 10, 2); // in kg
            $table->decimal('capacity_volume', 10, 2)->nullable(); // in m3
            $table->enum('fuel_type', ['gasoline', 'diesel', 'electric', 'hybrid']);
            $table->foreignId('driver_id')->nullable()->constrained('scm_drivers')->nullOnDelete();
            $table->string('insurance_number')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('tax_expiry')->nullable();
            $table->date('last_service_date')->nullable();
            $table->date('next_service_date')->nullable();
            $table->integer('odometer_reading')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['active', 'maintenance', 'inactive','in_use'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'branch_id']);
            $table->index('driver_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
