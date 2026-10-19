<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\Zone;
use App\Models\User;
use App\Models\Logbook;
use App\Services\CarbonCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicPortalController extends Controller
{
    /**
     * Authenticate user via Signed URL.
     */
    public function quickLogin(Request $request, $userId)
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Tautan Login Cepat Tidak Valid atau Kedaluwarsa.');
        }

        $user = User::findOrFail($userId);
        
        // Log in the user
        Auth::login($user, true);

        return redirect()->route('dashboard')->with('success', 'Selamat datang kembali, login cepat berhasil!');
    }

    /**
     * Landing Page with Program Showcase & Macro Metrics.
     */
    public function index()
    {
        $programs = Program::whereIn('status', ['active', 'aktif'])
            ->with(['zones', 'kpiIndicators', 'storyMilestones', 'projectOutputs'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        // Sum up stats across all programs
        $totalVolunteers = 0;
        $totalBeneficiaries = 0;
        $totalCarbonOffset = 0;

        foreach ($programs as $prog) {
            $totalVolunteers += $prog->stats['volunteers_count'] ?? 0;
            $totalBeneficiaries += $prog->stats['beneficiaries_count'] ?? 0;
            $totalCarbonOffset += $prog->stats['carbon_offset'] ?? 0;
        }

        // Fetch Leaflet polygon zones to draw on landing map
        $zones = Zone::all();

        return view('welcome', compact('programs', 'totalVolunteers', 'totalBeneficiaries', 'totalCarbonOffset', 'zones'));
    }

    /**
     * Dedicated Interactive Impact Experience for a specific Project.
     */
    public function showProjectExperience($id)
    {
        $program = Program::with([
            'projectCharter',
            'storyMilestones',
            'impactChains',
            'kpiIndicators',
            'projectOutputs',
            'beforeAfterRecords',
            'zones',
            'programKerja.kegiatan',
            'projectOfficer',
            'supervisor',
            'assignments.user',
        ])->findOrFail($id);

        // Fetch carbon metrics
        $carbonService = new CarbonCalculationService();
        $carbonMetrics = $carbonService->getProgramMetrics($program->id);

        // Real field logbooks with photos and activities
        $logbookQuery = Logbook::where('program_id', $program->id)
            ->whereNotNull('photo_path')
            ->with('user')
            ->orderBy('date', 'asc');

        if ($program->start_date && $program->end_date) {
            $logbookQuery->whereBetween('date', [
                $program->start_date->format('Y-m-d 00:00:00'),
                $program->end_date->format('Y-m-d 23:59:59')
            ]);
        }
        $realLogbooks = $logbookQuery->get();

        $byDate = $realLogbooks->groupBy(function($l) {
            return substr($l->date, 0, 10);
        })->sortKeys();

        $curatedStoryLogs = collect();
        $dailyGroups = collect();
        $uniqueVolunteers = collect();
        $dayIndex = 1;

        foreach ($byDate as $dateStr => $logsOnDate) {
            // Sort logs within the day: substantive field actions first, followed by routine/attendance
            $sortedLogs = $logsOnDate->sortByDesc(function($l) {
                $t = strtolower($l->title);
                $score = 0;
                
                // Specific high-impact project milestones and actions
                $keywords = [
                    'bersih pantai', 'coastal', 'sampah', 'insinerator', 'kompos', 'maggot', 
                    'hajat laut', 'tempat sampah', 'silaturahmi', 'kunjungan', 'keberangkatan', 
                    'kepulangan', 'purbayani cup', 'umkm', 'qris', 'edukasi', 'bale resik', 
                    'peninjauan', 'pemantauan', 'simulasi', 'finishing', 'pemasangan', 'lahan',
                    'pertanian', 'pariwisata', 'material', 'struktur'
                ];
                foreach ($keywords as $kw) {
                    if (str_contains($t, $kw)) {
                        $score += 60;
                        break;
                    }
                }
                
                if (str_contains($t, '[tugas]')) $score += 35;
                if (str_contains($t, '[aktivitas tambahan]')) $score += 25;
                if (str_contains($t, '[to-do]')) $score += 20;
                if (str_contains($l->photo_path, 'documentations')) $score += 25;
                if (str_contains($l->photo_path, 'task_')) $score += 15;
                if (str_contains($l->photo_path, 'presensi_admin')) $score += 10;
                
                // Deprioritize routine and simple check-ins for the main title & first photos
                if (str_contains($t, 'kegiatan rutin')) $score -= 40;
                if (str_contains($t, 'sholat')) $score -= 50;
                if (str_contains($t, 'olahraga')) $score -= 30;
                if (str_contains($t, 'absen harian') || str_contains($t, 'check-in umum')) $score -= 45;
                if (str_contains($t, 'check-out')) $score -= 45;
                
                return $score;
            })->values();

            if ($sortedLogs->isEmpty()) {
                continue;
            }

            foreach ($sortedLogs as $ml) {
                if ($ml->user) {
                    $uniqueVolunteers->push([
                        'id' => $ml->user->id,
                        'name' => $ml->user->name,
                    ]);
                }
            }

            $primaryLog = $sortedLogs->first();
            if ($primaryLog) {
                $primaryLog->day_number = $dayIndex;
                $primaryLog->formatted_day = 'Day ' . sprintf('%02d', $dayIndex);
                $curatedStoryLogs->push($primaryLog);
            }

            // Clean title for neat presentation
            $cleanTitle = preg_replace('/^\[.*?\]\s*/', '', $primaryLog->title ?? 'Aksi Lapangan');
            $cleanTitle = ucfirst(trim($cleanTitle));

            $dailyGroups->push([
                'day_number' => $dayIndex,
                'formatted_day' => 'Day ' . sprintf('%02d', $dayIndex),
                'date_str' => \Carbon\Carbon::parse($dateStr)->format('d M Y'),
                'total_photos' => $sortedLogs->count(),
                'primary_title' => $cleanTitle,
                'logs' => $sortedLogs->map(function($l) {
                    $timeStr = '';
                    if ($l->date) {
                        try {
                            $timeStr = \Carbon\Carbon::parse($l->date)->format('H:i');
                        } catch (\Exception $e) {
                            $timeStr = '10:00';
                        }
                    }
                    $photo = $l->photo_path;
                    if ($photo && !str_starts_with($photo, 'http') && !str_starts_with($photo, '/')) {
                        $photo = '/' . $photo;
                    }
                    return [
                        'id' => $l->id,
                        'title' => $l->title,
                        'content' => $l->content,
                        'photo_path' => $photo,
                        'time' => $timeStr ?: '10:00',
                        'user_id' => $l->user_id,
                        'user_name' => $l->user->name ?? 'Relawan',
                        'user_role' => $l->user->role ?? 'Volunteer',
                        'user_avatar' => $l->user->avatar_photo ?? null,
                    ];
                })
            ]);

            $dayIndex++;
        }

        $uniqueVolunteers = $uniqueVolunteers->unique('id')->values();

        // Assigned personnel: STRICTLY Volunteer and Project Officer ONLY
        $assignedUsers = $program->assignments()
            ->with('user')
            ->get()
            ->map(function($as) {
                $user = $as->user;
                if (!$user) return null;
                $userRole = strtolower($user->role ?? '');
                if (!in_array($userRole, ['volunteer', 'peserta', 'project-officer'])) {
                    return null;
                }
                $selfie = Logbook::where('user_id', $user->id)
                    ->whereNotNull('photo_path')
                    ->where('photo_path', 'like', '%selfies%')
                    ->latest()
                    ->value('photo_path');
                $user->avatar_photo = $selfie;
                return $user;
            })
            ->filter()
            ->unique('id')
            ->values();

        // Location Photos
        $locationPhotos = collect();
        if ($program->cover_image) {
            $locationPhotos->push([
                'url' => str_starts_with($program->cover_image, 'http') ? $program->cover_image : '/' . ltrim($program->cover_image, '/'),
                'caption' => 'Tapak Konservasi Pesisir'
            ]);
        }
        if ($program->banner_program && $program->banner_program !== $program->cover_image) {
            $locationPhotos->push([
                'url' => str_starts_with($program->banner_program, 'http') ? $program->banner_program : '/' . ltrim($program->banner_program, '/'),
                'caption' => 'Pemandangan Garis Pantai & Sabuk Hijau'
            ]);
        }
        
        // Add additional field landscape photos from real logs
        $landscapeLogs = $realLogbooks->filter(function($l) {
            return !str_contains($l->photo_path, 'selfies') && (str_contains(strtolower($l->title), 'pantai') || str_contains(strtolower($l->title), 'kunjungan') || str_contains(strtolower($l->title), 'desa'));
        })->take(3);

        foreach ($landscapeLogs as $ll) {
            $photoUrl = $ll->photo_path;
            if ($photoUrl && !str_starts_with($photoUrl, 'http')) {
                $photoUrl = '/' . ltrim($photoUrl, '/');
            }
            $locationPhotos->push(['url' => $photoUrl, 'caption' => $ll->title]);
        }

        if ($locationPhotos->isEmpty()) {
            $locationPhotos->push(['url' => '/images/projects/mangrove.jpg', 'caption' => 'Kawasan Purbayani']);
        }

        // Other active programs for easy switching in the project explorer
        $otherPrograms = Program::whereIn('status', ['active', 'aktif'])
            ->where('id', '!=', $program->id)
            ->get();

        return view('public.project-experience', compact(
            'program',
            'carbonMetrics',
            'realLogbooks',
            'curatedStoryLogs',
            'dailyGroups',
            'uniqueVolunteers',
            'assignedUsers',
            'locationPhotos',
            'otherPrograms'
        ));
    }

    /**
     * Dedicated Evidence Review & Program Report Document (Laporan Program & Tinjauan Bukti).
     */
    public function showEvidenceReport($id)
    {
        $program = Program::with([
            'projectCharter',
            'storyMilestones',
            'impactChains',
            'kpiIndicators',
            'projectOutputs',
            'beforeAfterRecords',
            'zones',
            'financeTransactions',
        ])->findOrFail($id);

        return view('public.evidence-report', compact('program'));
    }

    /**
     * API for leaflet map polygons on the public portal.
     */
    public function getMapData(Request $request)
    {
        $query = Zone::with('program');

        // Optional filter by program
        if ($request->has('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        $zones = $query->get();

        $features = [];
        foreach ($zones as $zone) {
            $activities = Kegiatan::where('zone_id', $zone->id)->where('status', 'active')->get();
            $activityNames = $activities->pluck('name')->implode(', ');

            $rawCoords = $zone->geojson;
            $coordinates = [];
            if (is_array($rawCoords)) {
                $formattedCoords = [];
                foreach ($rawCoords as $coord) {
                    if (is_array($coord) && count($coord) >= 2) {
                        $formattedCoords[] = [(float)$coord[1], (float)$coord[0]];
                    }
                }
                if (count($formattedCoords) > 0 && $formattedCoords[0] !== end($formattedCoords)) {
                    $formattedCoords[] = $formattedCoords[0];
                }
                $coordinates = [$formattedCoords];
            }

            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'id' => $zone->id,
                    'program_id' => $zone->program_id,
                    'name' => $zone->name,
                    'description' => $zone->description . ($activityNames ? ' (Kegiatan: ' . $activityNames . ')' : ''),
                    'status' => $zone->status_zona ?? 'aktif',
                    'zone_name' => $zone->name,
                    'program_name' => $zone->program?->name ?? 'Yota Program',
                ],
                'geometry' => [
                    'type' => 'Polygon',
                    'coordinates' => $coordinates,
                ]
            ];
        }

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
