<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProgramUserAssignment extends Model
{
    use HasUuids;

    protected $table = 'program_user_assignments';

    protected $fillable = [
        'user_id',
        'program_id',
        'zone_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
