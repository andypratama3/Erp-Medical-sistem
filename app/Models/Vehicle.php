<?php

namespace App\Models;

use App\Traits\HasBranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes, HasBranchScope;

    protected $fillable = [
        'branch_id',
        'plate_number',
        'brand',
        'model',
        'year',
        'color',
        'capacity_weight',
        'capacity_volume',
        'fuel_type',
        'driver_id',
        'insurance_number',
        'insurance_expiry',
        'tax_expiry',
        'last_service_date',
        'next_service_date',
        'odometer_reading',
        'notes',
        'status',
    ];

    protected $casts = [
        'insurance_expiry' => 'date',
        'tax_expiry' => 'date',
        'last_service_date' => 'date',
        'next_service_date' => 'date',
        'capacity_weight' => 'decimal:2',
        'capacity_volume' => 'decimal:2',
        'odometer_reading' => 'integer',
    ];

    // ============ RELATIONSHIPS ============

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(SCMDriver::class, 'driver_id');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(SCMDelivery::class, 'vehicle_id');
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    // ============ SCOPES ============

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
                    ->whereNull('driver_id');
    }

    public function scopeInMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    // ============ ACCESSORS ============

    public function getFullNameAttribute(): string
    {
        return "{$this->brand} {$this->model} ({$this->plate_number})";
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'active' && !$this->driver_id;
    }

    public function getInsuranceStatusAttribute(): string
    {
        if (!$this->insurance_expiry) {
            return 'no-insurance';
        }

        $daysUntilExpiry = now()->diffInDays($this->insurance_expiry, false);

        if ($daysUntilExpiry < 0) {
            return 'expired';
        } elseif ($daysUntilExpiry <= 30) {
            return 'expiring-soon';
        }

        return 'valid';
    }

    public function getTaxStatusAttribute(): string
    {
        if (!$this->tax_expiry) {
            return 'no-tax';
        }

        $daysUntilExpiry = now()->diffInDays($this->tax_expiry, false);

        if ($daysUntilExpiry < 0) {
            return 'expired';
        } elseif ($daysUntilExpiry <= 30) {
            return 'expiring-soon';
        }

        return 'valid';
    }

    public function getServiceStatusAttribute(): string
    {
        if (!$this->next_service_date) {
            return 'unknown';
        }

        $daysUntilService = now()->diffInDays($this->next_service_date, false);

        if ($daysUntilService < 0) {
            return 'overdue';
        } elseif ($daysUntilService <= 7) {
            return 'due-soon';
        }

        return 'ok';
    }

    // ============ METHODS ============

    public function assignDriver(SCMDriver $driver): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($driver->vehicle_id) {
            return false;
        }

        $this->update(['driver_id' => $driver->id]);
        $driver->update(['vehicle_id' => $this->id]);

        return true;
    }

    public function unassignDriver(): bool
    {
        if (!$this->driver_id) {
            return false;
        }

        $driver = $this->driver;
        $this->update(['driver_id' => null]);
        $driver->update(['vehicle_id' => null]);

        return true;
    }

    public function markForMaintenance(string $reason = null): bool
    {
        return $this->update([
            'status' => 'maintenance',
            'notes' => $reason ? "Maintenance: {$reason}" : $this->notes,
        ]);
    }

    public function markAsActive(): bool
    {
        return $this->update(['status' => 'active']);
    }

    public function calculateAverageFuelConsumption(): float
    {
        $deliveries = $this->deliveries()
            ->where('delivery_status', 'delivered')
            ->whereNotNull('fuel_consumed')
            ->whereNotNull('distance_km')
            ->get();

        if ($deliveries->isEmpty()) {
            return 0;
        }

        $totalFuel = $deliveries->sum('fuel_consumed');
        $totalDistance = $deliveries->sum('distance_km');

        return $totalDistance > 0 ? round($totalFuel / $totalDistance, 2) : 0;
    }
}
