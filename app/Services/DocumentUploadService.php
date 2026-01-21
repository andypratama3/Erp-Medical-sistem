<?php

namespace App\Services;

use App\Models\DocumentUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentUploadService
{
    public function upload(
        UploadedFile $file,
        string $documentableType,
        int $documentableId,
        string $category,
        ?string $description = null
    ): DocumentUpload {
        $originalFilename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $storedFilename = Str::uuid() . '.' . $extension;

        $basePath = $this->getBasePath($category);
        $filePath = $file->storeAs($basePath, $storedFilename);

        return DocumentUpload::create([
            'documentable_type' => $documentableType,
            'documentable_id' => $documentableId,
            'document_category' => $category,
            'original_filename' => $originalFilename,
            'stored_filename' => $storedFilename,
            'file_path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'description' => $description,
            'uploaded_by' => auth()->id(),
        ]);
    }

    protected function getBasePath(string $category): string
    {
        return match($category) {
            'wqs_stock_photo' => 'documents/sales_do/wqs',
            'scm_loading_photo', 'scm_delivery_proof', 'scm_signature' => 'documents/sales_do/scm',
            'act_invoice', 'act_faktur_pajak' => 'documents/sales_do/act',
            'fin_payment_proof' => 'documents/sales_do/fin',
            'reg_alkes_catalog', 'reg_alkes_nie', 'reg_alkes_akl', 'reg_alkes_scope' => 'documents/reg_alkes',
            default => 'documents/other',
        };
    }
}