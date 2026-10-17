<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class FinanceTransaction extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'program_id',
        'budget_ceiling_id',
        'title',
        'description',
        'amount',
        'type',
        'category',
        'status',
        'spj_document_path',
        'receipt_path',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function budgetCeiling()
    {
        return $this->belongsTo(BudgetCeiling::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    protected function receiptPath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? (str_starts_with($value, 'http') ? $value : (str_starts_with($value, '/storage/') ? $value : (str_starts_with($value, 'storage/') ? '/' . $value : '/storage/' . ltrim($value, '/')))) : null
        );
    }

    protected function spjDocumentPath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? (str_starts_with($value, 'http') ? $value : (str_starts_with($value, '/storage/') ? $value : (str_starts_with($value, 'storage/') ? '/' . $value : '/storage/' . ltrim($value, '/')))) : null
        );
    }
}
