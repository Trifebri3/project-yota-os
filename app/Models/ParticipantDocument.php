<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ParticipantDocument extends Model
{
    use HasUuids;

    protected $table = 'participant_documents';

    protected $fillable = [
        'user_id',
        'program_id',
        'name',
        'file_path',
        'type',
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
