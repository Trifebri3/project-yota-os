<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Agenda;
use App\Models\Task;
use App\Models\Submission;
use App\Models\AdditionalActivity;
use App\Models\ProgramResource;
use App\Models\ParticipantDocument;
use App\Models\Complaint;
use App\Models\Logbook;
use App\Models\ProgramUserAssignment;
use App\Models\Reprimand;
use App\Models\UserFacility;
use App\Models\Todo;
use App\Models\User;
use App\Models\RoutineActivity;
use App\Models\RoutineActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VolunteerWorkspaceController extends Controller
{
    /**
     * Show Program Calendar.
     */
    public function calendar()
    {
        $user = Auth::user();
        $assignment = ProgramUserAssignment::where('user_id', $user->id)->first();

        if (!$assignment) {
            return redirect()->route('volunteer.dashboard')->with('error', 'Anda belum ditugaskan ke program.');
        }

        $program = Program::with(['timelines', 'programKerja'])->findOrFail($assignment->program_id);
        $agendas = Agenda::where('program_id', $program->id)->orderBy('date', 'asc')->get();

        return view('volunteer.workspace.calendar', compact('program', 'agendas'));
    }

    /**
     * Show task details.
     */
    public function showTask($id)
    {
        $task = Task::with('activity.workPlan.program')->findOrFail($id);
        return view('volunteer.tasks.show', compact('task'));
    }

    /**
     * Submit WBS task execution results.
     */
    public function submitTask(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string',
            'photo' => 'nullable',
        ]);

        $task = Task::with('activity.workPlan')->findOrFail($id);

        try {
            $photoPath = null;
            if ($request->photo || $request->hasFile('photo')) {
                $photoPath = \App\Services\PhotoUploadService::storeAndCompress(
                    $request->photo,
                    $task->activity?->workPlan?->program_id ?? null,
                    $task->activity?->workPlan?->id ?? null,
                    $task->activity_id,
                    $request->photo_lat,
                    $request->photo_lng,
                    $request->photo_device
                );
            }

            // Store WBS submission
            Submission::updateOrCreate([
                'task_id' => $id,
                'user_id' => Auth::id(),
            ], [
                'content_data' => [
                    'notes' => $request->notes,
                ],
                'attachment_path' => $photoPath,
                'status' => 'pending',
            ]);

            // Auto compile WBS execution into official logbook
            Logbook::updateOrCreate([
                'user_id' => Auth::id(),
                'title' => '[Tugas Program] ' . $task->title,
            ], [
                'program_id' => $task->activity->workPlan->program_id ?? $request->program_id,
                'date' => Carbon::today(),
                'content' => $request->notes ?? 'Melaksanakan tugas program.',
                'hours_spent' => 2.0, // default execution hours
                'photo_path' => $photoPath,
                'status' => 'pending',
            ]);

            return redirect()->route('volunteer.tasks.index')->with('success', 'Berhasil: Laporan tugas dikirim & masuk antrean logbook.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengirim laporan tugas: ' . $e->getMessage());
        }
    }

    /**
     * Show Additional Activities logs.
     */
    public function additionalActivities()
    {
        $user = Auth::user();
        $assignments = ProgramUserAssignment::where('user_id', $user->id)->with('program')->get();
        $activities = AdditionalActivity::where('user_id', $user->id)->orderBy('date', 'desc')->get();

        return view('volunteer.workspace.additional-activity', compact('activities', 'assignments'));
    }

    /**
     * Store peer activity.
     */
    public function storeAdditionalActivity(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'name' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'hours_spent' => 'required|numeric|min:0.5',
            'photo' => 'required',
        ]);

        $exists = AdditionalActivity::where('user_id', Auth::id())
            ->where('program_id', $request->program_id)
            ->where('name', $request->name)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['name' => 'Aktivitas tambahan ini sudah diajukan sebelumnya.']);
        }

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

        AdditionalActivity::create([
            'user_id' => Auth::id(),
            'program_id' => $request->program_id,
            'name' => $request->name,
            'description' => $request->description,
            'date' => $request->date,
            'hours_spent' => $request->hours_spent,
            'photo_path' => $photoPath,
            'status' => 'pending',
        ]);

        // Auto compile additional activity into official logbook
        Logbook::updateOrCreate([
            'user_id' => Auth::id(),
            'title' => '[Aktivitas Tambahan] ' . $request->name,
        ], [
            'program_id' => $request->program_id,
            'content' => $request->description,
            'date' => $request->date,
            'hours_spent' => $request->hours_spent,
            'photo_path' => $photoPath,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Aktivitas tambahan berhasil diajukan untuk review Admin.');
    }

    /**
     * List resources guidelines.
     */
    public function resources()
    {
        $user = Auth::user();
        $assignment = ProgramUserAssignment::where('user_id', $user->id)->first();

        if (!$assignment) {
            $resources = collect();
        } else {
            $resources = ProgramResource::where('program_id', $assignment->program_id)
                ->where(function($q) use ($user) {
                    $q->where('distribution', 'general')
                      ->orWhere('assigned_user_id', $user->id);
                })->get();
        }

        return view('volunteer.workspace.resources', compact('resources'));
    }

    /**
     * List issued certificates.
     */
    public function documents()
    {
        $documents = ParticipantDocument::where('user_id', Auth::id())->get();
        return view('volunteer.workspace.documents', compact('documents'));
    }

    /**
     * List complaints and submit form.
     */
    public function complaints()
    {
        $user = Auth::user();
        $assignment = ProgramUserAssignment::where('user_id', $user->id)->first();

        $complaints = Complaint::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        $program = $assignment ? Program::find($assignment->program_id) : null;

        return view('volunteer.workspace.complaints', compact('complaints', 'program'));
    }

    /**
     * Submit complaint or aspiration.
     */
    public function storeComplaint(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'category' => 'required|string',
            'title' => 'required|string',
            'description' => 'required|string',
        ]);

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

        Complaint::create([
            'user_id' => Auth::id(),
            'program_id' => $request->program_id,
            'category' => $request->category,
            'title' => $request->title,
            'description' => $request->description,
            'photo_path' => $photoPath,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Aduan/Aspirasi Anda berhasil dikirim ke Admin.');
    }

    /**
     * Print Logbook (PDF/Print layout).
     */
    public function printLogbook(Request $request)
    {
        $user = Auth::user();
        $query = Logbook::where('user_id', $user->id)->orderBy('date', 'asc');

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $logbooks = $query->get();

        // Load assignment to get the program logo and supervisor signature
        $assignment = ProgramUserAssignment::where('user_id', $user->id)->first();
        $program = $assignment ? Program::with(['supervisor', 'projectOfficer'])->find($assignment->program_id) : null;

        return view('volunteer.workspace.print-logbook', compact('user', 'logbooks', 'program'));
    }

    /**
     * Show disciplinary warning letters (Reprimands).
     */
    public function reprimands()
    {
        $user = Auth::user();
        $reprimands = Reprimand::with('program')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('volunteer.workspace.reprimands', compact('reprimands'));
    }

    /**
     * Show assigned facilities & inventory/attributes.
     */
    public function facilities()
    {
        $user = Auth::user();
        $facilities = UserFacility::with('facility.program')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('volunteer.workspace.facilities', compact('facilities'));
    }

    // Volunteer To-Dos
    public function todosIndex()
    {
        $userId = Auth::id();
        $assignment = ProgramUserAssignment::where('user_id', $userId)->first();

        if (!$assignment) {
            return redirect()->route('volunteer.dashboard')->with('error', 'Anda belum ditugaskan ke program.');
        }

        $programId = $assignment->program_id;

        $todos = Todo::with(['creator', 'completedBy'])
            ->where('program_id', $programId)
            ->where(function($q) use ($userId) {
                $q->where(function($sq) use ($userId) {
                    $sq->where('type', 'individual')
                       ->where('created_by', $userId);
                })
                ->orWhere(function($sq) use ($userId) {
                    $sq->where('type', 'collaborative')
                       ->where(function($ssq) use ($userId) {
                           $ssq->where('created_by', $userId)
                               ->orWhereJsonContains('collaborators', $userId);
                       });
                })
                ->orWhere(function($sq) use ($userId) {
                    $sq->where('type', 'admin')
                       ->where(function($ssq) use ($userId) {
                           $ssq->whereNull('collaborators')
                               ->orWhereJsonContains('collaborators', $userId);
                       });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $memberIds = ProgramUserAssignment::where('program_id', $programId)->where('user_id', '!=', $userId)->pluck('user_id');
        $programMembers = User::whereIn('id', $memberIds)->get();

        return view('volunteer.workspace.todos', compact('todos', 'programMembers', 'programId'));
    }

    public function storeTodo(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'title' => 'required|string|max:255',
            'type' => 'required|in:individual,collaborative',
        ]);

        $exists = Todo::where('program_id', $request->program_id)
            ->where('title', $request->title)
            ->where('created_by', Auth::id())
            ->exists();

        if ($exists) {
            return redirect()->back()->withErrors(['title' => 'Tugas To-Do ini sudah terdaftar.']);
        }

        $collaborators = null;
        if ($request->type === 'collaborative') {
            $collaborators = $request->collaborators ?? [];
        }

        Todo::create([
            'program_id' => $request->program_id,
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => Auth::id(),
            'type' => $request->type,
            'collaborators' => $collaborators,
            'status' => 'pending',
            'verification_status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Tugas To-Do berhasil ditambahkan.');
    }

    public function submitTodo(Request $request, $id)
    {
        $request->validate([
            'photo' => 'nullable',
            'notes' => 'required|string',
        ]);

        $todo = Todo::findOrFail($id);
        $photoPath = null;
        if ($request->photo || $request->hasFile('photo')) {
            $photoPath = \App\Services\PhotoUploadService::storeAndCompress(
                $request->photo,
                $todo->program_id,
                null,
                null,
                $request->photo_lat,
                $request->photo_lng,
                $request->photo_device
            );
        }

        $data = [
            'photo_path' => $photoPath,
            'notes' => $request->notes,
            'completed_by' => Auth::id(),
            'completed_at' => now(),
        ];

        if ($todo->type === 'individual' || $todo->type === 'admin') {
            $data['status'] = 'pending'; // stays pending verification
            $data['verification_status'] = 'pending';
            $todo->update($data);

            // Auto compile individual/admin To-Do execution into official logbook as pending
            Logbook::updateOrCreate([
                'user_id' => Auth::id(),
                'title' => '[To-Do] ' . $todo->title,
            ], [
                'program_id' => $todo->program_id,
                'content' => 'Melaksanakan tugas to-do: ' . ($request->notes ?? 'Selesai.'),
                'date' => now()->toDateString(),
                'hours_spent' => 2.0,
                'photo_path' => $photoPath,
                'status' => 'pending',
            ]);

            $msg = 'Laporan penyelesaian tugas dikirim & menunggu persetujuan Admin.';
        } else {
            $data['status'] = 'completed';
            $data['verification_status'] = 'approved';
            $todo->update($data);

            // Group todo: complete for everyone, create logbook for all collaborators
            $collaborators = $todo->collaborators ?? [];
            $allUserIds = array_merge([$todo->created_by], $collaborators);

            foreach (array_unique($allUserIds) as $uId) {
                Logbook::updateOrCreate([
                    'user_id' => $uId,
                    'title' => '[To-Do Kelompok Selesai] ' . $todo->title,
                ], [
                    'program_id' => $todo->program_id,
                    'content' => 'Tugas kelompok selesai dilaporkan oleh: ' . Auth::user()->name . '. Catatan: ' . $request->notes,
                    'date' => now()->toDateString(),
                    'hours_spent' => 2.0,
                    'photo_path' => $photoPath,
                    'status' => 'approved',
                ]);
            }
            $msg = 'Tugas kelompok berhasil diselesaikan!';
        }

        return redirect()->back()->with('success', $msg);
    }

    public function deleteTodo($id)
    {
        $todo = Todo::findOrFail($id);
        if ($todo->created_by === Auth::id()) {
            $todo->delete();
            return redirect()->back()->with('success', 'To-Do berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk menghapus To-Do ini.');
    }

    /**
     * Show scheduled routines and their completion status for today.
     */
    public function routinesIndex()
    {
        $user = Auth::user();
        $assignment = ProgramUserAssignment::where('user_id', $user->id)->first();

        if (!$assignment) {
            return redirect()->route('volunteer.dashboard')->with('error', 'Anda belum ditugaskan ke program.');
        }

        $program = Program::findOrFail($assignment->program_id);
        
        $routines = RoutineActivity::where('program_id', $program->id)->get()->map(function ($rt) use ($user) {
            $rt->is_completed_today = RoutineActivityLog::where('user_id', $user->id)
                ->where('routine_activity_id', $rt->id)
                ->whereDate('date', Carbon::today())
                ->exists();
            return $rt;
        });

        return view('volunteer.workspace.routine', compact('program', 'routines'));
    }

    /**
     * Submit today's routine activity log.
     */
    public function submitRoutine(Request $request, $id)
    {
        $user = Auth::user();
        $routine = RoutineActivity::findOrFail($id);

        // Check if already completed today
        $alreadyLogged = RoutineActivityLog::where('user_id', $user->id)
            ->where('routine_activity_id', $routine->id)
            ->whereDate('date', Carbon::today())
            ->exists();

        if ($alreadyLogged) {
            return redirect()->back()->with('error', 'Anda sudah melaporkan kegiatan rutin ini hari ini.');
        }

        // Build validation rules dynamically
        $rules = [];
        if ($routine->is_selfie_required) {
            $rules['photo'] = 'required|image|max:2048';
        }
        if ($routine->is_notes_required) {
            $rules['notes'] = 'required|string';
        }

        $request->validate($rules);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = \App\Services\PhotoUploadService::compressAndStoreOnly(
                $request->file('photo'),
                'routine_logs',
                'routine_' . $user->id
            );
        }

        // Save log
        RoutineActivityLog::create([
            'user_id' => $user->id,
            'routine_activity_id' => $routine->id,
            'date' => Carbon::today(),
            'photo_path' => $photoPath,
            'notes' => $request->notes,
        ]);

        // Calculate hours spent
        $start = Carbon::parse($routine->start_time);
        $end = Carbon::parse($routine->end_time);
        $duration = round($end->diffInMinutes($start) / 60, 1);
        if ($duration <= 0) {
            // Handle overnight or invalid duration
            $duration = round($end->addDay()->diffInMinutes($start) / 60, 1);
        }
        if ($duration <= 0) {
            $duration = 1.0; // fallback to 1 hour
        }

        // Compile into official logbook (as pending review)
        Logbook::updateOrCreate([
            'user_id' => $user->id,
            'title' => '[Kegiatan Rutin] ' . $routine->name,
            'date' => Carbon::today(),
        ], [
            'program_id' => $routine->program_id,
            'content' => $request->notes ?? 'Melaksanakan kegiatan rutin harian: ' . $routine->name,
            'hours_spent' => $duration,
            'photo_path' => $photoPath,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Kegiatan rutin berhasil dilaporkan & dikirim ke logbook.');
    }
}
