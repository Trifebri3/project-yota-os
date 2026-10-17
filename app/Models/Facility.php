<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasUuids;

    protected $table = 'facilities';

    protected $fillable = [
        'program_id',
        'name',
        'description',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function allocations()
    {
        return $this->hasMany(UserFacility::class);
    }
}
