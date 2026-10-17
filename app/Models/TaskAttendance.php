<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TaskAttendance extends Model
{
    use HasUuids;

    protected $table = 'task_attendances';

    protected $fillable = [
        'user_id',
        'task_id',
        'location',
        'photo_path',
        'time',
    ];

    protected $casts = [
        'time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
