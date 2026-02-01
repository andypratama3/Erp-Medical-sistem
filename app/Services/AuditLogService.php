<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Log an audit entry (Flexible method - supports both old and new parameter styles)
     *
     * Old style: log(module, action, modelType, modelId, description, oldValues, newValues)
     * New style: log(entity_type: ..., entity_id: ..., action: ..., description: ..., old_values: ..., new_values: ...)
     *
     * @param string $param1 Module name OR Entity type
     * @param string|int|null $param2 Action OR Entity ID
     * @param string|array|null $param3 Model type OR Action
     * @param int|string|null $param4 Model ID OR Description
     * @param string|array|null $param5 Description OR Old values
     * @param array|null $param6 Old values OR New values
     * @param array|null $param7 New values
     * @return AuditLog
     */
    public function log(
        string $param1,
        string|int|null $param2 = null,
        string|array|null $param3 = null,
        int|string|null $param4 = null,
        string|array|null $param5 = null,
        ?array $param6 = null,
        ?array $param7 = null
    ): AuditLog {
        // Detect which style is being used
        // If param3 is an array, it's the new style (entity_type, entity_id, action, description, old_values, new_values)
        // If param3 is a string, it's the old style (module, action, modelType, modelId, description, oldValues, newValues)

        if (is_array($param3) || $param7 !== null) {
            // Old style: (module, action, modelType, modelId, description, oldValues, newValues)
            return $this->logOldStyle(
                module: $param1,
                action: (string)$param2,
                modelType: is_array($param3) ? '' : (string)$param3,
                modelId: is_int($param4) ? $param4 : null,
                description: is_string($param5) ? $param5 : '',
                oldValues: is_array($param6) ? $param6 : [],
                newValues: is_array($param7) ? $param7 : []
            );
        } else {
            // New style: (entity_type, entity_id, action, description, old_values, new_values)
            return $this->logNewStyle(
                entityType: $param1,
                entityId: is_int($param2) ? $param2 : null,
                action: is_string($param3) ? $param3 : '',
                description: is_string($param4) ? $param4 : '',
                oldValues: is_array($param5) ? $param5 : null,
                newValues: is_array($param6) ? $param6 : null
            );
        }
    }

    /**
     * Old style logging (with module parameter)
     */
    protected function logOldStyle(
        string $module,
        string $action,
        string $modelType,
        ?int $modelId = null,
        string $description = '',
        array $oldValues = [],
        array $newValues = []
    ): AuditLog {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'module' => $module,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'old_values' => !empty($oldValues) ? $oldValues : null,
            'new_values' => !empty($newValues) ? $newValues : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * New style logging (entity-based, auto-detect module)
     */
    protected function logNewStyle(
        string $entityType,
        ?int $entityId = null,
        string $action = '',
        string $description = '',
        ?array $oldValues = null,
        ?array $newValues = null
    ): AuditLog {
        // Auto-detect module from entity type
        $module = $this->detectModule($entityType);

        return AuditLog::create([
            'user_id' => Auth::id(),
            'module' => $module,
            'action' => $action,
            'model_type' => $entityType,
            'model_id' => $entityId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Detect module from entity type
     */
    protected function detectModule(string $entityType): string
    {
        // Remove namespace and get class name
        $className = class_basename($entityType);

        // Detect module based on class name prefix
        if (str_starts_with($className, 'SalesDO')) {
            return 'crm';
        } elseif (str_starts_with($className, 'WQS')) {
            return 'wqs';
        } elseif (str_starts_with($className, 'SCM')) {
            return 'scm';
        } elseif (str_starts_with($className, 'ACT')) {
            return 'act';
        } elseif (str_starts_with($className, 'FIN')) {
            return 'fin';
        } elseif (str_starts_with($className, 'RegAlkes')) {
            return 'reg_alkes';
        } elseif (in_array($className, ['Product', 'Customer', 'Vendor', 'Manufacture', 'MasterOffice', 'MasterDepartment', 'Tax', 'PaymentTerm', 'Branch'])) {
            return 'master';
        } elseif (in_array($className, ['User', 'Role', 'Permission'])) {
            return 'auth';
        }

        return 'system';
    }

    /**
     * Log create action
     */
    public function logCreate(string $module, $model, string $description = ''): AuditLog
    {
        return $this->logOldStyle(
            module: $module,
            action: 'create',
            modelType: get_class($model),
            modelId: $model->id,
            description: $description ?: "Created new " . class_basename($model),
            newValues: $model->toArray()
        );
    }

    /**
     * Log update action
     */
    public function logUpdate(string $module, $model, array $originalData, string $description = ''): AuditLog
    {
        $changes = $this->getChanges($originalData, $model->toArray());

        return $this->logOldStyle(
            module: $module,
            action: 'update',
            modelType: get_class($model),
            modelId: $model->id,
            description: $description ?: "Updated " . class_basename($model) . " #" . $model->id,
            oldValues: $originalData,
            newValues: $model->toArray()
        );
    }

    /**
     * Log delete action
     */
    public function logDelete(string $module, $model, string $description = ''): AuditLog
    {
        return $this->logOldStyle(
            module: $module,
            action: 'delete',
            modelType: get_class($model),
            modelId: $model->id,
            description: $description ?: "Deleted " . class_basename($model) . " #" . $model->id,
            oldValues: $model->toArray()
        );
    }

    /**
     * Log view action
     */
    public function logView(string $module, $model, string $description = ''): AuditLog
    {
        return $this->logOldStyle(
            module: $module,
            action: 'view',
            modelType: get_class($model),
            modelId: $model->id,
            description: $description ?: "Viewed " . class_basename($model) . " #" . $model->id
        );
    }

    /**
     * Log custom action
     */
    public function logAction(
        string $module,
        string $action,
        $model,
        string $description = '',
        array $additionalData = []
    ): AuditLog {
        return $this->logOldStyle(
            module: $module,
            action: $action,
            modelType: get_class($model),
            modelId: $model->id,
            description: $description,
            newValues: $additionalData
        );
    }

    /**
     * Get changes between old and new data
     */
    protected function getChanges(array $oldData, array $newData): array
    {
        $changes = [];

        foreach ($newData as $key => $value) {
            if (!isset($oldData[$key]) || $oldData[$key] !== $value) {
                $changes[$key] = [
                    'old' => $oldData[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        return $changes;
    }

    /**
     * Get audit logs for a specific model
     */
    public function getLogsForModel($model, int $limit = 50)
    {
        return AuditLog::where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent audit logs for a module
     */
    public function getRecentLogs(string $module, int $limit = 50)
    {
        return AuditLog::where('module', $module)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get audit logs by user
     */
    public function getLogsByUser(int $userId, int $limit = 50)
    {
        return AuditLog::where('user_id', $userId)
            ->with('user')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get all audit logs with filters
     */
    public function getAllLogs(array $filters = [], int $perPage = 20)
    {
        $query = AuditLog::with('user');

        if (isset($filters['module'])) {
            $query->where('module', $filters['module']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['model_type'])) {
            $query->where('model_type', $filters['model_type']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Clean old audit logs (older than specified days)
     */
    public function cleanOldLogs(int $olderThanDays = 365): int
    {
        return AuditLog::where('created_at', '<', now()->subDays($olderThanDays))->delete();
    }

    /**
     * Get statistics for a module
     */
    public function getModuleStats(string $module, int $days = 30): array
    {
        $startDate = now()->subDays($days);

        $stats = AuditLog::where('module', $module)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->get()
            ->pluck('count', 'action')
            ->toArray();

        return [
            'total' => array_sum($stats),
            'by_action' => $stats,
            'period_days' => $days,
        ];
    }
}
