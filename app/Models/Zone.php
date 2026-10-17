<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'geojson',
        'photo_zona',
        'status_zona',
        'program_id',
    ];

    protected $casts = [
        'geojson' => 'array',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function activities()
    {
        return $this->hasMany(Kegiatan::class, 'zone_id');
    }

    public function assignments()
    {
        return $this->hasMany(ProgramUserAssignment::class);
    }
}
