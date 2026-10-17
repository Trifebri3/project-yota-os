<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SubProgramAttendance extends Model
{
    use HasUuids;

    protected $table = 'sub_program_attendances';

    protected $fillable = [
        'user_id',
        'program_id',
        'program_kerja_id',
        'kegiatan_id',
        'scan_time',
        'latitude',
        'longitude',
        'device_id',
        'status',
        'reason',
        'photo_path',
    ];

    protected $casts = [
        'scan_time' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function subProgram()
    {
        return $this->belongsTo(ProgramKerja::class, 'program_kerja_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }
}
