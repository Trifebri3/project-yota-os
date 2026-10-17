<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasUuids;

    protected $table = 'inventory_logs';

    const UPDATED_AT = null;

    protected $fillable = [
        'inventory_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'location',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
