<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Documentation extends Model
{
    use HasUuids;

    protected $table = 'documentations';

    protected $fillable = [
        'user_id',
        'program_id',
        'program_kerja_id',
        'activity_id',
        'file_path',
        'user_name',
        'program_name',
        'sub_program_name',
        'timestamp',
        'latitude',
        'longitude',
        'device_info',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
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

    public function activity()
    {
        return $this->belongsTo(Kegiatan::class, 'activity_id');
    }
}
