<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'program_id',
        'title',
        'description',
        'created_by',
        'type',
        'collaborators',
        'status',
        'verification_status',
        'photo_path',
        'notes',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'collaborators' => 'array',
        'completed_at' => 'datetime',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
