<?php

namespace App\Models;

use App\Traits\HasBranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphMany};
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class TaskBoard extends Model
{
    use HasFactory, SoftDeletes, HasBranchScope;

    protected $table = 'task_boards';

    protected $fillable = [
        'branch_id',
        'sales_do_id',
        'module',
        'task_type',
        'task_status',
        'task_description',
        'priority',
        'due_date',
        'assigned_to',
        'started_at',
        'completed_at',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /* ================= CONSTANTS ================= */

    public const MODULES = [
        'crm' => 'CRM',
        'wqs' => 'WQS',
        'scm' => 'SCM',
        'act' => 'ACT',
        'fin' => 'FIN',
    ];

    public const STATUSES = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'on_hold' => 'On Hold',
        'completed' => 'Completed',
        'rejected' => 'Rejected',
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    public const TASK_TYPES = [
        // WQS Module
        'wqs_stock_check' => 'Stock Check',
        'wqs_quality_review' => 'Quality Review',
        'wqs_document_verify' => 'Document Verification',

        // SCM Module
        'scm_pick_pack' => 'Pick & Pack',
        'scm_shipping_prep' => 'Shipping Preparation',
        'scm_delivery' => 'Delivery',

        // ACT Module
        'act_invoice_prep' => 'Invoice Preparation',
        'act_invoice_issue' => 'Invoice Issue',

        // FIN Module
        'fin_payment_track' => 'Payment Tracking',
        'fin_collection' => 'Collection',
        'fin_aging_monitor' => 'Aging Monitoring',
    ];

    /* ================= RELATIONSHIPS ================= */

    public function salesDO(): BelongsTo
    {
        return $this->belongsTo(SalesDO::class, 'sales_do_id', 'id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(DocumentUpload::class, 'documentable');
    }

    /* ================= SCOPES ================= */

    public function scopeByModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('task_status', $status);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopePending($query)
    {
        return $query->where('task_status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('task_status', 'in_progress');
    }

    public function scopeOnHold($query)
    {
        return $query->where('task_status', 'on_hold');
    }

    public function scopeCompleted($query)
    {
        return $query->where('task_status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                     ->whereNotIn('task_status', ['completed', 'rejected']);
    }

    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeNotCompleted($query)
    {
        return $query->whereNotIn('task_status', ['completed', 'rejected']);
    }

    /* ================= ACCESSORS ================= */

    public function getModuleLabelAttribute(): string
    {
        return self::MODULES[$this->module] ?? $this->module;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->task_status] ?? $this->task_status;
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITIES[$this->priority] ?? 'Medium';
    }

    public function getTaskTypeLabelAttribute(): string
    {
        return self::TASK_TYPES[$this->task_type] ?? $this->task_type;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->task_status) {
            'pending' => 'yellow',
            'in_progress' => 'blue',
            'on_hold' => 'red',
            'completed' => 'green',
            'rejected' => 'gray',
            default => 'gray',
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'urgent' => 'red',
            default => 'gray',
        };
    }

    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->due_date) {
            return null;
        }

        return $this->due_date->diffInDays(now(), false);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->days_until_due !== null && $this->days_until_due < 0
               && !in_array($this->task_status, ['completed', 'rejected']);
    }

    public function getIsUrgentAttribute(): bool
    {
        return $this->priority === 'urgent' || $this->is_overdue;
    }

    public function getDurationInHoursAttribute(): ?int
    {
        if (!$this->started_at || !$this->completed_at) {
            return null;
        }

        return $this->completed_at->diffInHours($this->started_at);
    }

    /* ================= BUSINESS LOGIC ================= */

    /**
     * Start task
     */
    public function start(): bool
    {
        if ($this->task_status !== 'pending') {
            return false;
        }

        return $this->update([
            'task_status' => 'in_progress',
            'started_at' => now(),
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Complete task
     */
    public function complete(): bool
    {
        if (!in_array($this->task_status, ['in_progress', 'on_hold'])) {
            return false;
        }

        return $this->update([
            'task_status' => 'completed',
            'completed_at' => now(),
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Hold task
     */
    public function hold(string $reason = ''): bool
    {
        if ($this->task_status === 'completed' || $this->task_status === 'rejected') {
            return false;
        }

        $notes = "HOLD: {$reason} - " . now()->format('Y-m-d H:i:s');

        return $this->update([
            'task_status' => 'on_hold',
            'notes' => ($this->notes ?? '') . "\n" . $notes,
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Reject task
     */
    public function reject(string $reason = ''): bool
    {
        if ($this->task_status === 'completed' || $this->task_status === 'rejected') {
            return false;
        }

        $notes = "REJECTED: {$reason} - " . now()->format('Y-m-d H:i:s');

        return $this->update([
            'task_status' => 'rejected',
            'notes' => ($this->notes ?? '') . "\n" . $notes,
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Resume from hold
     */
    public function resume(): bool
    {
        if ($this->task_status !== 'on_hold') {
            return false;
        }

        return $this->update([
            'task_status' => 'in_progress',
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Check if task can be started
     */
    public function canStart(): bool
    {
        return $this->task_status === 'pending';
    }

    /**
     * Check if task can be completed
     */
    public function canComplete(): bool
    {
        return in_array($this->task_status, ['in_progress', 'on_hold']);
    }

    /**
     * Check if task can be held
     */
    public function canHold(): bool
    {
        return !in_array($this->task_status, ['completed', 'rejected']);
    }

    /**
     * Check if task can be resumed
     */
    public function canResume(): bool
    {
        return $this->task_status === 'on_hold';
    }

    /**
     * Get module color for UI
     */
    public static function getModuleColor(string $module): string
    {
        return match($module) {
            'crm' => 'yellow',
            'wqs' => 'blue',
            'scm' => 'indigo',
            'act' => 'purple',
            'fin' => 'orange',
            default => 'gray',
        };
    }

    /**
     * Get tasks for dashboard
     */
    public static function getDashboardStats(string $module = null)
    {
        $query = self::notCompleted();

        if ($module) {
            $query->byModule($module);
        }

        return [
            'pending' => (clone $query)->pending()->count(),
            'in_progress' => (clone $query)->inProgress()->count(),
            'on_hold' => (clone $query)->onHold()->count(),
            'overdue' => (clone $query)->overdue()->count(),
        ];
    }

    /* ================= EVENTS ================= */

    protected static function booted()
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by ??= auth()->id();
                $model->priority ??= 'medium';
                $model->task_status ??= 'pending';
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }
}
