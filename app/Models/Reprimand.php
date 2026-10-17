<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Reprimand extends Model
{
    use HasUuids;

    protected $table = 'reprimands';

    protected $fillable = [
        'user_id',
        'program_id',
        'level',
        'reason',
        'proof_path',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
