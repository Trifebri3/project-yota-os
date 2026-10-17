<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'signature_path',
        'status',
        'organisasi',
        'photo_profil',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    // Role checks
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super-admin';
    }

    public function isProgramAdmin(): bool
    {
        return $this->role === 'program-admin';
    }

    public function isProjectOfficer(): bool
    {
        return $this->role === 'project-officer';
    }

    public function isSupervisor(): bool
    {
        return $this->role === 'supervisor';
    }

    public function isPeserta(): bool
    {
        return $this->role === 'peserta';
    }

    public function isVolunteer(): bool
    {
        return $this->role === 'volunteer';
    }

    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    // Relationships
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function assignments()
    {
        return $this->hasMany(ProgramUserAssignment::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'pic_id');
    }

    public function subProgramAttendances()
    {
        return $this->hasMany(SubProgramAttendance::class);
    }

    protected function photoProfil(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? (str_starts_with($value, 'http') ? $value : (str_starts_with($value, '/storage/') ? $value : (str_starts_with($value, 'storage/') ? '/' . $value : '/storage/' . ltrim($value, '/')))) : null
        );
    }

    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? (str_starts_with($value, 'http') ? $value : (str_starts_with($value, '/storage/') ? $value : (str_starts_with($value, 'storage/') ? '/' . $value : '/storage/' . ltrim($value, '/')))) : null
        );
    }

    protected function signaturePath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? (str_starts_with($value, 'http') ? $value : (str_starts_with($value, '/storage/') ? $value : (str_starts_with($value, 'storage/') ? '/' . $value : '/storage/' . ltrim($value, '/')))) : null
        );
    }
}
