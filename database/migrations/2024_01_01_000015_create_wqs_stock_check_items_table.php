<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wqs_stock_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_do_id')
                  ->constrained('sales_do')
                  ->onDelete('cascade');

            // Check info
            $table->date('check_date');
            $table->enum('overall_status', ['pending', 'checked', 'completed', 'failed'])
                  ->default('pending');

            $table->text('check_notes')->nullable();
            $table->text('notes')->nullable();

            // Tracking
            $table->foreignId('checked_by')
                  ->constrained('users')
                  ->onDelete('restrict');
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['sales_do_id']);
            $table->index(['overall_status']);
            $table->index(['check_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wqs_stock_checks');
    }
};
