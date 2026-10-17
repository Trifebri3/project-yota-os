<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasUuids;

    protected $table = 'complaints';

    protected $fillable = [
        'user_id',
        'program_id',
        'category',
        'title',
        'description',
        'photo_path',
        'status',
        'response',
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
