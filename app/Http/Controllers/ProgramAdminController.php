<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProjectCharter;
use App\Models\ProgramKerja;
use App\Models\Kegiatan;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\Submission;
use App\Models\Logbook;
use App\Models\FinanceTransaction;
use App\Models\Certificate;
use App\Models\User;
use App\Models\Zone;
use App\Models\Agenda;
use App\Models\RoutineActivity;
use App\Models\AdditionalActivity;
use App\Models\ProgramResource;
use App\Models\ParticipantDocument;
use App\Models\Facility;
use App\Models\UserFacility;
use App\Models\Complaint;
use App\Models\Reprimand;
use App\Models\DailyReport;
use App\Models\Todo;
use App\Models\BudgetCeiling;
use App\Services\ReportGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramAdminController extends Controller
{
    protected $reportService;

    public function __construct(ReportGeneratorService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Workspace Dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Find programs managed by this admin
        $programs = $this->getAuthorizedPrograms();

        $programIds = $programs->pluck('id')->toArray();

        // Count stats
        $totalSubPrograms = ProgramKerja::whereIn('program_id', $programIds)->count();
        $totalActivities = Kegiatan::whereHas('workPlan', function($q) use ($programIds) {
            $q->whereIn('program_id', $programIds);
        })->count();

        $totalLogbooks = Logbook::whereIn('program_id', $programIds)->count();
        
        $totalAttendances = Attendance::whereHas('activity.workPlan', function($q) use ($programIds) {
            $q->whereIn('program_id', $programIds);
        })->count();

        // Get count of assigned participants / volunteers
        $totalPeserta = \App\Models\ProgramUserAssignment::whereIn('program_id', $programIds)
            ->whereHas('user', function($q) {
                $q->where('role', 'peserta')->orWhere('role', 'participant');
            })->count();

        $totalVolunteer = \App\Models\ProgramUserAssignment::whereIn('program_id', $programIds)
            ->whereHas('user', function($q) {
                $q->where('role', 'volunteer');
            })->count();

        // Calculate Carbon from CarbonCalculationService
        $carbonService = new \App\Services\CarbonCalculationService();
        $totalEmissions = 0.0;
        $totalOffsets = 0.0;
        foreach ($programIds as $pid) {
            $metrics = $carbonService->getProgramMetrics($pid);
            $totalEmissions += $metrics['total_emissions'];
            $totalOffsets += $metrics['total_offsets'];
        }

        $stats = [
            'total_peserta' => $totalPeserta,
            'total_volunteer' => $totalVolunteer,
            'total_sub_programs' => $totalSubPrograms,
            'total_activities' => $totalActivities,
            'total_logbooks' => $totalLogbooks,
            'total_attendances' => $totalAttendances,
            'total_emissions' => $totalEmissions,
            'total_offsets' => $totalOffsets,
            'programs_count' => count($programIds),
            'pending_tasks_review' => Submission::whereHas('task.activity.workPlan', function($q) use ($programIds) {
                $q->whereIn('program_id', $programIds);
            })->where('status', 'pending')->count(),
            'pending_logbooks' => Logbook::whereIn('program_id', $programIds)->where('status', 'pending')->count(),
            'pending_finances' => FinanceTransaction::whereIn('program_id', $programIds)->where('status', 'pending')->count(),
        ];

        $programKerjas = ProgramKerja::whereIn('program_id', $programIds)->get();

        return view('program-admin.dashboard.index', compact('stats', 'programs', 'programKerjas'));
    }

    /* ==========================================
       WORKSPACE: PROJECT MANAGER
       ========================================== */
    public function projectManagerIndex()
    {
        $programs = Program::with(['projectCharter'])->get();
        return view('program-admin.project-manager.dashboard', compact('programs'));
    }

    public function createProgram(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $program = Program::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => 'draft',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'stats' => [
                'volunteers_count' => 0,
                'beneficiaries_count' => 0,
                'carbon_offset' => 0,
            ],
            'settings' => [
                'workflow' => ['stages' => ['draft', 'active', 'completed']],
            ]
        ]);

        ProjectCharter::create([
            'program_id' => $program->id,
            'budget' => $request->budget ?? 0,
        ]);

        return redirect()->back()->with('success', 'Program berhasil dibuat.');
    }

    public function showProgramCharter($programId)
    {
        $program = Program::with('projectCharter')->findOrFail($programId);
        return view('program-admin.project-manager.project-charter', compact('program'));
    }

    public function updateProgramCharter(Request $request, $programId)
    {
        $program = Program::findOrFail($programId);
        $charter = ProjectCharter::updateOrCreate(
            ['program_id' => $program->id],
            [
                'vision' => $request->vision,
                'mission' => $request->mission,
                'background' => $request->background,
                'scope' => $request->scope,
                'budget' => $request->budget,
            ]
        );

        return redirect()->back()->with('success', 'Project Charter berhasil diperbarui.');
    }

    public function showWorkPlans($programId)
    {
        $program = Program::with('programKerja.kegiatan')->findOrFail($programId);
        return view('program-admin.project-manager.wbs', compact('program'));
    }

    public function storeWorkPlan(Request $request, $programId)
    {
        $request->validate([
            'name' => 'required|string',
            'budget' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'program_timeline_id' => 'nullable|uuid',
            'tahapan' => 'required|in:Design Thinking,Pra-Pelaksanaan,Pelaksanaan,Monitoring & Evaluasi,LPJ & Dampak',
        ]);

        if ($request->filled('program_timeline_id')) {
            $timeline = \App\Models\ProgramTimeline::findOrFail($request->program_timeline_id);
            $start = \Carbon\Carbon::parse($request->start_date);
            $end = \Carbon\Carbon::parse($request->end_date);
            
            if ($start->lt($timeline->start_date) || $end->gt($timeline->end_date)) {
                return redirect()->back()->withErrors([
                    'start_date' => "Tanggal harus berada dalam rentang timeline: {$timeline->name} ({$timeline->start_date->format('d M Y')} - {$timeline->end_date->format('d M Y')})"
                ])->withInput();
            }
        }

        $qrCode = 'QR-SUBPROG-' . strtoupper(\Illuminate\Support\Str::random(6));
        $attendanceToken = \Illuminate\Support\Str::random(32);

        ProgramKerja::create([
            'program_id' => $programId,
            'name' => $request->name,
            'description' => $request->description,
            'budget' => $request->budget,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'pending',
            'program_timeline_id' => $request->program_timeline_id,
            'tahapan' => $request->tahapan,
            'qr_code' => $qrCode,
            'attendance_token' => $attendanceToken,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius_meters' => $request->radius_meters ?? 50,
        ]);

        return redirect()->back()->with('success', 'Program kerja (WBS) berhasil ditambahkan.');
    }

    public function generateWorkPlanQr($id)
    {
        $wp = ProgramKerja::findOrFail($id);
        if (!$wp->qr_code) {
            $wp->update([
                'qr_code' => 'QR-SUBPROG-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'attendance_token' => \Illuminate\Support\Str::random(32),
            ]);
        }
        return redirect()->back()->with('success', 'QR Code & Token absensi Sub-Program berhasil di-generate.');
    }

    /* ==========================================
       WORKSPACE: OPERATIONS (Attendance, Tasks, Logbooks)
       ========================================== */
    public function operationsIndex()
    {
        $activities = Kegiatan::with(['workPlan.program', 'zone', 'tasks'])->get();
        $zones = Zone::all();
        
        // Also fetch active programs to load their timelines for dropdowns
        $programs = Program::with('timelines')->get();

        $volunteers = User::whereIn('role', ['volunteer', 'participant', 'peserta'])
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        return view('program-admin.operations.dashboard', compact('activities', 'zones', 'programs', 'volunteers'));
    }

    public function storeActivity(Request $request)
    {
        $request->validate([
            'work_plan_id' => 'required|uuid',
            'name' => 'required|string',
            'zone_id' => 'nullable|uuid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'program_timeline_id' => 'nullable|uuid',
            'assigned_to' => 'nullable|array',
            'assigned_to.*' => 'uuid',
        ]);

        if ($request->filled('program_timeline_id')) {
            $timeline = \App\Models\ProgramTimeline::findOrFail($request->program_timeline_id);
            $start = \Carbon\Carbon::parse($request->start_date);
            $end = \Carbon\Carbon::parse($request->end_date);
            
            if ($start->lt($timeline->start_date) || $end->gt($timeline->end_date)) {
                return redirect()->back()->withErrors([
                    'start_date' => "Tanggal kegiatan harus berada dalam rentang timeline: {$timeline->name} ({$timeline->start_date->format('d M Y')} - {$timeline->end_date->format('d M Y')})"
                ])->withInput();
            }
        }

        $activity = Kegiatan::create([
            'work_plan_id' => $request->work_plan_id,
            'name' => $request->name,
            'description' => $request->description,
            'status' => 'active',
            'zone_id' => $request->zone_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'program_timeline_id' => $request->program_timeline_id,
        ]);

        // Auto create corresponding Task
        Task::create([
            'activity_id' => $activity->id,
            'title' => $activity->name,
            'description' => $activity->description ?? 'Melaksanakan kegiatan lapangan: ' . $activity->name,
            'due_date' => $activity->end_date,
            'status' => 'active',
            'type' => 'individual',
            'assigned_to' => $request->assigned_to ?? [], // Empty array = all volunteers
        ]);

        return redirect()->back()->with('success', 'Kegiatan & tugas berhasil dibuat.');
    }

    public function updateActivity(Request $request, $id)
    {
        $request->validate([
            'work_plan_id' => 'required|uuid',
            'name' => 'required|string',
            'zone_id' => 'nullable|uuid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'program_timeline_id' => 'nullable|uuid',
            'assigned_to' => 'nullable|array',
            'assigned_to.*' => 'uuid',
        ]);

        $activity = Kegiatan::findOrFail($id);

        if ($request->filled('program_timeline_id')) {
            $timeline = \App\Models\ProgramTimeline::findOrFail($request->program_timeline_id);
            $start = \Carbon\Carbon::parse($request->start_date);
            $end = \Carbon\Carbon::parse($request->end_date);
            
            if ($start->lt($timeline->start_date) || $end->gt($timeline->end_date)) {
                return redirect()->back()->withErrors([
                    'start_date' => "Tanggal kegiatan harus berada dalam rentang timeline: {$timeline->name} ({$timeline->start_date->format('d M Y')} - {$timeline->end_date->format('d M Y')})"
                ])->withInput();
            }
        }

        $activity->update([
            'work_plan_id' => $request->work_plan_id,
            'name' => $request->name,
            'description' => $request->description,
            'zone_id' => $request->zone_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'program_timeline_id' => $request->program_timeline_id,
        ]);

        // Auto update corresponding Task
        Task::updateOrCreate([
            'activity_id' => $activity->id,
        ], [
            'title' => $activity->name,
            'description' => $activity->description ?? 'Melaksanakan kegiatan lapangan: ' . $activity->name,
            'due_date' => $activity->end_date,
            'status' => 'active',
            'type' => 'individual',
            'assigned_to' => $request->assigned_to ?? [], // Empty array = all volunteers
        ]);

        return redirect()->back()->with('success', 'Kegiatan & tugas berhasil diperbarui.');
    }

    public function deleteActivity($id)
    {
        $activity = Kegiatan::findOrFail($id);
        
        // Delete corresponding Task
        Task::where('activity_id', $activity->id)->delete();
        
        $activity->delete();
        return redirect()->back()->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function attendanceValidation()
    {
        $programs = $this->getAuthorizedPrograms();
        $programIds = $programs->pluck('id')->toArray();

        $pendingAttendances = Attendance::with(['user', 'activity.workPlan.program'])
            ->whereHas('activity.workPlan', function($q) use ($programIds) {
                $q->whereIn('program_id', $programIds);
            })
            ->where(function($q) {
                $q->where('check_in_status', 'outside_zone')
                  ->orWhere('check_out_status', 'outside_zone');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('program-admin.administration.attendance-validation', compact('pendingAttendances'));
    }

    public function approveAttendance($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->update([
            'check_in_status' => 'inside_zone',
            'check_out_status' => 'inside_zone',
        ]);

        return redirect()->back()->with('success', 'Absensi luar zona disetujui.');
    }

    public function printAttendance(Request $request)
    {
        $programs = $this->getAuthorizedPrograms();

        $programIds = $programs->pluck('id')->toArray();

        $query = Attendance::with(['user', 'activity.workPlan.program.projectOfficer', 'activity.workPlan.program.supervisor'])
            ->whereHas('activity.workPlan', function($q) use ($programIds) {
                $q->whereIn('program_id', $programIds);
            })
            ->orderBy('created_at', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $attendances = $query->get();

        return view('program-admin.administration.print-attendance', compact('attendances'));
    }

    public function taskApproval()
    {
        $programs = $this->getAuthorizedPrograms();
        $programIds = $programs->pluck('id')->toArray();

        $submissions = Submission::with(['task.activity.workPlan.program', 'user'])
            ->whereHas('task.activity.workPlan', function($q) use ($programIds) {
                $q->whereIn('program_id', $programIds);
            })
            ->where('status', 'pending')
            ->get();

        return view('program-admin.administration.task-approval', compact('submissions'));
    }

    public function approveTaskSubmission(Request $request, $id)
    {
        $submission = Submission::findOrFail($id);
        $status = $request->status ?? 'approved'; // approved/rejected
        $submission->update([
            'status' => $status,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'remarks' => $request->remarks,
        ]);

        if ($submission->status === 'approved') {
            $submission->task->update(['status' => 'completed']);
            
            // Find and approve the corresponding logbook entry
            $logbook = Logbook::where('user_id', $submission->user_id)
                ->whereIn('title', [
                    '[Tugas] ' . $submission->task->title,
                    '[Tugas Program] ' . $submission->task->title
                ])
                ->first();
            if ($logbook) {
                $logbook->update(['status' => 'approved']);
            } else {
                Logbook::create([
                    'user_id' => $submission->user_id,
                    'program_id' => $submission->task->activity->workPlan->program_id ?? null,
                    'title' => '[Tugas Program] ' . $submission->task->title,
                    'content' => $submission->notes ?? 'Melaksanakan tugas program.',
                    'date' => now()->toDateString(),
                    'hours_spent' => 2.0,
                    'photo_path' => $submission->attachment_path,
                    'status' => 'approved',
                ]);
            }
        } else {
            // Delete pending logbook if task is rejected
            Logbook::where('user_id', $submission->user_id)
                ->whereIn('title', [
                    '[Tugas] ' . $submission->task->title,
                    '[Tugas Program] ' . $submission->task->title
                ])
                ->delete();
        }

        return redirect()->back()->with('success', 'Submission berhasil diverifikasi.');
    }

    public function logbookApproval()
    {
        $programs = $this->getAuthorizedPrograms();
        $programIds = $programs->pluck('id')->toArray();

        $logbooks = Logbook::with(['user', 'program'])
            ->whereIn('program_id', $programIds)
            ->where('status', 'pending')
            ->get();

        return view('program-admin.administration.logbook-approval', compact('logbooks'));
    }

    public function approveLogbook(Request $request, $id)
    {
        $logbook = Logbook::findOrFail($id);
        $logbook->update([
            'status' => $request->status ?? 'approved',
        ]);

        return redirect()->back()->with('success', 'Logbook berhasil diverifikasi.');
    }

    /* ==========================================
       WORKSPACE: FINANCE (Transactions, SPJ)
       ========================================== */
    public function financeIndex()
    {
        $programs = $this->getAuthorizedPrograms();
        $programIds = $programs->pluck('id')->toArray();

        $transactions = FinanceTransaction::whereIn('program_id', $programIds)
            ->with(['program', 'creator', 'budgetCeiling'])
            ->orderBy('created_at', 'desc')
            ->get();

        $ceilings = BudgetCeiling::whereIn('program_id', $programIds)
            ->with('program')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $totalAlokasi = BudgetCeiling::whereIn('program_id', $programIds)->sum('amount');
        $totalPengeluaran = FinanceTransaction::whereIn('program_id', $programIds)->where('type', 'expense')->where('status', 'approved')->sum('amount');
        $totalPemasukan = FinanceTransaction::whereIn('program_id', $programIds)->where('type', 'income')->where('status', 'approved')->sum('amount');
        $sisaAnggaran = $totalAlokasi + $totalPemasukan - $totalPengeluaran;
        $totalPendingExpense = FinanceTransaction::whereIn('program_id', $programIds)->where('type', 'expense')->where('status', 'pending')->sum('amount');
        $sisaBersihEstimasi = $sisaAnggaran - $totalPendingExpense;

        return view('program-admin.finance.dashboard', compact(
            'transactions', 'programs', 'ceilings', 
            'totalAlokasi', 'totalPengeluaran', 'totalPemasukan', 
            'sisaAnggaran', 'totalPendingExpense', 'sisaBersihEstimasi'
        ));
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'budget_ceiling_id' => 'required|uuid',
            'title' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:income,expense',
            'category' => 'nullable|string',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = '/storage/' . $request->file('receipt')->store('receipts', 'public');
        }

        FinanceTransaction::create([
            'program_id' => $request->program_id,
            'budget_ceiling_id' => $request->budget_ceiling_id,
            'title' => $request->title,
            'description' => $request->description,
            'amount' => $request->amount,
            'type' => $request->type,
            'category' => $request->category ?? 'operational',
            'receipt_path' => $receiptPath,
            'created_by' => Auth::id(),
            'status' => 'approved', // automatic approval for admins
        ]);

        return redirect()->back()->with('success', 'Transaksi keuangan berhasil dicatat.');
    }

    public function storeBudgetCeiling(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'source' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);

        BudgetCeiling::create([
            'program_id' => $request->program_id,
            'source' => $request->source,
            'amount' => $request->amount,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Alokasi Pagu Anggaran Baru berhasil ditambahkan.');
    }

    public function deleteBudgetCeiling($id)
    {
        BudgetCeiling::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Alokasi Pagu Anggaran berhasil dihapus.');
    }

    public function deleteTransaction($id)
    {
        FinanceTransaction::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Transaksi keuangan berhasil dihapus.');
    }

    public function updateTransaction(Request $request, $id)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'budget_ceiling_id' => 'required|uuid',
            'title' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'type' => 'required|in:income,expense',
            'category' => 'nullable|string',
        ]);

        $transaction = FinanceTransaction::findOrFail($id);

        $receiptPath = $transaction->getRawOriginal('receipt_path');
        if ($request->hasFile('receipt')) {
            $receiptPath = '/storage/' . $request->file('receipt')->store('receipts', 'public');
        }

        $transaction->update([
            'program_id' => $request->program_id,
            'budget_ceiling_id' => $request->budget_ceiling_id,
            'title' => $request->title,
            'description' => $request->description,
            'amount' => $request->amount,
            'type' => $request->type,
            'category' => $request->category ?? 'operational',
            'receipt_path' => $receiptPath,
        ]);

        return redirect()->back()->with('success', 'Transaksi keuangan berhasil diperbarui.');
    }

    /* ==========================================
       WORKSPACE: ADMINISTRATION (LPJ, Certificates)
       ========================================= */
    public function administrationIndex()
    {
        $programs = $this->getAuthorizedPrograms();
        return view('program-admin.administration.templates', compact('programs'));
    }

    public function viewLpjReport($programId)
    {
        $program = Program::findOrFail($programId);
        $reportHtml = $this->reportService->generateLpjHtml($program);
        
        return view('program-admin.administration.documents', compact('program', 'reportHtml'));
    }

    public function viewCertificatePrint($programId, $userId)
    {
        $program = Program::findOrFail($programId);
        $user = User::findOrFail($userId);
        
        $certificate = Certificate::firstOrCreate([
            'program_id' => $programId,
            'user_id' => $userId,
        ], [
            'certificate_number' => 'CERT-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'issue_date' => now(),
            'verification_code' => Str::random(10),
        ]);

        $certHtml = $this->reportService->generateCertificateHtml($user, $program, $certificate->certificate_number, $certificate->verification_code);

        return view('program-admin.administration.certificates', compact('certHtml'));
    }

    public function timelineIndex()
    {
        $programs = $this->getAuthorizedPrograms();
        $programIds = $programs->pluck('id')->toArray();

        $agendas = Agenda::whereIn('program_id', $programIds)->with('program')->orderBy('date', 'asc')->get();
        return view('program-admin.operations.timeline', compact('programs', 'agendas'));
    }

    public function routinesIndex()
    {
        $programs = $this->getAuthorizedPrograms();
        $programIds = $programs->pluck('id')->toArray();

        $routines = RoutineActivity::whereIn('program_id', $programIds)->with('program')->get();
        return view('program-admin.operations.routine', compact('programs', 'routines'));
    }

    public function additionalLogsIndex()
    {
        $programs = $this->getAuthorizedPrograms();
        $programIds = $programs->pluck('id')->toArray();

        $activities = AdditionalActivity::whereIn('program_id', $programIds)->with(['user', 'program'])->get();
        return view('program-admin.operations.additional-logs', compact('activities'));
    }

    public function resourcesIndex()
    {
        $programs = $this->getAuthorizedPrograms();
        $programIds = $programs->pluck('id')->toArray();

        $resources = ProgramResource::whereIn('program_id', $programIds)->with('program')->get();
        $users = User::all();
        return view('program-admin.administration.resources', compact('programs', 'resources', 'users'));
    }

    public function certificatesIndex()
    {
        $programs = $this->getAuthorizedPrograms();
        $programIds = $programs->pluck('id')->toArray();

        $users = User::all();
        $documents = ParticipantDocument::whereIn('program_id', $programIds)->with(['user', 'program'])->get();
        return view('program-admin.administration.issue-certificates', compact('programs', 'users', 'documents'));
    }

    public function facilitiesIndex()
    {
        $programs = $this->getAuthorizedPrograms();
        $programIds = $programs->pluck('id')->toArray();

        $facilities = Facility::whereIn('program_id', $programIds)->with('program')->get();
        $users = User::all();
        $facilityIds = $facilities->pluck('id')->toArray();
        $allocations = UserFacility::whereIn('facility_id', $facilityIds)->with(['user', 'facility'])->get();
        return view('program-admin.administration.facilities', compact('programs', 'facilities', 'users', 'allocations'));
    }

    public function complaintsIndex()
    {
        $programs = $this->getAuthorizedPrograms();
        $programIds = $programs->pluck('id')->toArray();

        $complaints = Complaint::whereIn('program_id', $programIds)->with(['user', 'program'])->get();
        return view('program-admin.administration.complaints', compact('complaints'));
    }

    public function reprimandsIndex()
    {
        $programs = $this->getAuthorizedPrograms();
        $programIds = $programs->pluck('id')->toArray();

        $users = User::all();
        $reprimands = Reprimand::whereIn('program_id', $programIds)->with(['user', 'program'])->get();
        return view('program-admin.administration.reprimands', compact('programs', 'users', 'reprimands'));
    }

    public function dailyReportsIndex()
    {
        $programs = $this->getAuthorizedPrograms();
        $programIds = $programs->pluck('id')->toArray();

        $reports = DailyReport::whereIn('program_id', $programIds)->with('program')->get();
        return view('program-admin.administration.daily-reports', compact('programs', 'reports'));
    }

    /**
     * Update WBS output targets.
     */
    public function updateWbsTarget(Request $request, $id)
    {
        $wp = ProgramKerja::findOrFail($id);
        
        $targets = [];
        if ($request->has('labels')) {
            foreach ($request->labels as $i => $label) {
                if (empty($label)) continue;
                $targets[] = [
                    'label' => $label,
                    'target' => (int)($request->targets[$i] ?? 0),
                    'achieved' => (int)($request->achieveds[$i] ?? 0),
                    'unit' => $request->units[$i] ?? 'Unit',
                    'sector' => $request->sectors[$i] ?? 'Sosial',
                ];
            }
        }
        
        $wp->update([
            'target_outputs' => $targets,
        ]);

        return redirect()->back()->with('success', 'Target output WBS berhasil diperbarui.');
    }

    /**
     * Store Calendar Agenda.
     */
    public function storeAgenda(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'name' => 'required|string',
            'date' => 'required|date',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = '/' . $request->file('attachment')->store('agendas', 'public');
        }

        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = '/' . $request->file('document')->store('agendas', 'public');
        }

        Agenda::create([
            'program_id' => $request->program_id,
            'name' => $request->name,
            'description' => $request->description,
            'date' => $request->date,
            'location' => $request->location,
            'penanggung_jawab' => $request->penanggung_jawab,
            'meeting_link' => $request->meeting_link,
            'attachment_path' => $attachmentPath,
            'document_path' => $documentPath,
        ]);

        return redirect()->back()->with('success', 'Agenda timeline berhasil ditambahkan.');
    }

    /**
     * Store Routine Activity template.
     */
    public function storeRoutine(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'name' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        RoutineActivity::create([
            'program_id' => $request->program_id,
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_selfie_required' => $request->has('is_selfie_required'),
            'is_notes_required' => $request->has('is_notes_required'),
        ]);

        return redirect()->back()->with('success', 'Kegiatan rutin berhasil dijadwalkan.');
    }

    /**
     * Approve Additional Activity.
     */
    public function approveAdditionalActivity($id)
    {
        $act = AdditionalActivity::findOrFail($id);
        $act->update(['status' => 'approved']);

        // Register into participant's logbooks officially
        Logbook::create([
            'user_id' => $act->user_id,
            'program_id' => $act->program_id,
            'date' => $act->date,
            'title' => '[Aktivitas Tambahan] ' . $act->name,
            'content' => $act->description,
            'hours_spent' => $act->hours_spent,
            'photo_path' => $act->photo_path,
            'status' => 'approved',
        ]);

        return redirect()->back()->with('success', 'Aktivitas tambahan disetujui & masuk logbook.');
    }

    /**
     * Reject Additional Activity.
     */
    public function rejectAdditionalActivity(Request $request, $id)
    {
        $act = AdditionalActivity::findOrFail($id);
        $act->update([
            'status' => 'rejected',
            'remarks' => $request->remarks,
        ]);

        return redirect()->back()->with('success', 'Aktivitas tambahan ditolak.');
    }

    /**
     * Store template resource.
     */
    public function storeResource(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'name' => 'required|string',
            'file' => 'required|file',
            'type' => 'required|string',
        ]);

        $filePath = '/storage/' . $request->file('file')->store('resources', 'public');

        ProgramResource::create([
            'program_id' => $request->program_id,
            'name' => $request->name,
            'file_path' => $filePath,
            'type' => $request->type,
            'distribution' => $request->distribution ?? 'general',
            'assigned_user_id' => $request->assigned_user_id,
        ]);

        return redirect()->back()->with('success', 'Dokumen/SOP berhasil dibagikan.');
    }

    /**
     * Issue custom certificate/piagam/letter.
     */
    public function storeParticipantDocument(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'user_id' => 'required|uuid',
            'name' => 'required|string',
            'file' => 'required|file',
        ]);

        $filePath = '/storage/' . $request->file('file')->store('documents', 'public');

        ParticipantDocument::create([
            'program_id' => $request->program_id,
            'user_id' => $request->user_id,
            'name' => $request->name,
            'file_path' => $filePath,
            'type' => $request->type ?? 'certificate',
        ]);

        return redirect()->back()->with('success', 'Dokumen/Piagam berhasil diterbitkan.');
    }

    /**
     * Store facility record.
     */
    public function storeFacility(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'name' => 'required|string',
        ]);

        Facility::create([
            'program_id' => $request->program_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Fasilitas baru berhasil didaftarkan.');
    }

    /**
     * Allocate facility status to a participant.
     */
    public function allocateFacility(Request $request)
    {
        $request->validate([
            'user_id' => 'required|uuid',
            'facility_id' => 'required|uuid',
            'status' => 'required|in:pending,given',
        ]);

        UserFacility::updateOrCreate([
            'user_id' => $request->user_id,
            'facility_id' => $request->facility_id,
        ], [
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Status pembagian fasilitas diperbarui.');
    }

    /**
     * Respond to participant complaint.
     */
    public function respondComplaint(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);
        $complaint->update([
            'status' => $request->status ?? 'resolved',
            'response' => $request->response,
        ]);

        return redirect()->back()->with('success', 'Pengaduan berhasil ditanggapi.');
    }

    /**
     * Issue warning/reprimand.
     */
    public function storeReprimand(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'user_id' => 'required|uuid',
            'level' => 'required|in:light,medium,heavy',
            'reason' => 'required|string',
            'date' => 'required|date',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof')) {
            $proofPath = '/storage/' . $request->file('proof')->store('reprimands', 'public');
        }

        Reprimand::create([
            'program_id' => $request->program_id,
            'user_id' => $request->user_id,
            'level' => $request->level,
            'reason' => $request->reason,
            'date' => $request->date,
            'proof_path' => $proofPath,
        ]);

        return redirect()->back()->with('success', 'Surat Teguran berhasil dikirim.');
    }

    /**
     * File daily report.
     */
    public function storeDailyReport(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'date' => 'required|date',
        ]);

        DailyReport::create([
            'program_id' => $request->program_id,
            'date' => $request->date,
            'issues' => $request->issues,
            'risks' => $request->risks,
            'roadblocks' => $request->roadblocks,
            'recommendations' => $request->recommendations,
        ]);

        return redirect()->back()->with('success', 'Laporan Harian Lapangan berhasil diarsipkan.');
    }

    /**
     * Show profile & signature settings view.
     */
    public function showProfileSettings()
    {
        $user = Auth::user();
        return view('program-admin.settings.profile', compact('user'));
    }

    /**
     * Update profile settings & signature.
     */
    public function updateProfileSettings(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'organisasi' => 'nullable|string|max:255',
            'signature' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ]);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'organisasi' => $request->organisasi,
        ];

        if ($request->hasFile('signature')) {
            $path = '/storage/' . $request->file('signature')->store('signatures', 'public');
            $data['signature_path'] = $path;
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profil dan Tanda Tangan Digital berhasil diperbarui.');
    }

    // SOP Resources CRUD
    public function updateResource(Request $request, $id)
    {
        $resource = ProgramResource::findOrFail($id);
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
        ]);

        $data = [
            'name' => $request->name,
            'type' => $request->type,
            'distribution' => $request->distribution ?? 'general',
            'assigned_user_id' => $request->assigned_user_id,
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = '/storage/' . $request->file('file')->store('resources', 'public');
        }

        $resource->update($data);
        return redirect()->back()->with('success', 'Dokumen/SOP berhasil diperbarui.');
    }

    public function deleteResource($id)
    {
        ProgramResource::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Dokumen/SOP berhasil dihapus.');
    }

    // Participant Documents CRUD
    public function updateParticipantDocument(Request $request, $id)
    {
        $doc = ParticipantDocument::findOrFail($id);
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
        ]);

        $data = [
            'name' => $request->name,
            'type' => $request->type,
        ];

        if ($request->hasFile('file')) {
            $data['file_path'] = '/storage/' . $request->file('file')->store('documents', 'public');
        }

        $doc->update($data);
        return redirect()->back()->with('success', 'Dokumen/Piagam berhasil diperbarui.');
    }

    public function deleteParticipantDocument($id)
    {
        ParticipantDocument::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Dokumen/Piagam berhasil dihapus.');
    }

    // Facilities CRUD
    public function updateFacility(Request $request, $id)
    {
        $facility = Facility::findOrFail($id);
        $request->validate([
            'name' => 'required|string',
        ]);

        $facility->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Kelengkapan atribut berhasil diperbarui.');
    }

    public function deleteFacility($id)
    {
        Facility::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kelengkapan atribut berhasil dihapus.');
    }

    // Reprimands CRUD
    public function updateReprimand(Request $request, $id)
    {
        $rep = Reprimand::findOrFail($id);
        $request->validate([
            'level' => 'required|in:light,medium,heavy',
            'reason' => 'required|string',
            'date' => 'required|date',
        ]);

        $data = [
            'level' => $request->level,
            'reason' => $request->reason,
            'date' => $request->date,
        ];

        if ($request->hasFile('proof')) {
            $data['proof_path'] = '/storage/' . $request->file('proof')->store('reprimands', 'public');
        }

        $rep->update($data);
        return redirect()->back()->with('success', 'Surat teguran berhasil diperbarui.');
    }

    public function deleteReprimand($id)
    {
        Reprimand::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Surat teguran berhasil dihapus.');
    }

    // Daily Situational Reports CRUD
    public function updateDailyReport(Request $request, $id)
    {
        $report = DailyReport::findOrFail($id);
        $request->validate([
            'date' => 'required|date',
        ]);

        $report->update([
            'date' => $request->date,
            'issues' => $request->issues,
            'risks' => $request->risks,
            'roadblocks' => $request->roadblocks,
            'recommendations' => $request->recommendations,
        ]);

        return redirect()->back()->with('success', 'Laporan harian berhasil diperbarui.');
    }

    public function deleteDailyReport($id)
    {
        DailyReport::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Laporan harian berhasil dihapus.');
    }

    // Admin To-Dos
    public function todos()
    {
        $programs = Program::all();
        $todos = Todo::with(['program', 'creator', 'completedBy'])->orderBy('created_at', 'desc')->get();
        $users = User::where('role', 'volunteer')->orWhere('role', 'peserta')->orWhere('role', 'participant')->get();
        
        return view('program-admin.administration.todos', compact('programs', 'todos', 'users'));
    }

    public function storeAdminTodo(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_type' => 'required|in:all,specific',
        ]);

        $collaborators = null;
        if ($request->target_type === 'specific') {
            $collaborators = $request->collaborators ?? [];
        }

        Todo::create([
            'program_id' => $request->program_id,
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => Auth::id(),
            'type' => 'admin',
            'collaborators' => $collaborators,
            'status' => 'pending',
            'verification_status' => 'approved',
        ]);

        return redirect()->back()->with('success', 'Tugas To-Do Admin berhasil diterbitkan.');
    }

    public function approveTodo(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->update([
            'verification_status' => 'approved',
            'status' => 'completed',
        ]);

        // Sync or create Logbook entry for the completed_by user
        $userId = $todo->completed_by ?? $todo->created_by;
        Logbook::updateOrCreate([
            'user_id' => $userId,
            'title' => '[To-Do] ' . $todo->title,
        ], [
            'program_id' => $todo->program_id,
            'content' => $todo->notes ?? 'Menyelesaikan tugas To-Do.',
            'date' => $todo->completed_at ? $todo->completed_at->toDateString() : now()->toDateString(),
            'hours_spent' => 2.0,
            'photo_path' => $todo->photo_path,
            'status' => 'approved',
        ]);

        if ($todo->type === 'collaborative' && is_array($todo->collaborators)) {
            foreach ($todo->collaborators as $collabUserId) {
                if ($collabUserId !== $todo->completed_by) {
                    Logbook::updateOrCreate([
                        'user_id' => $collabUserId,
                        'title' => '[To-Do Kelompok Selesai] ' . $todo->title,
                    ], [
                        'program_id' => $todo->program_id,
                        'content' => 'Tugas kelompok diselesaikan oleh rekan tim: ' . ($todo->notes ?? 'Selesai.'),
                        'date' => $todo->completed_at ? $todo->completed_at->toDateString() : now()->toDateString(),
                        'hours_spent' => 2.0,
                        'photo_path' => $todo->photo_path,
                        'status' => 'approved',
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Laporan penyelesaian tugas disetujui & logbook diterbitkan.');
    }

    public function rejectTodo(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->update([
            'verification_status' => 'rejected',
            'status' => 'pending',
        ]);

        // Delete the pending logbook entry
        Logbook::where('user_id', $todo->completed_by ?? $todo->created_by)
            ->where('title', '[To-Do] ' . $todo->title)
            ->delete();

        return redirect()->back()->with('success', 'Laporan penyelesaian tugas ditolak.');
    }

    public function deleteTodo($id)
    {
        Todo::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Tugas To-Do berhasil dihapus.');
    }

    private function getAuthorizedPrograms()
    {
        $user = Auth::user();
        if ($user->isViewer()) {
            return Program::all();
        }
        return Program::where('project_officer_id', $user->id)
            ->orWhere('supervisor_id', $user->id)
            ->get();
    }
}
