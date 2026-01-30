<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable 
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_branch_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all branches that this user has access to
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'user_branches')
            ->withPivot('is_default')
            ->withTimestamps();
    }

    /**
     * Get the current active branch for this user
     */
    public function currentBranch()
    {
        return $this->belongsTo(Branch::class, 'current_branch_id');
    }

    /**
     * Get the default branch for this user
     */
    public function defaultBranch()
    {
        return $this->branches()->wherePivot('is_default', true)->first();
    }

    /**
     * Check if user is owner (has all branches access)
     */
    public function isOwner()
    {
        return $this->hasRole('owner');
    }

    /**
     * Switch to a different branch
     */
    public function switchBranch($branchId)
    {
        // If user is owner, allow switching to any branch
        if ($this->isOwner()) {
            $this->update(['current_branch_id' => $branchId]);
            return true;
        }

        // Check if user has access to this branch
        if ($this->branches()->where('branch_id', $branchId)->exists()) {
            $this->update(['current_branch_id' => $branchId]);
            return true;
        }

        return false;
    }

    /**
     * Get current branch ID or default to 1
     */
    public function getCurrentBranchId()
    {
        return $this->current_branch_id ?? $this->defaultBranch()?->id ?? 1;
    }
}
