<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class UserFacility extends Model
{
    use HasUuids;

    protected $table = 'user_facilities';

    protected $fillable = [
        'user_id',
        'facility_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}
