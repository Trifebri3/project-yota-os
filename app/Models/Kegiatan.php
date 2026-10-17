<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kegiatan extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'kegiatan';

    protected $fillable = [
        'work_plan_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'zone_id',
        'program_timeline_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function workPlan()
    {
        return $this->belongsTo(ProgramKerja::class, 'work_plan_id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'activity_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'activity_id');
    }

    public function timeline()
    {
        return $this->belongsTo(ProgramTimeline::class, 'program_timeline_id');
    }
}
