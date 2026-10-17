<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BudgetCeiling extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'program_id',
        'source',
        'amount',
        'notes',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function transactions()
    {
        return $this->hasMany(FinanceTransaction::class);
    }
}
