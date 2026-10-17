<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class StoryMilestone extends Model
{
    use HasUuids;

    protected $fillable = [
        'program_id',
        'day_number',
        'phase',
        'title',
        'subtitle',
        'story_narrative',
        'activity_name',
        'personnel_involved',
        'key_achievement',
        'date',
        'photos',
        'is_published',
        'order',
    ];

    protected $casts = [
        'photos' => 'array',
        'date' => 'date',
        'is_published' => 'boolean',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
