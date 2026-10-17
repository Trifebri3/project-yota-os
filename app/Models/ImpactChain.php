<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ImpactChain extends Model
{
    use HasUuids;

    protected $fillable = [
        'program_id',
        'stage',
        'stage_order',
        'title',
        'description',
        'metric_value',
        'metric_unit',
        'target_value',
        'evidence_note',
        'verification_status',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
