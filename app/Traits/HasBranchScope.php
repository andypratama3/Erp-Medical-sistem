<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasBranchScope
{
    /**
     * Boot the trait
     */
    protected static function bootHasBranchScope(): void
    {
        // Automatically filter by current branch when querying
        static::addGlobalScope('branch', function (Builder $builder) {
            $user = auth()->user();
            
            if ($user && !$user->isOwner()) {
                $builder->where('branch_id', $user->getCurrentBranchId());
            }
        });

        // Automatically set branch_id when creating
        static::creating(function ($model) {
            if (!$model->branch_id) {
                $user = auth()->user();
                if ($user) {
                    $model->branch_id = $user->getCurrentBranchId();
                }
            }
        });
    }

    /**
     * Scope query to specific branch
     */
    public function scopeForBranch(Builder $query, int $branchId): Builder
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope query to all branches (bypass filter) - only for owners
     */
    public function scopeAllBranches(Builder $query): Builder
    {
        return $query->withoutGlobalScope('branch');
    }

    /**
     * Get the branch this record belongs to
     */
    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class);
    }
}
