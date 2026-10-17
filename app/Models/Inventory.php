<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'asset_id',
        'name',
        'category',
        'sub_category',
        'program_id',
        'location',
        'quantity',
        'unit',
        'initial_condition',
        'current_condition',
        'price_per_unit',
        'total_value',
        'purchase_date',
        'funding_source',
        'supplier',
        'pic_id',
        'status_active',
        'status_damaged',
        'status_lost',
        'status_borrowed',
        'description',
        'photo_path',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'price_per_unit' => 'decimal:2',
        'total_value' => 'decimal:2',
        'status_active' => 'boolean',
        'status_damaged' => 'boolean',
        'status_lost' => 'boolean',
        'status_borrowed' => 'boolean',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function logs()
    {
        return $this->hasMany(InventoryLog::class, 'inventory_id');
    }
}
