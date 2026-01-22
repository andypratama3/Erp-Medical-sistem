<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    MorphTo,
    BelongsTo
};
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentUpload extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'document_category',
        'original_filename',
        'stored_filename',
        'file_path',
        'mime_type',
        'file_size',
        'description',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        return match (true) {
            $this->file_size >= 1_073_741_824 =>
                number_format($this->file_size / 1_073_741_824, 2) . ' GB',
            $this->file_size >= 1_048_576 =>
                number_format($this->file_size / 1_048_576, 2) . ' MB',
            $this->file_size >= 1024 =>
                number_format($this->file_size / 1024, 2) . ' KB',
            default => $this->file_size . ' bytes',
        };
    }
}
