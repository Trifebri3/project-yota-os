<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomForm extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'fields',
        'target_type',
    ];

    protected $casts = [
        'fields' => 'array',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'form_id');
    }
}
