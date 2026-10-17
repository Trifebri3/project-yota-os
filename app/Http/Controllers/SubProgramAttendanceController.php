<?php

namespace App\Http\Controllers;

use App\Models\ProgramKerja;
use App\Models\SubProgramAttendance;
use App\Models\Logbook;
use App\Models\User;
use App\Models\ProgramUserAssignment;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\PhotoUploadService;

class SubProgramAttendanceController extends Controller
{
    /**
     * Scan and register QR attendance for Sub-Program.
     */
    public function scan(Request $request)
    {
        try {
            $request->validate([
                'qr_code' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'device_id' => 'nullable|string',
            ]);

            $user = Auth::user();
            $qrCode = $request->qr_code;
            $userLat = (float) $request->latitude;
            $userLng = (float) $request->longitude;

            // 1. Find Sub Program by QR code
            $subProgram = ProgramKerja::where('qr_code', $qrCode)->first();
            if (!$subProgram) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code Sub Program tidak valid.'
                ], 404);
            }

            // Fetch all activities under this sub program
            $today = Carbon::today();
            $allActivities = \App\Models\Kegiatan::where('work_plan_id', $subProgram->id)->get();

            if ($allActivities->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal absen: Belum ada kegiatan/operasi lapangan yang terdaftar untuk Sub-Program ini.'
                ], 422);
            }

            // Filter active activities for today
            $activeActivities = $allActivities->filter(function($keg) use ($today) {
                if (!$keg->start_date || !$keg->end_date) return false;
                return $today->between(
                    Carbon::parse($keg->start_date)->startOfDay(), 
                    Carbon::parse($keg->end_date)->endOfDay()
                );
            });

            if ($activeActivities->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal absen: Tidak ada kegiatan/operasi lapangan yang aktif atau terjadwal hari ini (' . $today->format('d M Y') . ') untuk Sub-Program ini.'
                ], 422);
            }

            // Find the first active activity for today that this user has NOT checked into yet
            $matchedActivity = null;
            foreach ($activeActivities as $keg) {
                $hasAttended = SubProgramAttendance::where('user_id', $user->id)
                    ->where('kegiatan_id', $keg->id)
                    ->whereDate('scan_time', $today)
                    ->exists();
                
                if (!$hasAttended) {
                    $matchedActivity = $keg;
                    break;
                }
            }

            if (!$matchedActivity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan scan kehadiran untuk semua kegiatan/operasi yang aktif hari ini pada Sub-Program ini.'
                ], 422);
            }

            // 3. Geofencing check (default 50 meters radius)
            $isValid = true;
            $reason = null;
            $distance = 0.0;

            if ($subProgram->latitude && $subProgram->longitude) {
                $distance = $this->calculateDistance(
                    $userLat,
                    $userLng,
                    (float) $subProgram->latitude,
                    (float) $subProgram->longitude
                );

                $maxRadius = (int) ($subProgram->radius_meters ?? 50);
                if ($distance > $maxRadius) {
                    $isValid = false;
                    $reason = "Scanned outside radius geofence. Distance: " . round($distance, 1) . "m, Allowed: {$maxRadius}m.";
                }
            } else {
                $reason = "No coordinate set on WBS, GPS validation skipped.";
            }

            // 4. Save to sub_program_attendances table
            $attendance = SubProgramAttendance::create([
                'user_id' => $user->id,
                'program_id' => $subProgram->program_id,
                'program_kerja_id' => $subProgram->id,
                'kegiatan_id' => $matchedActivity->id,
                'scan_time' => Carbon::now(),
                'latitude' => $userLat,
                'longitude' => $userLng,
                'device_id' => $request->device_id ?? $request->header('User-Agent'),
                'status' => $isValid ? 'valid' : 'invalid',
                'reason' => $reason,
            ]);

            // 5. Integrate and Auto-generate Logbook record
            Logbook::create([
                'user_id' => $user->id,
                'program_id' => $subProgram->program_id,
                'title' => '[Kehadiran QR] ' . $matchedActivity->name,
                'content' => 'Melakukan pencatatan kehadiran via SCAN QR Sub Program: ' . $subProgram->name . 
                             ' - Kegiatan: ' . $matchedActivity->name . 
                             '. Jarak: ' . round($distance, 1) . 'm. Status: ' . ($isValid ? 'VALID' : 'INVALID (Luar Zona)'),
                'date' => Carbon::today(),
                'hours_spent' => 0.0,
                'status' => 'approved',
            ]);

            if (!$isValid) {
                return response()->json([
                    'success' => false,
                    'outside' => true,
                    'message' => 'Absensi untuk kegiatan "' . $matchedActivity->name . '" dicatat sebagai INVALID karena Anda berada di luar radius lokasi kegiatan (' . round($distance, 1) . 'm dari titik pusat).'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kehadiran kegiatan "' . $matchedActivity->name . '" berhasil dicatat! Status: VALID.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses absensi QR: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate distance between coordinates in meters using Haversine formula.
     */
    private function calculateDistance($latFrom, $lonFrom, $latTo, $lonTo)
    {
        $earthRadius = 6371000; // Earth radius in meters
        
        $latFromRad = deg2rad($latFrom);
        $lonFromRad = deg2rad($lonFrom);
        $latToRad = deg2rad($latTo);
        $lonToRad = deg2rad($lonTo);

        $latDelta = $latToRad - $latFromRad;
        $lonDelta = $lonToRad - $lonFromRad;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFromRad) * cos($latToRad) * pow(sin($lonDelta / 2), 2)));
        
        return $angle * $earthRadius;
    }

    public function showVolunteerScanner()
    {
        $user = Auth::user();
        // Fetch all authorized programs
        $programs = \App\Models\Program::where('project_officer_id', $user->id)
            ->orWhere('supervisor_id', $user->id)
            ->get();
        $programIds = $programs->pluck('id')->toArray();

        // Fetch activities under authorized programs active TODAY
        $today = Carbon::today()->toDateString();
        $activities = Kegiatan::whereHas('workPlan', function($q) use ($programIds) {
            $q->whereIn('program_id', $programIds);
        })
        ->where('start_date', '<=', $today)
        ->where('end_date', '>=', $today)
        ->with('workPlan.program')
        ->get();

        $history = SubProgramAttendance::with(['user', 'program', 'subProgram', 'kegiatan'])
            ->orderBy('scan_time', 'desc')
            ->take(50)
            ->get();

        return view('program-admin.scan-volunteer.index', compact('history', 'activities'));
    }

    /**
     * Process scanned volunteer QR code, verify face capture, and check-in today's activity.
     */
    public function submitVolunteerScan(Request $request)
    {
        try {
            $request->validate([
                'scanned_qr' => 'required|string',
                'selfie_photo' => 'required|string', // base64 face capture of volunteer
                'lat' => 'nullable|numeric',
                'lng' => 'nullable|numeric',
                'kegiatan_id' => 'nullable|uuid',
                'points' => 'nullable|integer|min:0',
            ]);

            $scannedQr = $request->scanned_qr;
            $userId = null;
            $programId = null;

            if (str_starts_with($scannedQr, 'USER:')) {
                $parts = explode(':', $scannedQr);
                if (count($parts) >= 4 && $parts[2] === 'PROGRAM') {
                    $userId = $parts[1];
                    $programId = $parts[3];
                } elseif (count($parts) >= 2) {
                    $userId = $parts[1];
                }
            }

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format QR Code tidak valid.'
                ], 422);
            }

            $volunteer = User::where('id', $userId)->first();
            if (!$volunteer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Relawan tidak ditemukan.'
                ], 404);
            }

            // Fetch program assignments
            $query = ProgramUserAssignment::where('user_id', $volunteer->id);
            if ($programId) {
                $query->where('program_id', $programId);
            }
            $assignments = $query->get();

            if ($assignments->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Relawan tidak terdaftar pada program ini.'
                ], 422);
            }

            $programIds = $assignments->pluck('program_id')->toArray();
            $matchedActivity = null;

            // 1. If activity is selected in request, use it
            if ($request->filled('kegiatan_id')) {
                $matchedActivity = Kegiatan::with('workPlan.program')->find($request->kegiatan_id);
            }

            // 2. Fallback to auto-match logic if no activity is selected
            if (!$matchedActivity) {
                $activities = Kegiatan::whereHas('workPlan', function($q) use ($programIds) {
                    $q->whereIn('program_id', $programIds);
                })->get();

                $today = Carbon::today();
                foreach ($activities as $act) {
                    $start = Carbon::parse($act->start_date)->startOfDay();
                    $end = Carbon::parse($act->end_date)->endOfDay();
                    
                    if ($today->between($start, $end)) {
                        // Check if user has already checked into this activity today
                        $alreadyCheckedIn = SubProgramAttendance::where('user_id', $volunteer->id)
                            ->where('kegiatan_id', $act->id)
                            ->whereDate('scan_time', $today)
                            ->exists();

                        if (!$alreadyCheckedIn) {
                            $matchedActivity = $act;
                            break;
                        }
                    }
                }
            }

            if (!$matchedActivity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada kegiatan aktif hari ini pada program relawan tersebut, atau relawan sudah melakukan presensi untuk seluruh kegiatan aktif.'
                ], 422);
            }

            // Process selfie photo base64
            $photoPath = null;
            if (!empty($request->selfie_photo)) {
                $photoPath = PhotoUploadService::compressAndStoreOnly(
                    $request->selfie_photo,
                    'selfies',
                    'presensi_admin_' . $volunteer->id
                );
            }

            $points = (int) ($request->points ?? 10);
            $reason = "Presensi disetujui Admin. Bobot Poin: " . $points;

            // Create attendance record
            $attendance = SubProgramAttendance::create([
                'user_id' => $volunteer->id,
                'program_id' => $matchedActivity->workPlan->program_id,
                'program_kerja_id' => $matchedActivity->work_plan_id,
                'kegiatan_id' => $matchedActivity->id,
                'scan_time' => Carbon::now(),
                'latitude' => $request->lat,
                'longitude' => $request->lng,
                'device_id' => 'Admin Scanner (' . $request->header('User-Agent') . ')',
                'status' => 'valid',
                'reason' => $reason,
                'photo_path' => $photoPath,
            ]);

            // Create Logbook record for the volunteer
            Logbook::create([
                'user_id' => $volunteer->id,
                'program_id' => $matchedActivity->workPlan->program_id,
                'title' => '[Absen Scan Admin] ' . $matchedActivity->name,
                'content' => 'Kehadiran terverifikasi & discan oleh Admin untuk kegiatan: ' . $matchedActivity->name . '. ' .
                             'Status: VALID. Poin Pencapaian: ' . $points . ' poin.',
                'date' => Carbon::today(),
                'hours_spent' => 2.0, // default execution hours
                'status' => 'approved',
                'photo_path' => $photoPath,
            ]);

            // Update WBS target outputs achievement in ProgramKerja (SubProgram)
            $subProgram = ProgramKerja::find($matchedActivity->work_plan_id);
            if ($subProgram && !empty($subProgram->target_outputs)) {
                $targets = $subProgram->target_outputs;
                $updated = false;

                // Try to find a target output matching the activity name (case-insensitive)
                foreach ($targets as $index => $target) {
                    if (isset($target['label']) && (
                        stripos($target['label'], $matchedActivity->name) !== false ||
                        stripos($matchedActivity->name, $target['label']) !== false
                    )) {
                        $targets[$index]['achieved'] = (int)($target['achieved'] ?? 0) + 1;
                        $updated = true;
                        break;
                    }
                }

                // If no exact/partial name match, increment the first target output as a fallback
                if (!$updated && isset($targets[0])) {
                    $targets[0]['achieved'] = (int)($targets[0]['achieved'] ?? 0) + 1;
                    $updated = true;
                }

                if ($updated) {
                    $subProgram->update([
                        'target_outputs' => $targets
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Presensi relawan "' . $volunteer->name . '" untuk kegiatan "' . $matchedActivity->name . '" berhasil dicatat!',
                'volunteer_name' => $volunteer->name,
                'activity_name' => $matchedActivity->name,
                'photo_url' => $photoPath,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses presensi relawan: ' . $e->getMessage()
            ], 500);
        }
    }
}
