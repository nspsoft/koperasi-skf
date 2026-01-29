<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'description'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * User who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent model (polymorphic)
     */
    public function auditable()
    {
        return $this->morphTo('model');
    }

    /**
     * Log an activity
     */
    public static function log($action, $description = null, $model = null, $oldValues = null, $newValues = null)
    {
        $log = static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
        ]);

        // Auto-prune old logs with 2% probability to avoid overhead on every request
        if (rand(1, 100) <= 2) {
            static::pruneOldLogs();
        }

        return $log;
    }

    /**
     * Delete logs older than 1 year (365 days)
     */
    public static function pruneOldLogs()
    {
        try {
            // Keep logs for 365 days
            static::where('created_at', '<', now()->subDays(365))->delete();
        } catch (\Exception $e) {
            // Silently fail to not interrupt user flow
            \Log::warning('Failed to prune audit logs: ' . $e->getMessage());
        }
    }

    /**
     * Get action badge color
     */
    public function getActionColorAttribute()
    {
        return match($this->action) {
            'create' => 'success',
            'update' => 'info',
            'delete' => 'danger',
            'login' => 'primary',
            'logout' => 'secondary',
            'approve' => 'success',
            'reject' => 'danger',
            default => 'secondary',
        };
    }
}
