<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reg_alkes_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_number', 50)->unique();
            $table->foreignId('manufacture_id')->constrained('master_manufactures');
            $table->string('manufacture_name', 255);
            $table->string('country_of_origin', 100);
            $table->enum('case_type', ['pqp', 'hrl', 'renewal'])->default('pqp');
            $table->date('submission_date')->nullable();
            $table->date('target_date')->nullable();
            $table->date('nie_issued_date')->nullable();
            $table->string('nie_number', 100)->nullable();
            $table->enum('case_status', [
                'case_draft',
                'case_submitted',
                'waiting_nie',
                'nie_issued',
                'sku_imported',
                'sku_active',
                'cancelled'
            ])->default('case_draft');
            $table->text('notes')->nullable();
            $table->integer('total_skus')->default(0);
            $table->integer('imported_skus')->default(0);
            $table->integer('active_skus')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();

            $table->index('case_number');
            $table->index('manufacture_id');
            $table->index('case_status');
            $table->index('case_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reg_alkes_cases');
    }
};