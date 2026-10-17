<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasUuids;

    protected $table = 'agendas';

    protected $fillable = [
        'program_id',
        'name',
        'description',
        'date',
        'location',
        'penanggung_jawab',
        'attachment_path',
        'meeting_link',
        'document_path',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
