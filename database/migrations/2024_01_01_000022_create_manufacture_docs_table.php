<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manufacture_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacture_id')->constrained('master_manufactures')->cascadeOnDelete();
            $table->enum('doc_type', ['catalog', 'ce_certificate', 'iso_certificate', 'authorization_letter', 'other'])->default('catalog');
            $table->string('document_name', 255);
            $table->string('file_path', 500);
            $table->string('file_type', 50);
            $table->bigInteger('file_size')->default(0);
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('manufacture_id');
            $table->index('doc_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manufacture_docs');
    }
};