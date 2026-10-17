<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Kegiatan;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\Submission;
use App\Models\Logbook;
use App\Models\Certificate;
use App\Models\User;
use App\Models\ProgramUserAssignment;
use App\Models\ParticipantDocument;
use App\Models\Agenda;
use App\Models\ProgramKerja;
use App\Models\SubProgramAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VolunteerController extends Controller
{
    /**
     * Dashboard view.
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Sync any past scan attendances to Logbook automatically
        $this->syncMissingAttendancesToLogbook($user->id);
        
        // Fetch volunteer assignment to program
        $assignment = ProgramUserAssignment::where('user_id', $user->id)->first();
        $program = $assignment ? Program::with(['projectOfficer', 'supervisor'])->find($assignment->program_id) : null;
        $zoneName = $assignment && $assignment->zone ? $assignment->zone->name : 'Semua Zona';

        // Sum total logged hours
        $totalHours = Logbook::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('hours_spent');

        $activeTasksCount = Task::whereJsonContains('assigned_to', $user->id)
            ->where('status', '!=', 'completed')
            ->count();

        $completedTasksCount = Submission::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        $certificatesCount = ParticipantDocument::where('user_id', $user->id)
            ->where('type', 'certificate')
            ->count();

        // Total logbooks count
        $totalLogbooks = Logbook::where('user_id', $user->id)->count();

        // Total activities count
        $totalActivities = Kegiatan::count();

        // Carbon logs sums
        $totalEmissions = \App\Models\CarbonLog::where('user_id', $user->id)->sum('emissions') ?? 0;
        $totalOffsets = \App\Models\CarbonLog::where('user_id', $user->id)->sum('offsets') ?? 0;

        // Todays active activities
        $activities = Kegiatan::where('status', 'active')->take(3)->get();

        // Check if checked in today
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->first();

        // Total attendances count
        $totalAttendanceCount = Attendance::where('user_id', $user->id)->count();

        // Check if daily carbon log is completed today
        $filledToday = \App\Models\CarbonLog::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->exists();

        // Todays agendas & meetings
        $todaysAgendas = $program ? Agenda::where('program_id', $program->id)->whereDate('date', Carbon::today())->get() : collect();
        $todaysTasks = Task::whereJsonContains('assigned_to', $user->id)->whereDate('due_date', Carbon::today())->get();

        // Todays routine activities
        $routines = collect();
        if ($program) {
            $routines = \App\Models\RoutineActivity::where('program_id', $program->id)->get()->map(function ($rt) use ($user) {
                $rt->is_completed_today = \App\Models\RoutineActivityLog::where('user_id', $user->id)
                    ->where('routine_activity_id', $rt->id)
                    ->whereDate('date', Carbon::today())
                    ->exists();
                return $rt;
            });
        }

        // Personal contribution metrics
        $myTrees = \App\Models\CarbonLog::where('user_id', $user->id)->sum('content_data->pohon_ditanam') ?? 0;
        $myWaste = \App\Models\CarbonLog::where('user_id', $user->id)->sum('content_data->sampah_organik_kg') ?? 0;

        $programKerjas = $program ? ProgramKerja::where('program_id', $program->id)->get() : collect();

        // Calculate volunteer achievement points from approved logs
        $logbooksWithPoints = Logbook::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('title', 'like', '%[Absen Scan Admin]%')
            ->get();
        
        $totalAchievementPoints = 0;
        foreach ($logbooksWithPoints as $lb) {
            if (preg_match('/Poin Pencapaian: (\d+) poin/', $lb->content, $matches)) {
                $totalAchievementPoints += (int) $matches[1];
            }
        }

        return view('volunteer.dashboard.index', compact(
            'totalHours', 'activeTasksCount', 'completedTasksCount', 'certificatesCount', 
            'activities', 'attendance', 'filledToday', 'program', 'zoneName',
            'totalAttendanceCount', 'totalLogbooks', 'totalActivities',
            'totalEmissions', 'totalOffsets', 'todaysAgendas', 'todaysTasks',
            'myTrees', 'myWaste', 'programKerjas', 'routines', 'totalAchievementPoints'
        ));
    }

    /**
     * Tasks listing.
     */
    public function tasks()
    {
        $user = Auth::user();
        $allTasks = Task::with(['activity.workPlan.program', 'submissions' => function($q) use ($user) {
            $q->where('user_id', $user->id);
        }])
        ->orderBy('due_date', 'asc')
        ->get()
        ->filter(function ($task) use ($user) {
            if (empty($task->assigned_to)) return true;
            return in_array($user->id, $task->assigned_to);
        });

        $today = Carbon::today();

        $approvedTasks = collect();
        $todaysTasks = collect();
        $overdueTasks = collect();
        $futureTasks = collect();
        $pendingTasks = collect(); // Tasks with no due date and no approved submissions

        foreach ($allTasks as $task) {
            $submission = $task->submissions->first();
            
            if ($submission && $submission->status === 'approved') {
                $approvedTasks->push($task);
            } else {
                if ($task->due_date) {
                    $dueDate = Carbon::parse($task->due_date)->startOfDay();
                    if ($dueDate->eq($today)) {
                        $todaysTasks->push($task);
                    } elseif ($dueDate->lt($today)) {
                        $overdueTasks->push($task);
                    } else {
                        $futureTasks->push($task);
                    }
                } else {
                    $pendingTasks->push($task);
                }
            }
        }

        return view('volunteer.tasks.index', compact(
            'todaysTasks', 'overdueTasks', 'futureTasks', 'approvedTasks', 'pendingTasks'
        ));
    }

    /**
     * View single task.
     */
    public function showTask($id)
    {
        $task = Task::with(['activity.workPlan.program', 'customForm'])->findOrFail($id);
        
        // Check if there is already a submission
        $submission = Submission::where('task_id', $task->id)
            ->where('user_id', Auth::id())
            ->first();

        return view('volunteer.tasks.show', compact('task', 'submission'));
    }

    /**
     * Submit task output.
     */
    public function submitTask(Request $request, $id)
    {
        $task = Task::with('customForm')->findOrFail($id);
        $user = Auth::user();

        // If custom form is linked, validate dynamic data
        $contentData = $request->content_data ?? [];
        $selfiePath = null;
        $signaturePath = null;

        // Save signature if signature pad is signed
        if (!empty($request->signature_base64)) {
            $data = substr($request->signature_base64, strpos($request->signature_base64, ',') + 1);
            $data = base64_decode($data);
            $fileName = 'signatures/sig_' . $user->id . '_' . time() . '.png';
            \Storage::disk('public')->put($fileName, $data);
            $signaturePath = '/storage/' . $fileName;
        }

        // Save selfie (compressed)
        if (!empty($request->selfie_base64)) {
            $selfiePath = \App\Services\PhotoUploadService::compressAndStoreOnly(
                $request->selfie_base64,
                'selfies',
                'task_' . $user->id
            );
        }

        // Check if files are uploaded
        foreach ($request->allFiles() as $key => $file) {
            if (str_starts_with($key, 'file_')) {
                $fieldName = substr($key, 5);
                $filePath = '/storage/' . $file->store('submissions', 'public');
                $contentData[$fieldName] = $filePath;
            }
        }

        Submission::updateOrCreate([
            'task_id' => $id,
            'user_id' => $user->id,
        ], [
            'title' => $task->title,
            'description' => $request->description,
            'content_data' => $contentData,
            'selfie_path' => $selfiePath,
            'signature_path' => $signaturePath,
            'gps_location' => ['lat' => $request->lat, 'lng' => $request->lng],
            'status' => 'pending',
        ]);

        $assignment = ProgramUserAssignment::where('user_id', $user->id)->first();
        Logbook::updateOrCreate([
            'user_id' => $user->id,
            'title' => '[Tugas] ' . $task->title,
        ], [
            'program_id' => $assignment ? $assignment->program_id : ($task->activity->workPlan->program_id ?? null),
            'content' => 'Melaksanakan dan melaporkan tugas program: ' . ($request->description ?? 'Selesai.'),
            'date' => Carbon::today(),
            'hours_spent' => 2.0,
            'photo_path' => $selfiePath,
            'status' => 'pending',
        ]);

        return redirect()->route('volunteer.tasks.show', $id)->with('success', 'Submission berhasil dikirim!');
    }

    /**
     * Logbooks listing.
     */
    public function logbooks()
    {
        $user = Auth::user();

        // Sync any past scan attendances to Logbook automatically
        $this->syncMissingAttendancesToLogbook($user->id);

        $logbooks = Logbook::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        $assignments = ProgramUserAssignment::where('user_id', $user->id)->with('program')->get();
        $programs = $assignments->map(fn($a) => $a->program)->filter();

        return view('volunteer.logbooks.index', compact('logbooks', 'programs'));
    }

    /**
     * Store logbook entries.
     */
    public function storeLogbook(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'activity_name' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'hours_spent' => 'required|numeric|min:0.5',
            'photo' => 'nullable',
        ]);

        $exists = Logbook::where('user_id', Auth::id())
            ->where('program_id', $request->program_id)
            ->where('title', $request->activity_name)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: Logbook dengan aktivitas "' . $request->activity_name . '" sudah tersimpan untuk tanggal ' . $request->date . '.');
        }

        try {
            $photoPath = null;
            if ($request->photo || $request->hasFile('photo')) {
                $photoPath = \App\Services\PhotoUploadService::storeAndCompress(
                    $request->photo,
                    $request->program_id,
                    null,
                    null,
                    $request->photo_lat,
                    $request->photo_lng,
                    $request->photo_device
                );
            }

            Logbook::create([
                'user_id' => Auth::id(),
                'program_id' => $request->program_id,
                'title' => $request->activity_name,
                'content' => $request->description,
                'date' => $request->date,
                'hours_spent' => $request->hours_spent,
                'status' => 'pending',
                'photo_path' => $photoPath,
            ]);

            return redirect()->back()->with('success', 'Berhasil: Logbook kegiatan "' . $request->activity_name . '" berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan logbook: ' . $e->getMessage());
        }
    }

    /**
     * Certificates listing.
     */
    public function certificates()
    {
        $certificates = Certificate::with('program')
            ->where('user_id', Auth::id())
            ->get();

        $uploadedDocuments = ParticipantDocument::with('program')
            ->where('user_id', Auth::id())
            ->whereIn('type', ['certificate', 'piagam'])
            ->get();

        return view('volunteer.certificates.index', compact('certificates', 'uploadedDocuments'));
    }

    /**
     * Verify certificates publicly.
     */
    public function verifyCertificate($code)
    {
        $certificate = Certificate::with(['user', 'program'])->where('verification_code', $code)->firstOrFail();
        return view('volunteer.certificates.verify', compact('certificate'));
    }

    /**
     * Calendar view.
     */
    public function calendar()
    {
        $activities = Kegiatan::with('workPlan.program')->where('status', 'active')->get();
        return view('volunteer.calendar.index', compact('activities'));
    }

    /**
     * Carbon offset tracker.
     */
    public function carbonIndex()
    {
        $user = Auth::user();
        $submissions = Submission::with('task')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->get();

        $totalCarbon = 0;
        foreach ($submissions as $sub) {
            if (isset($sub->content_data['carbon_offset'])) {
                $totalCarbon += (float)$sub->content_data['carbon_offset'];
            }
        }

        return view('volunteer.carbon.index', compact('totalCarbon', 'submissions'));
    }

    /**
     * View volunteer profile and show unique QR Codes.
     */
    public function profile()
    {
        $user = Auth::user();

        // Sync any past scan attendances to Logbook automatically
        $this->syncMissingAttendancesToLogbook($user->id);

        $assignments = $user->assignments()->with('program')->get();

        // Calculate volunteer achievement points from approved logs
        $logbooksWithPoints = Logbook::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('title', 'like', '%[Absen Scan Admin]%')
            ->get();
        
        $totalAchievementPoints = 0;
        foreach ($logbooksWithPoints as $lb) {
            if (preg_match('/Poin Pencapaian: (\d+) poin/', $lb->content, $matches)) {
                $totalAchievementPoints += (int) $matches[1];
            }
        }

        return view('volunteer.profile.index', compact('user', 'assignments', 'totalAchievementPoints'));
    }

    /**
     * Auto-sync any valid scan attendances to volunteer logbooks dynamically.
     */
    private function syncMissingAttendancesToLogbook($userId)
    {
        $attendances = SubProgramAttendance::with('kegiatan')
            ->where('user_id', $userId)
            ->where('status', 'valid')
            ->get();

        foreach ($attendances as $att) {
            if (!$att->kegiatan) continue;

            $date = Carbon::parse($att->scan_time)->toDateString();
            $title = '[Absen Scan Admin] ' . $att->kegiatan->name;
            $titleAlt = '[Kehadiran QR] ' . $att->kegiatan->name;

            // Check if logbook already exists for this activity on this date
            $logbookExists = Logbook::where('user_id', $userId)
                ->where('date', $date)
                ->where(function($q) use ($title, $titleAlt) {
                    $q->where('title', $title)
                      ->orWhere('title', $titleAlt);
                })
                ->exists();

            if (!$logbookExists) {
                // Determine points from reason or default to 10
                $points = 10;
                if ($att->reason && preg_match('/Bobot Poin: (\d+)/', $att->reason, $matches)) {
                    $points = (int) $matches[1];
                }

                Logbook::create([
                    'user_id' => $userId,
                    'program_id' => $att->program_id,
                    'title' => $title,
                    'content' => 'Kehadiran terverifikasi & discan oleh Admin untuk kegiatan: ' . $att->kegiatan->name . '. ' .
                                 'Status: VALID. Poin Pencapaian: ' . $points . ' poin.',
                    'date' => $date,
                    'hours_spent' => 2.0,
                    'status' => 'approved',
                    'photo_path' => $att->photo_path,
                ]);
            }
        }
    }
}
