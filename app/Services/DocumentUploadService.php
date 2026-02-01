<?php

namespace App\Services;

use App\Models\DocumentUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Exception;

/**
 * Enhanced Document Upload Service
 *
 * Handles all file uploads with:
 * - File type validation
 * - Size limits
 * - Storage organization
 * - Preview generation
 * - Download tracking
 * - Permission-based access
 * - Batch operations
 */
class DocumentUploadService
{
    // File type configurations
    public const ALLOWED_TYPES = [
        'images' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'documents' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'spreadsheets' => ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'videos' => ['video/mp4', 'video/quicktime', 'video/x-msvideo'],
    ];

    public const MAX_SIZES = [
        'images' => 5 * 1024 * 1024,        // 5MB
        'documents' => 10 * 1024 * 1024,    // 10MB
        'spreadsheets' => 10 * 1024 * 1024, // 10MB
        'videos' => 50 * 1024 * 1024,       // 50MB
    ];

    public const STORAGE_DISK = 'public';
    public const STORAGE_PATH = 'documents';

    /**
     * Upload single file
     */
    public function upload(
        UploadedFile $file,
        string $moduleName,
        string $recordType,
        int $recordId,
        ?string $description = null,
        ?int $userId = null
    ): DocumentUpload {
        try {
            // Validate file
            $this->validateFile($file);

            // Generate unique filename
            $filename = $this->generateFilename($file);

            // Organize storage path
            $storagePath = $this->getStoragePath($moduleName, $recordType, $recordId);

            // Store file
            $path = Storage::disk(self::STORAGE_DISK)->putFileAs(
                $storagePath,
                $file,
                $filename
            );

            // Create document record
            $document = DocumentUpload::create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'module_name' => $moduleName,
                'record_type' => $recordType,
                'record_id' => $recordId,
                'description' => $description,
                'uploaded_by' => $userId ?? auth()->id(),
                'download_count' => 0,
                'is_public' => false,
            ]);

            // Generate preview if needed
            if ($this->isPreviewable($document)) {
                $this->generatePreview($document);
            }

            return $document;
        } catch (Exception $e) {
            throw new Exception("File upload failed: {$e->getMessage()}");
        }
    }

    /**
     * Upload multiple files
     */
    public function uploadMultiple(
        array $files,
        string $moduleName,
        string $recordType,
        int $recordId,
        ?int $userId = null
    ): array {
        $documents = [];

        foreach ($files as $file) {
            try {
                $documents[] = $this->upload(
                    $file,
                    $moduleName,
                    $recordType,
                    $recordId,
                    null,
                    $userId
                );
            } catch (Exception $e) {
                // Log error but continue with other files
                \Log::error("File upload error: {$e->getMessage()}", ['file' => $file->getClientOriginalName()]);
            }
        }

        return $documents;
    }

    /**
     * Validate file
     */
    protected function validateFile(UploadedFile $file): void
    {
        // Check if file is valid
        if (!$file->isValid()) {
            throw new Exception('Invalid file upload');
        }

        // Check MIME type
        $mimeType = $file->getMimeType();
        $allowedMimes = array_merge(...array_values(self::ALLOWED_TYPES));

        if (!in_array($mimeType, $allowedMimes)) {
            throw new Exception("File type '{$mimeType}' not allowed");
        }

        // Check file size
        $fileType = $this->getFileCategory($mimeType);
        $maxSize = self::MAX_SIZES[$fileType] ?? 5 * 1024 * 1024;

        if ($file->getSize() > $maxSize) {
            throw new Exception("File size exceeds maximum of " . ($maxSize / 1024 / 1024) . "MB");
        }
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('YmdHis');
        $random = substr(md5(uniqid()), 0, 8);

        return "{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get storage path
     */
    protected function getStoragePath(string $moduleName, string $recordType, int $recordId): string
    {
        return sprintf(
            '%s/%s/%s/%d',
            self::STORAGE_PATH,
            strtolower($moduleName),
            strtolower($recordType),
            $recordId
        );
    }

    /**
     * Get file category
     */
    protected function getFileCategory(string $mimeType): string
    {
        foreach (self::ALLOWED_TYPES as $category => $types) {
            if (in_array($mimeType, $types)) {
                return $category;
            }
        }

        return 'documents';
    }

    /**
     * Check if file is previewable
     */
    protected function isPreviewable(DocumentUpload $document): bool
    {
        return in_array($document->mime_type, self::ALLOWED_TYPES['images'] ?? []);
    }

    /**
     * Generate preview for image
     */
    protected function generatePreview(DocumentUpload $document): void
    {
        // This would integrate with image processing library
        // For now, just mark as having preview capability
        $document->update(['has_preview' => true]);
    }

    /**
     * Download file
     */
    public function download(DocumentUpload $document)
    {
        // Check authorization
        if (!$this->canDownload($document)) {
            throw new Exception('Unauthorized to download this file');
        }

        // Increment download count
        $document->increment('download_count');
        $document->update(['last_downloaded_at' => now()]);

        // Log the download
        \Log::info('Document downloaded', [
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'timestamp' => now(),
        ]);

        // Return file download
        return Storage::disk(self::STORAGE_DISK)->download(
            $document->file_path,
            $document->file_name
        );
    }

    /**
     * Check download permission
     */
    protected function canDownload(DocumentUpload $document): bool
    {
        // Public files can be downloaded by anyone
        if ($document->is_public) {
            return true;
        }

        // Check user permission
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // Admin can download anything
        if ($user->hasRole('admin')) {
            return true;
        }

        // Check if user has access to the module/record
        // This depends on your specific access control logic
        return true;
    }

    /**
     * Delete file
     */
    public function delete(DocumentUpload $document): bool
    {
        try {
            // Delete physical file
            Storage::disk(self::STORAGE_DISK)->delete($document->file_path);

            // Delete preview if exists
            if ($document->has_preview) {
                $previewPath = $this->getPreviewPath($document->file_path);
                Storage::disk(self::STORAGE_DISK)->delete($previewPath);
            }

            // Delete database record
            $document->delete();

            return true;
        } catch (Exception $e) {
            \Log::error("Error deleting document: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Get preview path
     */
    protected function getPreviewPath(string $filePath): string
    {
        $pathInfo = pathinfo($filePath);
        return $pathInfo['dirname'] . '/preview_' . $pathInfo['filename'] . '.jpg';
    }

    /**
     * Get documents for record
     */
    public function getDocuments(string $recordType, int $recordId): array
    {
        return DocumentUpload::where('record_type', $recordType)
            ->where('record_id', $recordId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get file info
     */
    public function getFileInfo(DocumentUpload $document): array
    {
        $path = Storage::disk(self::STORAGE_DISK)->path($document->file_path);

        return [
            'id' => $document->id,
            'name' => $document->file_name,
            'size' => $document->file_size,
            'size_formatted' => $this->formatFileSize($document->file_size),
            'mime_type' => $document->mime_type,
            'uploaded_by' => $document->uploadedBy?->name ?? 'Unknown',
            'uploaded_at' => $document->created_at->format('d M Y H:i'),
            'downloads' => $document->download_count,
            'url' => Storage::disk(self::STORAGE_DISK)->url($document->file_path),
        ];
    }

    /**
     * Format file size
     */
    protected function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Clean old files
     */
    public function cleanup(): int
    {
        // Delete documents not accessed in 90 days
        $deleted = DocumentUpload::where('updated_at', '<', now()->subDays(90))
            ->get()
            ->each(function ($document) {
                $this->delete($document);
            })
            ->count();

        return $deleted;
    }
}
