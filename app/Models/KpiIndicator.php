<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class KpiIndicator extends Model
{
    use HasUuids;

    protected $fillable = [
        'program_id',
        'code',
        'name',
        'category',
        'definition',
        'baseline',
        'target',
        'actual',
        'unit',
        'measurement_method',
        'verified_by',
        'status',
        'evidence_doc',
        'graphic_image',
        'trend_data',
        'carbon_breakdown',
    ];

    protected $casts = [
        'baseline' => 'float',
        'target' => 'float',
        'actual' => 'float',
        'trend_data' => 'array',
        'carbon_breakdown' => 'array',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
