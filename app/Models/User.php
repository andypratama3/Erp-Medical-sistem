<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'current_branch_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ============ RELATIONSHIPS ============

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'user_branches')
            ->withPivot('is_default')
            ->withTimestamps();
    }

    public function currentBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'current_branch_id');
    }

    // ============ BRANCH ACCESS LOGIC ============

    /**
     * Get the default branch for this user.
     * For owners/superadmins, returns the first active branch.
     */
    public function defaultBranch(): ?Branch
    {
        if ($this->canAccessAllBranches()) {
            return Branch::active()->first();
        }

        return $this->branches()
            ->wherePivot('is_default', true)
            ->first();
    }

    /**
     * Get all branches this user can access.
     * Respects role-based access control.
     */
    public function accessibleBranches(): Builder
    {
        if ($this->canAccessAllBranches()) {
            return Branch::query()->active();
        }

        return Branch::query()
            ->active()
            ->whereIn(
                'master_branches.id', // ⬅️ PENTING
                $this->branches()->select('master_branches.id')
            );
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }

    // Add accessor
    public function getUnreadNotificationsCountAttribute()
    {
        return $this->unreadNotifications()->count();
    }



    /**
     * Get the current branch ID, with fallback to default.
     */
    public function getCurrentBranchId(): ?int
    {
        return $this->current_branch_id ?? $this->defaultBranch()?->id;
    }

    // ============ ROLE CHECKS ============

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function isSuperadmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    public function canAccessAllBranches(): bool
    {
        return $this->isOwner() || $this->isSuperadmin();
    }

    // ============ BRANCH SWITCHING ============

    /**
     * Attempt to switch to a different branch.
     */
    public function switchBranch(int $branchId): bool
    {
        // Verify user has access to this branch
        if (!$this->hasAccessToBranch($branchId)) {
            return false;
        }

        $this->update(['current_branch_id' => $branchId]);
        return true;
    }

    /**
     * Check if user has access to a specific branch.
     */
    public function hasAccessToBranch(int $branchId): bool
    {
        if ($this->canAccessAllBranches()) {
            return Branch::find($branchId)?->isActive() ?? false;
        }

        return $this->branches()->where('branch_id', $branchId)->exists();
    }

    // ============ SCOPES ============

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
