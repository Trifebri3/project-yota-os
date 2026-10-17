<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramKerja extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'program_kerja';

    protected $fillable = [
        'program_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'budget',
        'status',
        'program_timeline_id',
        'tahapan',
        'target_outputs',
        'qr_code',
        'attendance_token',
        'latitude',
        'longitude',
        'radius_meters',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'target_outputs' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius_meters' => 'integer',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'work_plan_id');
    }

    public function timeline()
    {
        return $this->belongsTo(ProgramTimeline::class, 'program_timeline_id');
    }

    public function attendances()
    {
        return $this->hasMany(SubProgramAttendance::class, 'program_kerja_id');
    }
}
