<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RoutineActivityLog extends Model
{
    use HasUuids;

    protected $table = 'routine_activity_logs';

    protected $fillable = [
        'user_id',
        'routine_activity_id',
        'date',
        'photo_path',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function routineActivity()
    {
        return $this->belongsTo(RoutineActivity::class);
    }
}
