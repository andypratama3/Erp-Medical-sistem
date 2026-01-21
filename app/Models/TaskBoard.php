<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskBoard extends Model
{
    protected $fillable = [
        'sales_do_id',
        'module',
        'task_status',
        'task_description',
        'due_date',
        'assigned_to',
        'started_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'module' => 'string',
        'task_status' => 'string',
    ];

    // Relationships
    public function salesDO(): BelongsTo
    {
        return $this->belongsTo(SalesDO::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('task_status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('task_status', 'in_progress');
    }

    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }
}