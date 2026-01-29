<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'priority',
        'is_published',
        'publish_date',
        'expire_date',
        'image',
        'created_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'publish_date' => 'date',
        'expire_date' => 'date',
    ];

    /**
     * Get the user who created this announcement.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for published announcements.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('publish_date')
                    ->orWhere('publish_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expire_date')
                    ->orWhere('expire_date', '>=', now());
            });
    }

    /**
     * Scope for active announcements (published and not expired).
     */
    public function scopeActive($query)
    {
        return $query->published()->orderBy('priority', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Get type label.
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'info' => 'Informasi',
            'warning' => 'Peringatan',
            'important' => 'Penting',
            'event' => 'Acara',
            default => $this->type,
        };
    }

    /**
     * Get type color.
     */
    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'info' => 'blue',
            'warning' => 'yellow',
            'important' => 'red',
            'event' => 'purple',
            default => 'gray',
        };
    }

    /**
     * Get priority label.
     */
    public function getPriorityLabelAttribute()
    {
        return match($this->priority) {
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            default => $this->priority,
        };
    }
}
