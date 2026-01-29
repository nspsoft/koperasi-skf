<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'role_id',
        'phone',
        'avatar',
        'is_active',
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the role relationship
     */
    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get the member profile associated with this user.
     */
    public function member()
    {
        return $this->hasOne(Member::class);
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        // Admin has all permissions
        if ($this->role === 'admin') {
            return true;
        }

        // Check via role model if available
        if ($this->role_id && $this->roleModel) {
            return $this->roleModel->hasPermission($permission);
        }

        return false;
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is pengurus.
     */
    public function isPengurus(): bool
    {
        return $this->role === 'pengurus';
    }

    /**
     * Check if user is member.
     */
    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    /**
     * Check if user is manager toko.
     */
    public function isManagerToko(): bool
    {
        return $this->role === 'manager_toko';
    }

    /**
     * Check if user has admin access (admin or pengurus).
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['admin', 'pengurus']);
    }

    /**
     * Check if user has store management access (admin, pengurus, or manager_toko).
     */
    public function hasStoreAccess(): bool
    {
        return in_array($this->role, ['admin', 'pengurus', 'manager_toko']);
    }

    /**
     * Get audit logs for this user.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get role label for display
     */
    public function getRoleLabelAttribute(): string
    {
        if ($this->roleModel) {
            return $this->roleModel->label;
        }
        
        return match($this->role) {
            'admin' => 'Administrator',
            'pengurus' => 'Pengurus',
            'manager_toko' => 'Manager Toko',
            'member' => 'Anggota',
            default => ucfirst($this->role ?? 'Unknown'),
        };
    }

    /**
     * Get role color for badge display
     */
    public function getRoleColorAttribute(): string
    {
        if ($this->roleModel) {
            return $this->roleModel->color;
        }
        
        return match($this->role) {
            'admin' => '#ef4444',
            'pengurus' => '#f59e0b',
            'manager_toko' => '#10b981',
            'member' => '#6366f1',
            default => '#6b7280',
        };
    }
}

