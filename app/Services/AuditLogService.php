<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Log an audit entry
     *
     * @param string $module Module name (e.g., 'master', 'crm', 'scm')
     * @param string $action Action performed (e.g., 'create', 'update', 'delete', 'view')
     * @param string $modelType Model class name
     * @param int|null $modelId Model ID
     * @param string $description Human readable description
     * @param array $oldValues Old values before change (for update)
     * @param array $newValues New values after change
     * @return AuditLog
     */
    public function log(
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
     * Log create action
     */
    public function logCreate(string $module, $model, string $description = ''): AuditLog
    {
        return $this->log(
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

        return $this->log(
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
        return $this->log(
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
        return $this->log(
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
        return $this->log(
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
}
