<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasUuids;

    protected $table = 'daily_reports';

    protected $fillable = [
        'program_id',
        'date',
        'issues',
        'risks',
        'roadblocks',
        'recommendations',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
