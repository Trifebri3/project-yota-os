<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\User;
use App\Models\Logbook;
use App\Models\Attendance;
use App\Models\Kegiatan;
use App\Services\CarbonCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViewerController extends Controller
{
    /**
     * Viewer Dashboard (Overview of all programs and metrics)
     */
    public function dashboard()
    {
        $programs = Program::with('projectCharter')->get();
        $programIds = $programs->pluck('id')->toArray();

        // Calculate global stats
        $totalPrograms = count($programIds);
        $totalActivities = Kegiatan::whereHas('workPlan', function($q) use ($programIds) {
            $q->whereIn('program_id', $programIds);
        })->count();

        $totalLogbooks = Logbook::whereIn('program_id', $programIds)->count();
        
        $totalVolunteers = \App\Models\ProgramUserAssignment::whereIn('program_id', $programIds)
            ->whereHas('user', function($q) {
                $q->where('role', 'volunteer');
            })->count();

        $totalPeserta = \App\Models\ProgramUserAssignment::whereIn('program_id', $programIds)
            ->whereHas('user', function($q) {
                $q->where('role', 'peserta')->orWhere('role', 'participant');
            })->count();

        // Carbon metrics
        $carbonService = new CarbonCalculationService();
        $totalEmissions = 0.0;
        $totalOffsets = 0.0;
        foreach ($programIds as $pid) {
            $metrics = $carbonService->getProgramMetrics($pid);
            $totalEmissions += $metrics['total_emissions'];
            $totalOffsets += $metrics['total_offsets'];
        }

        // Live system feeds (Logbooks only)
        $recentLogbooks = Logbook::whereIn('program_id', $programIds)
            ->with(['program', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(30) // increased limit to make filtering useful
            ->get();

        $volunteers = User::whereIn('role', ['volunteer', 'peserta', 'participant'])
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        return view('viewer.dashboard.index', compact(
            'programs', 'totalPrograms', 'totalActivities', 'totalLogbooks',
            'totalVolunteers', 'totalPeserta',
            'totalEmissions', 'totalOffsets', 'recentLogbooks', 'volunteers'
        ));
    }

    /**
     * Program Detailed View for Viewer
     */
    public function showProgram($id)
    {
        $program = Program::with(['projectCharter', 'programKerja.kegiatan', 'assignments.user'])->findOrFail($id);

        // Program specific stats
        $volunteersCount = $program->assignments()->whereHas('user', function($q) {
            $q->where('role', 'volunteer');
        })->count();

        $pesertaCount = $program->assignments()->whereHas('user', function($q) {
            $q->where('role', 'peserta')->orWhere('role', 'participant');
        })->count();

        // Carbon calculations
        $carbonService = new CarbonCalculationService();
        $carbonMetrics = $carbonService->getProgramMetrics($program->id);

        // Live items (Logbooks only)
        $recentLogbooks = Logbook::where('program_id', $program->id)
            ->with('user')
            ->orderBy('date', 'desc')
            ->limit(30) // increased limit to make filtering useful
            ->get();

        $volunteers = User::whereHas('assignments', function($q) use ($program) {
            $q->where('program_id', $program->id);
        })->whereIn('role', ['volunteer', 'peserta', 'participant'])->get();

        return view('viewer.dashboard.program', compact(
            'program', 'volunteersCount', 'pesertaCount', 'carbonMetrics', 'recentLogbooks', 'volunteers'
        ));
    }
}
