<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scm_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_do_id')->constrained('sales_do')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('scm_drivers');
            $table->date('delivery_date')->nullable();
            $table->time('departure_time')->nullable();
            $table->time('arrival_time')->nullable();
            $table->enum('delivery_status', ['scheduled', 'on_route', 'delivered', 'failed', 'cancelled'])->default('scheduled');
            $table->string('receiver_name', 200)->nullable();
            $table->string('receiver_position', 100)->nullable();
            $table->timestamp('received_at')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('sales_do_id');
            $table->index('driver_id');
            $table->index('delivery_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scm_deliveries');
    }
};
