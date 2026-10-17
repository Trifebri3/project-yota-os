<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Logbook extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'program_id',
        'title',
        'content',
        'date',
        'hours_spent',
        'status',
        'photo_path',
    ];

    protected $casts = [
        'date' => 'date',
        'hours_spent' => 'decimal:1',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    protected function photoPath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? (str_starts_with($value, 'http') ? $value : (str_starts_with($value, '/storage/') ? $value : (str_starts_with($value, 'storage/') ? '/' . $value : '/storage/' . ltrim($value, '/')))) : null
        );
    }
}
