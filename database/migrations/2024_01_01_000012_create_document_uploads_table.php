<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('documentable_type');
            $table->unsignedBigInteger('documentable_id');
            $table->enum('document_category', [
                'wqs_stock_photo',
                'scm_loading_photo',
                'scm_delivery_proof',
                'scm_signature',
                'act_invoice',
                'act_faktur_pajak',
                'fin_payment_proof',
                'reg_alkes_catalog',
                'reg_alkes_nie',
                'reg_alkes_akl',
                'reg_alkes_scope',
                'other'
            ]);
            $table->string('original_filename', 255);
            $table->string('stored_filename', 255);
            $table->string('file_path', 500);
            $table->string('mime_type', 100);
            $table->bigInteger('file_size')->default(0);
            $table->text('description')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['documentable_type', 'documentable_id']);
            $table->index('document_category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_uploads');
    }
};