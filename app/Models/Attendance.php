<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Attendance extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'activity_id',
        'check_in_time',
        'check_out_time',
        'check_in_gps',
        'check_out_gps',
        'check_in_selfie',
        'check_out_selfie',
        'check_in_status',
        'check_out_status',
        'reason',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'check_in_gps' => 'array',
        'check_out_gps' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(Kegiatan::class, 'activity_id');
    }

    protected function checkInSelfie(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? (str_starts_with($value, 'http') ? $value : (str_starts_with($value, '/storage/') ? $value : (str_starts_with($value, 'storage/') ? '/' . $value : '/storage/' . ltrim($value, '/')))) : null
        );
    }

    protected function checkOutSelfie(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? (str_starts_with($value, 'http') ? $value : (str_starts_with($value, '/storage/') ? $value : (str_starts_with($value, 'storage/') ? '/' . $value : '/storage/' . ltrim($value, '/')))) : null
        );
    }
}
