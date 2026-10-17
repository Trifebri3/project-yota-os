<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectCharter extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'program_id',
        'vision',
        'mission',
        'background',
        'scope',
        'budget',
        'stakeholder_info',
    ];

    protected $casts = [
        'stakeholder_info' => 'array',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
