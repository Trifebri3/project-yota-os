<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Kegiatan;
use App\Models\Logbook;
use App\Models\ProgramUserAssignment;
use App\Services\GeoValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    protected $geoService;

    public function __construct(GeoValidationService $geoService)
    {
        $this->geoService = $geoService;
    }

    /**
     * Show attendance main page.
     */
    public function index()
    {
        $user = Auth::user();
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->first();

        // Get activities active today or upcoming
        $activities = Kegiatan::with('zone')->where('status', 'active')->get();

        return view('volunteer.attendance.index', compact('todayAttendance', 'activities'));
    }

    /**
     * Show check-in screen.
     */
    public function showCheckIn($activityId)
    {
        $activity = Kegiatan::with('zone')->findOrFail($activityId);
        return view('volunteer.attendance.check-in', compact('activity'));
    }

    /**
     * Show general daily check-in screen.
     */
    public function showDailyCheckIn()
    {
        return view('volunteer.attendance.check-in-daily');
    }

    /**
     * Perform general daily check-in.
     */
    public function dailyCheckIn(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'selfie' => 'required|string',
            'reason' => 'nullable|string',
        ]);

        $user = Auth::user();
        $assignment = ProgramUserAssignment::with('zone')->where('user_id', $user->id)->first();
        $programId = $assignment ? $assignment->program_id : null;

        // Geofencing Check for Daily Check-In
        $isInside = false;
        if ($assignment && $assignment->zone && !empty($assignment->zone->geojson)) {
            $isInside = $this->geoService->isPointInPolygon(
                (float)$request->lat,
                (float)$request->lng,
                $assignment->zone->geojson
            );
        } else {
            $isInside = true; // No zone assigned, automatic pass
        }

        if (!$isInside && empty($request->reason)) {
            return response()->json([
                'success' => false,
                'outside' => true,
                'message' => 'Anda berada di luar zona geofence penugasan Anda. Anda wajib memberikan alasan.'
            ], 422);
        }

        // Save Selfie
        $selfiePath = null;
        if (str_starts_with($request->selfie, 'data:image')) {
            $data = substr($request->selfie, strpos($request->selfie, ',') + 1);
            $data = base64_decode($data);
            $fileName = 'selfies/daily_' . $user->id . '_' . time() . '.png';
            \Storage::disk('public')->put($fileName, $data);
            $selfiePath = '/storage/' . $fileName;
        }

        Attendance::create([
            'user_id' => $user->id,
            'activity_id' => null,
            'check_in_time' => Carbon::now(),
            'check_in_gps' => ['lat' => $request->lat, 'lng' => $request->lng],
            'check_in_selfie' => $selfiePath,
            'check_in_status' => $isInside ? 'inside_zone' : 'outside_zone',
            'reason' => $request->reason,
        ]);

        // Auto create logbook entry
        if ($programId) {
            Logbook::create([
                'user_id' => $user->id,
                'program_id' => $programId,
                'title' => '[Absen Harian] Check-In Umum',
                'content' => 'Melakukan absensi harian umum di lokasi kerja. Status: ' . ($isInside ? 'Dalam Zona' : 'Luar Zona') . ($request->reason ? '. Keterangan/Alasan: ' . $request->reason : ''),
                'date' => Carbon::today(),
                'hours_spent' => 0.0,
                'photo_path' => $selfiePath,
                'status' => 'approved',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Check In Harian Berhasil!'
        ]);
    }

    /**
     * Perform check-in action.
     */
    public function checkIn(Request $request, $activityId)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'selfie' => 'required|string', // Base64 selfie image
            'reason' => 'nullable|string',
        ]);

        $user = Auth::user();
        $activity = Kegiatan::with('zone')->findOrFail($activityId);

        // Geofencing Check
        $isInside = false;
        if ($activity->zone && !empty($activity->zone->geojson)) {
            $isInside = $this->geoService->isPointInPolygon(
                (float)$request->lat,
                (float)$request->lng,
                $activity->zone->geojson
            );
        } else {
            $isInside = true; // No zone, automatic pass
        }

        if (!$isInside && empty($request->reason)) {
            return response()->json([
                'success' => false,
                'outside' => true,
                'message' => 'Anda berada di luar zona geofence. Anda wajib memberikan alasan.'
            ], 422);
        }

        // Save Selfie
        $selfiePath = null;
        if (str_starts_with($request->selfie, 'data:image')) {
            $data = substr($request->selfie, strpos($request->selfie, ',') + 1);
            $data = base64_decode($data);
            $fileName = 'selfies/checkin_' . $user->id . '_' . time() . '.png';
            \Storage::disk('public')->put($fileName, $data);
            $selfiePath = '/storage/' . $fileName;
        }

        Attendance::create([
            'user_id' => $user->id,
            'activity_id' => $activityId,
            'check_in_time' => Carbon::now(),
            'check_in_gps' => ['lat' => $request->lat, 'lng' => $request->lng],
            'check_in_selfie' => $selfiePath,
            'check_in_status' => $isInside ? 'inside_zone' : 'outside_zone',
            'reason' => $request->reason,
        ]);

        // Auto create logbook entry
        $assignment = ProgramUserAssignment::where('user_id', $user->id)->first();
        if ($assignment) {
            Logbook::create([
                'user_id' => $user->id,
                'program_id' => $assignment->program_id,
                'title' => '[Absen Kegiatan] Check-In: ' . $activity->name,
                'content' => 'Melakukan check-in kegiatan program. Status: ' . ($isInside ? 'Dalam Zona' : 'Luar Zona') . ($request->reason ? '. Alasan: ' . $request->reason : ''),
                'date' => Carbon::today(),
                'hours_spent' => 0.0,
                'photo_path' => $selfiePath,
                'status' => 'approved',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Check In Berhasil dilakukan!'
        ]);
    }

    /**
     * Show check-out screen.
     */
    public function showCheckOut($attendanceId)
    {
        $attendance = Attendance::with('activity.zone')->findOrFail($attendanceId);
        return view('volunteer.attendance.check-out', compact('attendance'));
    }

    /**
     * Perform check-out action.
     */
    public function checkOut(Request $request, $attendanceId)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'selfie' => 'required|string', // Base64 selfie
            'reason' => 'nullable|string',
        ]);

        $user = Auth::user();
        $attendance = Attendance::findOrFail($attendanceId);
        $activity = $attendance->activity_id ? Kegiatan::with('zone')->find($attendance->activity_id) : null;

        // Geofencing Check
        $isInside = false;
        if ($activity && $activity->zone && !empty($activity->zone->geojson)) {
            $isInside = $this->geoService->isPointInPolygon(
                (float)$request->lat,
                (float)$request->lng,
                $activity->zone->geojson
            );
        } else {
            $isInside = true;
        }

        if (!$isInside && empty($request->reason)) {
            return response()->json([
                'success' => false,
                'outside' => true,
                'message' => 'Anda berada di luar zona geofence. Anda wajib memberikan alasan.'
            ], 422);
        }

        // Save Selfie
        $selfiePath = null;
        if (str_starts_with($request->selfie, 'data:image')) {
            $data = substr($request->selfie, strpos($request->selfie, ',') + 1);
            $data = base64_decode($data);
            $fileName = 'selfies/checkout_' . $user->id . '_' . time() . '.png';
            \Storage::disk('public')->put($fileName, $data);
            $selfiePath = '/storage/' . $fileName;
        }

        $attendance->update([
            'check_out_time' => Carbon::now(),
            'check_out_gps' => ['lat' => $request->lat, 'lng' => $request->lng],
            'check_out_selfie' => $selfiePath,
            'check_out_status' => $isInside ? 'inside_zone' : 'outside_zone',
            'reason' => $attendance->reason . ' | Check-out: ' . $request->reason,
        ]);

        // Auto create checkout logbook entry
        $assignment = ProgramUserAssignment::where('user_id', $user->id)->first();
        if ($assignment) {
            $checkIn = $attendance->check_in_time ? Carbon::parse($attendance->check_in_time) : Carbon::now();
            $duration = round(Carbon::now()->diffInMinutes($checkIn) / 60, 1);

            Logbook::create([
                'user_id' => $user->id,
                'program_id' => $assignment->program_id,
                'title' => '[Absen] Check-Out: ' . ($activity ? $activity->name : 'Umum'),
                'content' => 'Melakukan check-out absensi harian. Ringkasan kerja: ' . ($request->reason ?? 'Selesai bertugas.'),
                'date' => Carbon::today(),
                'hours_spent' => max(0.5, $duration),
                'photo_path' => $selfiePath,
                'status' => 'approved',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Check Out Berhasil dilakukan!'
        ]);
    }

    /**
     * Attendance History.
     */
    public function history()
    {
        $attendances = Attendance::with('activity')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('volunteer.attendance.history', compact('attendances'));
    }
}
