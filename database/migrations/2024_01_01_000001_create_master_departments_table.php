<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained('master_offices')->cascadeOnDelete();
            $table->string('code', 50)->unique();
            $table->string('name', 200);
            $table->string('head_name', 200)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();

            $table->index('code');
            $table->index('office_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_departments');
    }
};