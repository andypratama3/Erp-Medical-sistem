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
        Schema::create('master_discount_policies', function (Blueprint $table) {
            $table->id();
            $table->string('department_code');
            $table->string('level_name');
            $table->string('segment');
            $table->decimal('max_discount_percent', 5, 2);
            $table->text('notes')->nullable();
            $table->string('status');
            $table->timestamps();

            $table->index('department_code');
            $table->index('level_name');
            $table->index('segment');
            $table->index('max_discount_percent');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_discount_policies');
    }
};
