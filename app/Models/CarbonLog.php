<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarbonLog extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'carbon_logs';

    protected $fillable = [
        'user_id',
        'program_id',
        'form_id',
        'type',
        'date',
        'content_data',
        'emissions',
        'offsets',
        'net_balance',
    ];

    protected $casts = [
        'date' => 'date',
        'content_data' => 'array',
        'emissions' => 'float',
        'offsets' => 'float',
        'net_balance' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function customForm()
    {
        return $this->belongsTo(CustomForm::class, 'form_id');
    }
}
