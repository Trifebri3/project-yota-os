<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramTimeline extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'program_timelines';

    protected $fillable = [
        'program_id',
        'name',
        'start_date',
        'end_date',
        'is_locked',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_locked' => 'boolean',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function programKerja()
    {
        return $this->hasMany(ProgramKerja::class, 'program_timeline_id');
    }

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'program_timeline_id');
    }
}
