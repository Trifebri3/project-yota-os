<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RoutineActivity extends Model
{
    use HasUuids;

    protected $table = 'routine_activities';

    protected $fillable = [
        'program_id',
        'name',
        'start_time',
        'end_time',
        'is_selfie_required',
        'is_notes_required',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function logs()
    {
        return $this->hasMany(RoutineActivityLog::class);
    }
}
