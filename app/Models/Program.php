<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Program extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'cover_image',
        'status',
        'start_date',
        'end_date',
        'settings',
        'stats',
        'logo_program',
        'banner_program',
        'tujuan_program',
        'kategori_program',
        'project_officer_id',
        'supervisor_id',
        'mitra',
        'sponsor',
        'organisasi_pelaksana',
        'target_capaian',
    ];

    protected $casts = [
        'settings' => 'array',
        'stats' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function projectCharter()
    {
        return $this->hasOne(ProjectCharter::class);
    }

    public function programKerja()
    {
        return $this->hasMany(ProgramKerja::class);
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class);
    }

    public function financeTransactions()
    {
        return $this->hasMany(FinanceTransaction::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function projectOfficer()
    {
        return $this->belongsTo(User::class, 'project_officer_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function assignments()
    {
        return $this->hasMany(ProgramUserAssignment::class);
    }

    public function zones()
    {
        return $this->hasMany(Zone::class);
    }

    public function timelines()
    {
        return $this->hasMany(ProgramTimeline::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function subProgramAttendances()
    {
        return $this->hasMany(SubProgramAttendance::class);
    }

    public function storyMilestones()
    {
        return $this->hasMany(StoryMilestone::class)->orderBy('order', 'asc')->orderBy('day_number', 'asc');
    }

    public function impactChains()
    {
        return $this->hasMany(ImpactChain::class)->orderBy('stage_order', 'asc');
    }

    public function kpiIndicators()
    {
        return $this->hasMany(KpiIndicator::class);
    }

    public function projectOutputs()
    {
        return $this->hasMany(ProjectOutput::class);
    }

    public function beforeAfterRecords()
    {
        return $this->hasMany(BeforeAfterRecord::class);
    }

    protected function logoProgram(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) return null;
                if (str_starts_with($value, 'http')) return $value;
                if (str_starts_with($value, '/images/') || str_starts_with($value, 'images/')) {
                    return '/' . ltrim($value, '/');
                }
                if (str_starts_with($value, '/storage/') || str_starts_with($value, 'storage/')) {
                    return '/' . ltrim($value, '/');
                }
                return '/storage/' . ltrim($value, '/');
            }
        );
    }

    protected function coverImage(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) return null;
                if (str_starts_with($value, 'http')) return $value;
                if (str_starts_with($value, '/images/') || str_starts_with($value, 'images/')) {
                    return '/' . ltrim($value, '/');
                }
                if (str_starts_with($value, '/storage/') || str_starts_with($value, 'storage/')) {
                    return '/' . ltrim($value, '/');
                }
                return '/storage/' . ltrim($value, '/');
            }
        );
    }

    protected function bannerProgram(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) return null;
                if (str_starts_with($value, 'http')) return $value;
                if (str_starts_with($value, '/images/') || str_starts_with($value, 'images/')) {
                    return '/' . ltrim($value, '/');
                }
                if (str_starts_with($value, '/storage/') || str_starts_with($value, 'storage/')) {
                    return '/' . ltrim($value, '/');
                }
                return '/storage/' . ltrim($value, '/');
            }
        );
    }
}
