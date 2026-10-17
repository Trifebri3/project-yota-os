<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'activity_id',
        'title',
        'description',
        'due_date',
        'status',
        'type',
        'assigned_to',
        'dependencies',
        'checklists',
        'custom_fields',
        'form_id',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'assigned_to' => 'array',
        'dependencies' => 'array',
        'checklists' => 'array',
        'custom_fields' => 'array',
    ];

    public function activity()
    {
        return $this->belongsTo(Kegiatan::class, 'activity_id');
    }

    public function customForm()
    {
        return $this->belongsTo(CustomForm::class, 'form_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'task_id');
    }
}
