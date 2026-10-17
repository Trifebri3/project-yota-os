<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Program;
use App\Models\Zone;
use App\Models\CustomForm;
use App\Models\ActivityLog;
use App\Models\Submission;
use App\Models\Attendance;
use App\Models\Kegiatan;
use App\Models\Logbook;
use App\Models\ProgramUserAssignment;
use App\Models\ProgramTimeline;
use App\Models\KpiIndicator;
use App\Models\BeforeAfterRecord;
use App\Models\ProjectCharter;
use App\Services\CarbonCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminController extends Controller
{
    protected $carbonService;

    public function __construct(CarbonCalculationService $carbonService)
    {
        $this->carbonService = $carbonService;
    }

    /**
     * Dashboard overview showing system stats, carbon balances, and map.
     */
    public function dashboard()
    {
        // 1. General Program Summary
        $stats = [
            'total_programs' => Program::count(),
            'total_zones' => Zone::count(),
            'total_participants' => User::whereIn('role', ['peserta', 'volunteer'])->count(),
            'total_activities' => Kegiatan::count(),
            'total_reports' => Submission::count(),
            'total_hours' => Logbook::where('status', 'approved')->sum('hours_spent'),
        ];

        // 2. Aggregate Carbon Summary
        $programs = Program::all();
        $carbon = [
            'total_emissions' => 0.0,
            'total_offsets' => 0.0,
            'net_balance' => 0.0,
            'total_trees' => 0,
            'total_waste' => 0.0,
        ];

        foreach ($programs as $prog) {
            $metrics = $this->carbonService->getProgramMetrics($prog->id);
            $carbon['total_emissions'] += $metrics['total_emissions'];
            $carbon['total_offsets'] += $metrics['total_offsets'];
            $carbon['net_balance'] += $metrics['net_balance'];
            $carbon['total_trees'] += $metrics['total_trees'];
            $carbon['total_waste'] += $metrics['total_waste'];
        }

        // 3. System activity logs
        $recentLogs = ActivityLog::with('user')->orderBy('created_at', 'desc')->take(8)->get();
        
        // 4. Geofences lists for leaflet mapping
        $zones = Zone::with('program')->get();

        return view('super-admin.dashboard.index', compact('stats', 'carbon', 'recentLogs', 'zones'));
    }

    /**
     * Users listing & management.
     */
    public function users(Request $request)
    {
        $users = User::with('assignments.program')->orderBy('created_at', 'desc')->paginate(15);
        $programs = Program::all();
        $zones = Zone::all();

        return view('super-admin.users.index', compact('users', 'programs', 'zones'));
    }

    /**
     * Create user.
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:super-admin,program-admin,project-officer,supervisor,peserta,volunteer,viewer',
            'phone' => 'nullable|string',
            'organisasi' => 'nullable|string',
            'photo_profil' => 'nullable|image|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo_profil')) {
            $photoPath = '/' . $request->file('photo_profil')->store('profiles', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'organisasi' => $request->organisasi,
            'photo_profil' => $photoPath,
            'status' => 'active',
        ]);

        // Audit Log
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create',
            'model_type' => User::class,
            'model_id' => $user->id,
            'new_values' => ['name' => $user->name, 'role' => $user->role],
        ]);

        return redirect()->back()->with('success', 'User berhasil dibuat.');
    }

    /**
     * Assign user to Program and Zone.
     */
    public function assignUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|uuid',
            'program_id' => 'required|uuid',
            'zone_id' => 'nullable|uuid',
        ]);

        ProgramUserAssignment::create([
            'user_id' => $request->user_id,
            'program_id' => $request->program_id,
            'zone_id' => $request->zone_id,
        ]);

        return redirect()->back()->with('success', 'User berhasil ditugaskan ke program.');
    }

    /**
     * Toggle user status (active/inactive).
     */
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'model_type' => User::class,
            'model_id' => $user->id,
            'new_values' => ['status' => $user->status],
        ]);

        return redirect()->back()->with('success', 'Status user berhasil diubah.');
    }

    /**
     * Programs CRUD Workspace.
     */
    public function programs()
    {
        $programs = Program::with(['projectOfficer', 'supervisor'])->get();
        
        // Fetch candidates for PO and Supervisor
        $projectOfficers = User::whereIn('role', ['project-officer', 'program-admin'])->get();
        $supervisors = User::whereIn('role', ['supervisor', 'program-admin'])->get();

        return view('super-admin.programs.index', compact('programs', 'projectOfficers', 'supervisors'));
    }

    /**
     * Store new Program.
     */
    public function storeProgram(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kategori_program' => 'required|in:Lingkungan,Pendidikan,Pemberdayaan Ekonomi,Penelitian,Kesehatan,Sosial',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'logo_program' => 'nullable|image|max:1024',
            'banner_program' => 'nullable|image|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo_program')) {
            $logoPath = '/' . $request->file('logo_program')->store('logos', 'public');
        }

        $bannerPath = null;
        if ($request->hasFile('banner_program')) {
            $bannerPath = '/' . $request->file('banner_program')->store('banners', 'public');
        }

        $program = Program::create([
            'name' => $request->name,
            'description' => $request->description,
            'tujuan_program' => $request->tujuan_program,
            'kategori_program' => $request->kategori_program,
            'status' => 'draft',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'project_officer_id' => $request->project_officer_id,
            'supervisor_id' => $request->supervisor_id,
            'mitra' => $request->mitra,
            'sponsor' => $request->sponsor,
            'organisasi_pelaksana' => $request->organisasi_pelaksana,
            'target_capaian' => $request->target_capaian,
            'logo_program' => $logoPath,
            'banner_program' => $bannerPath,
            'stats' => ['volunteers_count' => 0, 'beneficiaries_count' => 0, 'carbon_offset' => 0],
            'settings' => ['workflow' => ['stages' => ['draft', 'active', 'completed']]],
        ]);

        // Auto create project charter
        $program->projectCharter()->create([
            'budget' => $request->budget ?? 0,
            'vision' => $request->tujuan_program,
        ]);

        return redirect()->back()->with('success', 'Program berhasil dibuat.');
    }

    /**
     * Zones List & Creator.
     */
    public function zones()
    {
        $zones = Zone::with('program')->get();
        $programs = Program::all();

        return view('super-admin.zones.index', compact('zones', 'programs'));
    }

    /**
     * Save zone.
     */
    public function storeZone(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:attendance,task,activity',
            'geojson' => 'required|string',
            'status_zona' => 'required|in:aktif,belum_aktif,monitoring,selesai',
            'program_id' => 'required|uuid',
            'photo_zona' => 'nullable|image|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo_zona')) {
            $photoPath = '/' . $request->file('photo_zona')->store('zones', 'public');
        }

        Zone::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'status_zona' => $request->status_zona,
            'program_id' => $request->program_id,
            'geojson' => json_decode($request->geojson, true),
            'photo_zona' => $photoPath,
        ]);

        return redirect()->back()->with('success', 'Zona Program berhasil disimpan.');
    }

    /**
     * Form builder.
     */
    public function forms()
    {
        $forms = CustomForm::all();
        $programs = Program::all();

        return view('super-admin.settings.permissions', compact('forms', 'programs'));
    }

    /**
     * Store Form.
     */
    public function storeForm(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:custom_forms',
            'fields_json' => 'required|string',
        ]);

        CustomForm::create([
            'name' => $request->name,
            'code' => Str::slug($request->code),
            'description' => $request->description,
            'fields' => json_decode($request->fields_json, true),
            'target_type' => $request->target_type ?? 'generic',
        ]);

        return redirect()->back()->with('success', 'Form Karbon berhasil disimpan.');
    }

    /**
     * Compliance geospasial and document audits.
     */
    public function audits()
    {
        $submissions = Submission::with(['task.activity.workPlan.program', 'user'])->orderBy('created_at', 'desc')->paginate(15);
        $attendances = Attendance::with(['user', 'activity.workPlan.program'])->orderBy('created_at', 'desc')->paginate(15);

        return view('super-admin.audit-logs.audit', compact('submissions', 'attendances'));
    }

    /**
     * Audit logs.
     */
    public function auditLogs()
    {
        $logs = ActivityLog::with('user')->orderBy('created_at', 'desc')->paginate(20);
        return view('super-admin.audit-logs.index', compact('logs'));
    }

    /**
     * Update Program.
     */
    public function updateProgram(Request $request, $id)
    {
        $program = Program::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'kategori_program' => 'required|in:Lingkungan,Pendidikan,Pemberdayaan Ekonomi,Penelitian,Kesehatan,Sosial',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'logo_program' => 'nullable|image|max:1024',
            'banner_program' => 'nullable|image|max:2048',
        ]);

        $logoPath = $program->logo_program;
        if ($request->hasFile('logo_program')) {
            $logoPath = '/' . $request->file('logo_program')->store('logos', 'public');
        }

        $bannerPath = $program->banner_program;
        if ($request->hasFile('banner_program')) {
            $bannerPath = '/' . $request->file('banner_program')->store('banners', 'public');
        }

        $program->update([
            'name' => $request->name,
            'description' => $request->description,
            'tujuan_program' => $request->tujuan_program,
            'kategori_program' => $request->kategori_program,
            'status' => $request->status ?? $program->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'project_officer_id' => $request->project_officer_id,
            'supervisor_id' => $request->supervisor_id,
            'mitra' => $request->mitra,
            'sponsor' => $request->sponsor,
            'organisasi_pelaksana' => $request->organisasi_pelaksana,
            'target_capaian' => $request->target_capaian,
            'logo_program' => $logoPath,
            'banner_program' => $bannerPath,
        ]);

        if ($program->projectCharter) {
            $program->projectCharter->update([
                'budget' => $request->budget ?? $program->projectCharter->budget,
                'vision' => $request->tujuan_program,
            ]);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'model_type' => Program::class,
            'model_id' => $program->id,
            'new_values' => ['name' => $program->name],
        ]);

        return redirect()->back()->with('success', 'Program berhasil diperbarui.');
    }

    /**
     * Delete Program.
     */
    public function destroyProgram($id)
    {
        $program = Program::findOrFail($id);
        $program->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'model_type' => Program::class,
            'model_id' => $id,
        ]);

        return redirect()->back()->with('success', 'Program berhasil dihapus.');
    }

    /**
     * Update Zone.
     */
    public function updateZone(Request $request, $id)
    {
        $zone = Zone::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:attendance,task,activity',
            'geojson' => 'required|string',
            'status_zona' => 'required|in:aktif,belum_aktif,monitoring,selesai',
            'program_id' => 'required|uuid',
            'photo_zona' => 'nullable|image|max:2048',
        ]);

        $photoPath = $zone->photo_zona;
        if ($request->hasFile('photo_zona')) {
            $photoPath = '/' . $request->file('photo_zona')->store('zones', 'public');
        }

        $zone->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'status_zona' => $request->status_zona,
            'program_id' => $request->program_id,
            'geojson' => json_decode($request->geojson, true),
            'photo_zona' => $photoPath,
        ]);

        return redirect()->back()->with('success', 'Zona Program berhasil diperbarui.');
    }

    /**
     * Delete Zone.
     */
    public function destroyZone($id)
    {
        $zone = Zone::findOrFail($id);
        $zone->delete();

        return redirect()->back()->with('success', 'Zona berhasil dihapus.');
    }

    /**
     * Update User.
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email,' . $user->id,
            'role' => 'required|in:super-admin,program-admin,project-officer,supervisor,peserta,volunteer,viewer',
            'phone' => 'nullable|string',
            'organisasi' => 'nullable|string',
            'photo_profil' => 'nullable|image|max:2048',
        ]);

        $photoPath = $user->photo_profil;
        if ($request->hasFile('photo_profil')) {
            $photoPath = '/' . $request->file('photo_profil')->store('profiles', 'public');
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'organisasi' => $request->organisasi,
            'photo_profil' => $photoPath,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update',
            'model_type' => User::class,
            'model_id' => $user->id,
            'new_values' => ['name' => $user->name, 'role' => $user->role],
        ]);

        return redirect()->back()->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Delete User.
     */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete',
            'model_type' => User::class,
            'model_id' => $id,
        ]);

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    /**
     * Get all timelines for a program.
     */
    public function programTimelines($programId)
    {
        $timelines = ProgramTimeline::where('program_id', $programId)->orderBy('start_date', 'asc')->get();
        return response()->json($timelines);
    }

    /**
     * Store new timeline phase.
     */
    public function storeProgramTimeline(Request $request, $programId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $timeline = ProgramTimeline::create([
            'program_id' => $programId,
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_locked' => $request->has('is_locked') || $request->is_locked == 1,
        ]);

        return response()->json(['success' => true, 'timeline' => $timeline]);
    }

    /**
     * Update timeline phase.
     */
    public function updateProgramTimeline(Request $request, $id)
    {
        $timeline = ProgramTimeline::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $timeline->update([
            'name' => $request->name,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_locked' => $request->has('is_locked') || $request->is_locked == 1,
        ]);

        return response()->json(['success' => true, 'timeline' => $timeline]);
    }

    /**
     * Delete timeline phase.
     */
    public function destroyProgramTimeline($id)
    {
        $timeline = ProgramTimeline::findOrFail($id);
        $timeline->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Impact Experience & Identity Manager Page.
     */
    public function impactExperienceManager($programId)
    {
        $program = Program::with(['projectCharter', 'kpiIndicators', 'beforeAfterRecords', 'zones'])->findOrFail($programId);
        return view('super-admin.programs.impact-experience', compact('program'));
    }

    /**
     * Update Program Identity (Banner, Cover, Logo, Visi/Misi, Scope, Objectives, Stats).
     */
    public function updateProgramIdentity(Request $request, $programId)
    {
        $program = Program::findOrFail($programId);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'kategori_program' => 'required|string',
            'mitra' => 'nullable|string',
            'sponsor' => 'nullable|string',
            'tujuan_program' => 'nullable|string',
            'banner_program' => 'nullable|image|max:4096',
            'cover_image' => 'nullable|image|max:4096',
            'logo_program' => 'nullable|image|max:2048',
        ]);

        $bannerPath = $program->banner_program;
        if ($request->hasFile('banner_program')) {
            $bannerPath = '/' . $request->file('banner_program')->store('banners', 'public');
        }

        $coverPath = $program->cover_image;
        if ($request->hasFile('cover_image')) {
            $coverPath = '/' . $request->file('cover_image')->store('covers', 'public');
        }

        $logoPath = $program->logo_program;
        if ($request->hasFile('logo_program')) {
            $logoPath = '/' . $request->file('logo_program')->store('logos', 'public');
        }

        $currentStats = $program->stats ?? [];
        $currentStats['carbon_offset'] = (float) ($request->carbon_offset ?? ($currentStats['carbon_offset'] ?? 0));
        $currentStats['beneficiaries_count'] = (int) ($request->beneficiaries_count ?? ($currentStats['beneficiaries_count'] ?? 0));
        $currentStats['volunteers_count'] = (int) ($request->volunteers_count ?? ($currentStats['volunteers_count'] ?? 0));

        $program->update([
            'name' => $request->name,
            'description' => $request->description,
            'kategori_program' => $request->kategori_program,
            'mitra' => $request->mitra,
            'sponsor' => $request->sponsor,
            'tujuan_program' => $request->tujuan_program,
            'banner_program' => $bannerPath,
            'cover_image' => $coverPath,
            'logo_program' => $logoPath,
            'stats' => $currentStats,
        ]);

        // Update Project Charter vision & scope
        $program->projectCharter()->updateOrCreate(
            ['program_id' => $program->id],
            [
                'vision' => $request->vision ?? $program->tujuan_program,
                'mission' => $request->mission ?? '',
                'scope' => $request->scope ?? '',
            ]
        );

        return redirect()->back()->with('success', 'Identitas program dan dokumen media berhasil diperbarui.');
    }

    /**
     * Store new KPI indicator with optional graphic image upload.
     */
    public function storeKpi(Request $request, $programId)
    {
        $program = Program::findOrFail($programId);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'category' => 'nullable|string',
            'target' => 'required|numeric',
            'actual' => 'required|numeric',
            'unit' => 'required|string',
            'verified_by' => 'nullable|string',
            'definition' => 'nullable|string',
            'graphic_image' => 'nullable|image|max:3072',
        ]);

        $graphicPath = null;
        if ($request->hasFile('graphic_image')) {
            $graphicPath = '/' . $request->file('graphic_image')->store('kpi_graphics', 'public');
        }

        $program->kpiIndicators()->create([
            'code' => $request->code,
            'name' => $request->name,
            'category' => $request->category ?? 'General',
            'target' => $request->target,
            'actual' => $request->actual,
            'unit' => $request->unit,
            'verified_by' => $request->verified_by,
            'definition' => $request->definition,
            'graphic_image' => $graphicPath,
            'status' => 'achieved',
        ]);

        return redirect()->back()->with('success', 'Indikator KPI dan grafis berhasil ditambahkan.');
    }

    /**
     * Update KPI indicator.
     */
    public function updateKpi(Request $request, $id)
    {
        $kpi = KpiIndicator::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'target' => 'required|numeric',
            'actual' => 'required|numeric',
            'unit' => 'required|string',
            'graphic_image' => 'nullable|image|max:3072',
        ]);

        $graphicPath = $kpi->graphic_image;
        if ($request->hasFile('graphic_image')) {
            $graphicPath = '/' . $request->file('graphic_image')->store('kpi_graphics', 'public');
        }

        $kpi->update([
            'code' => $request->code ?? $kpi->code,
            'name' => $request->name,
            'category' => $request->category ?? $kpi->category,
            'target' => $request->target,
            'actual' => $request->actual,
            'unit' => $request->unit,
            'verified_by' => $request->verified_by ?? $kpi->verified_by,
            'definition' => $request->definition ?? $kpi->definition,
            'graphic_image' => $graphicPath,
        ]);

        return redirect()->back()->with('success', 'Indikator KPI berhasil diperbarui.');
    }

    /**
     * Delete KPI indicator.
     */
    public function destroyKpi($id)
    {
        $kpi = KpiIndicator::findOrFail($id);
        $kpi->delete();
        return redirect()->back()->with('success', 'Indikator KPI berhasil dihapus.');
    }

    /**
     * Store Before & After record with image uploads.
     */
    public function storeBeforeAfter(Request $request, $programId)
    {
        $program = Program::findOrFail($programId);

        $request->validate([
            'title' => 'required|string|max:255',
            'metric_impact' => 'nullable|string',
            'location' => 'nullable|string',
            'before_image' => 'required|image|max:4096',
            'after_image' => 'required|image|max:4096',
            'before_description' => 'nullable|string',
            'after_description' => 'nullable|string',
        ]);

        $beforePath = '/' . $request->file('before_image')->store('before_after', 'public');
        $afterPath = '/' . $request->file('after_image')->store('before_after', 'public');

        $program->beforeAfterRecords()->create([
            'title' => $request->title,
            'metric_impact' => $request->metric_impact,
            'location' => $request->location,
            'before_image' => $beforePath,
            'after_image' => $afterPath,
            'before_date' => $request->before_date,
            'after_date' => $request->after_date,
            'before_description' => $request->before_description,
            'after_description' => $request->after_description,
        ]);

        return redirect()->back()->with('success', 'Data komparasi Before & After berhasil ditambahkan.');
    }

    /**
     * Update Before & After record.
     */
    public function updateBeforeAfter(Request $request, $id)
    {
        $record = BeforeAfterRecord::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'before_image' => 'nullable|image|max:4096',
            'after_image' => 'nullable|image|max:4096',
        ]);

        $beforePath = $record->before_image;
        if ($request->hasFile('before_image')) {
            $beforePath = '/' . $request->file('before_image')->store('before_after', 'public');
        }

        $afterPath = $record->after_image;
        if ($request->hasFile('after_image')) {
            $afterPath = '/' . $request->file('after_image')->store('before_after', 'public');
        }

        $record->update([
            'title' => $request->title,
            'metric_impact' => $request->metric_impact ?? $record->metric_impact,
            'location' => $request->location ?? $record->location,
            'before_image' => $beforePath,
            'after_image' => $afterPath,
            'before_date' => $request->before_date ?? $record->before_date,
            'after_date' => $request->after_date ?? $record->after_date,
            'before_description' => $request->before_description ?? $record->before_description,
            'after_description' => $request->after_description ?? $record->after_description,
        ]);

        return redirect()->back()->with('success', 'Data Before & After berhasil diperbarui.');
    }

    /**
     * Delete Before & After record.
     */
    public function destroyBeforeAfter($id)
    {
        $record = BeforeAfterRecord::findOrFail($id);
        $record->delete();
        return redirect()->back()->with('success', 'Data Before & After berhasil dihapus.');
    }
}
