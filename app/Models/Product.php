<?php

namespace App\Models;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $table = 'master_products';

    protected $fillable = [
        'category_id',
        'product_group_id',
        'manufacture_id',
        'sku',
        'name',
        'description',
        'unit',
        'unit_price',
        'cost_price',
        'min_stock',
        'max_stock',
        'barcode',
        'product_type',
        'is_taxable',
        'is_importable',
        'status',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'is_taxable' => 'boolean',
        'is_importable' => 'boolean',
        'status' => 'string',
        'product_type' => 'string',
    ];

    // media collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product_images')
            ->useDisk('public')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif'])
            ->withResponsiveImages();

        $this->addMediaCollection('product_videos')
            ->useDisk('public')
            ->acceptsMimeTypes(['video/mp4', 'video/mov'])
            ->singleFile();
    }

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productGroup(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function manufacture(): BelongsTo
    {
        return $this->belongsTo(Manufacture::class, 'manufacture_id');
    }

    public function salesDOItems(): HasMany
    {
        return $this->hasMany(SalesDOItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeMedicalDevice($query)
    {
        return $query->where('product_type', 'medical_device');
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->unit_price, 0, ',', '.');
    }

    public function getMarginAttribute(): float
    {
        if ($this->cost_price == 0) return 0;
        return (($this->unit_price - $this->cost_price) / $this->cost_price) * 100;
    }
}
