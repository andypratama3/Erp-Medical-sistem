<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'type',
        'unit',
        'barcode',
        'manufacture_id',
        'category_id',
        'product_group_id',
        'stock_qty',
        'current_stock',
        'akl_akd',
        'akl_reg_no',
        'expired_registration',
        'general_name',
        'licence_number',
        'listing_level',
        'status',
        'photos',
        'videos',
        'description',
        'price',
        'cost'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock_qty' => 'integer',
        'current_stock' => 'integer',
        'photos' => 'array',
        'videos' => 'array',
        'expired_registration' => 'date',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function manufacture(): BelongsTo
    {
        return $this->belongsTo(Manufacture::class);
    }

    public function productGroup(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function priceLists(): HasMany
    {
        return $this->hasMany(PriceList::class);
    }

    public function activePriceList()
    {
        return $this->hasOne(PriceList::class)->active()->effective()->latest();
    }

    // Scopes
    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock < stock_qty');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedCostAttribute()
    {
        return 'Rp ' . number_format($this->cost, 0, ',', '.');
    }

    // Audit logging
    protected static function booted()
    {
        static::created(function ($product) {
            AuditLog::log('created', $product, null, $product->toArray(), 'product');
        });

        static::updated(function ($product) {
            AuditLog::log('updated', $product, $product->getOriginal(), $product->getChanges(), 'product');
        });

        static::deleted(function ($product) {
            AuditLog::log('deleted', $product, $product->toArray(), null, 'product');
        });
    }
}
