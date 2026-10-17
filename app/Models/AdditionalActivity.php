<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AdditionalActivity extends Model
{
    use HasUuids;

    protected $table = 'additional_activities';

    protected $fillable = [
        'user_id',
        'program_id',
        'name',
        'description',
        'date',
        'hours_spent',
        'photo_path',
        'status',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
        'hours_spent' => 'float',
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
