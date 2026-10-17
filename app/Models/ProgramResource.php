<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ProgramResource extends Model
{
    use HasUuids;

    protected $table = 'program_resources';

    protected $fillable = [
        'program_id',
        'name',
        'file_path',
        'type',
        'distribution',
        'assigned_user_id',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
