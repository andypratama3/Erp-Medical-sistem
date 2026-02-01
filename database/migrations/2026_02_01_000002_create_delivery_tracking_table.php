<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('scm_deliveries')->onDelete('cascade');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('status');
            $table->text('notes')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['delivery_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_tracking');
    }
};
