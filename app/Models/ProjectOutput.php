<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProjectOutput extends Model
{
    use HasUuids;

    protected $fillable = [
        'program_id',
        'category',
        'title',
        'description',
        'thumbnail',
        'release_date',
        'authors_or_team',
        'evidence_url',
        'ip_number',
        'status',
        'tags',
    ];

    protected $casts = [
        'release_date' => 'date',
        'tags' => 'array',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
