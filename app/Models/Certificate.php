<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'program_id',
        'user_id',
        'certificate_number',
        'issue_date',
        'download_path',
        'verification_code',
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
