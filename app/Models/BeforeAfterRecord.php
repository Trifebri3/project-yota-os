<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BeforeAfterRecord extends Model
{
    use HasUuids;

    protected $fillable = [
        'program_id',
        'title',
        'location',
        'before_image',
        'after_image',
        'before_date',
        'after_date',
        'before_description',
        'after_description',
        'metric_impact',
    ];

    protected $casts = [
        'before_date' => 'date',
        'after_date' => 'date',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
