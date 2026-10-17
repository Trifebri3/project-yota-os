<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPortalController;
use App\Http\Controllers\SignatureController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\ProgramAdminController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CarbonLogController;
use App\Http\Controllers\VolunteerWorkspaceController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\SubProgramAttendanceController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Public Portal routes
Route::get('/', [PublicPortalController::class, 'index'])->name('home');
Route::get('/projects/{program}', [PublicPortalController::class, 'showProjectExperience'])->name('public.projects.show');
Route::get('/map-data', [PublicPortalController::class, 'getMapData'])->name('map.data');
Route::get('/certificate/verify/{code}', [VolunteerController::class, 'verifyCertificate'])->name('certificates.verify');
Route::get('/inventory/asset/{id}', [\App\Http\Controllers\InventoryController::class, 'scan'])->name('inventory.scan');
Route::get('/inventory/public/{id}', [\App\Http\Controllers\InventoryController::class, 'publicShow'])->name('inventory.public');

// Quick Login via Signed URL
Route::get('/quick-login/{user}', [PublicPortalController::class, 'quickLogin'])->name('quick-login');

// Signature remote flows
Route::post('/signature/session', [SignatureController::class, 'createSession'])->name('signature.session');
Route::get('/signature/status/{token}', [SignatureController::class, 'getStatus'])->name('signature.status');
Route::get('/signature/remote/{token}', [SignatureController::class, 'remoteSignPage'])->name('signature.remote');
Route::post('/signature/save-remote/{token}', [SignatureController::class, 'saveRemoteSignature'])->name('signature.save-remote');
Route::post('/signature/upload', [SignatureController::class, 'uploadDirect'])->name('signature.upload');

// Standard Breeze Dashboard redirect based on role
Route::get('/dashboard', function () {
    $user = Auth::user();
    if ($user->isSuperAdmin()) {
        return redirect()->route('super-admin.dashboard');
    } elseif ($user->isViewer()) {
        return redirect()->route('viewer.dashboard');
    } elseif ($user->isProgramAdmin() || $user->isProjectOfficer() || $user->isSupervisor()) {
        return redirect()->route('program-admin.dashboard');
    } else {
        return redirect()->route('volunteer.dashboard');
    }
})->middleware(['auth'])->name('dashboard');

// Auth only Profile actions
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// SUPER ADMIN routes
Route::middleware(['auth', 'role:super-admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    
    // Programs Manager
    Route::get('/programs', [SuperAdminController::class, 'programs'])->name('programs.index');
    Route::post('/programs/store', [SuperAdminController::class, 'storeProgram'])->name('programs.store');
    Route::post('/programs/update/{id}', [SuperAdminController::class, 'updateProgram'])->name('programs.update');
    Route::post('/programs/delete/{id}', [SuperAdminController::class, 'destroyProgram'])->name('programs.delete');
    
    // Program Timelines
    Route::get('/programs/{programId}/timelines', [SuperAdminController::class, 'programTimelines'])->name('programs.timelines');
    Route::post('/programs/{programId}/timelines/store', [SuperAdminController::class, 'storeProgramTimeline'])->name('programs.timelines.store');
    Route::post('/programs/timelines/update/{id}', [SuperAdminController::class, 'updateProgramTimeline'])->name('programs.timelines.update');
    Route::post('/programs/timelines/delete/{id}', [SuperAdminController::class, 'destroyProgramTimeline'])->name('programs.timelines.delete');

    // Impact Experience & Public Identity Manager (Banner, Cover, Logo, KPI Graphic, Before & After)
    Route::get('/programs/{programId}/impact-experience', [SuperAdminController::class, 'impactExperienceManager'])->name('programs.impact-experience');
    Route::post('/programs/{programId}/impact-experience/identity', [SuperAdminController::class, 'updateProgramIdentity'])->name('programs.impact.identity');
    Route::post('/programs/{programId}/kpi/store', [SuperAdminController::class, 'storeKpi'])->name('programs.kpi.store');
    Route::post('/programs/kpi/update/{id}', [SuperAdminController::class, 'updateKpi'])->name('programs.kpi.update');
    Route::post('/programs/kpi/delete/{id}', [SuperAdminController::class, 'destroyKpi'])->name('programs.kpi.delete');
    Route::post('/programs/{programId}/before-after/store', [SuperAdminController::class, 'storeBeforeAfter'])->name('programs.before-after.store');
    Route::post('/programs/before-after/update/{id}', [SuperAdminController::class, 'updateBeforeAfter'])->name('programs.before-after.update');
    Route::post('/programs/before-after/delete/{id}', [SuperAdminController::class, 'destroyBeforeAfter'])->name('programs.before-after.delete');

    // Users Management
    Route::get('/users', [SuperAdminController::class, 'users'])->name('users.index');
    Route::post('/users/store', [SuperAdminController::class, 'storeUser'])->name('users.store');
    Route::post('/users/update/{id}', [SuperAdminController::class, 'updateUser'])->name('users.update');
    Route::post('/users/delete/{id}', [SuperAdminController::class, 'destroyUser'])->name('users.delete');
    Route::post('/users/toggle/{id}', [SuperAdminController::class, 'toggleUserStatus'])->name('users.toggle');
    Route::post('/users/assign', [SuperAdminController::class, 'assignUser'])->name('users.assign');
    
    // Audit logs & compliance audits
    Route::get('/audit-logs', [SuperAdminController::class, 'auditLogs'])->name('audit-logs.index');
    Route::get('/audits', [SuperAdminController::class, 'audits'])->name('audits.index');
    
    // Zones Geofence
    Route::get('/zones', [SuperAdminController::class, 'zones'])->name('zones.index');
    Route::post('/zones/store', [SuperAdminController::class, 'storeZone'])->name('zones.store');
    Route::post('/zones/update/{id}', [SuperAdminController::class, 'updateZone'])->name('zones.update');
    Route::post('/zones/delete/{id}', [SuperAdminController::class, 'destroyZone'])->name('zones.delete');
    
    // Forms Engine Manager
    Route::get('/forms', [SuperAdminController::class, 'forms'])->name('forms.index');
    Route::post('/forms/store', [SuperAdminController::class, 'storeForm'])->name('forms.store');
});

// PROGRAM ADMIN routes
Route::middleware(['auth', 'role:program-admin,project-officer,supervisor'])->prefix('program-admin')->name('program-admin.')->group(function () {
    Route::get('/dashboard', [ProgramAdminController::class, 'dashboard'])->name('dashboard');
    
    // Workspace: Project Manager
    Route::get('/project-manager', [ProgramAdminController::class, 'projectManagerIndex'])->name('project-manager.index');
    Route::post('/program/store', [ProgramAdminController::class, 'createProgram'])->name('program.store');
    Route::get('/charter/{programId}', [ProgramAdminController::class, 'showProgramCharter'])->name('charter.show');
    Route::post('/charter/update/{programId}', [ProgramAdminController::class, 'updateProgramCharter'])->name('charter.update');
    Route::get('/workplans/{programId}', [ProgramAdminController::class, 'showWorkPlans'])->name('workplans.show');
    Route::post('/workplans/store/{programId}', [ProgramAdminController::class, 'storeWorkPlan'])->name('workplans.store');
    
    // Workspace: Operations
    Route::get('/operations', [ProgramAdminController::class, 'operationsIndex'])->name('operations.index');
    Route::post('/activity/store', [ProgramAdminController::class, 'storeActivity'])->name('activity.store');
    Route::post('/activity/update/{id}', [ProgramAdminController::class, 'updateActivity'])->name('activity.update');
    Route::delete('/activity/delete/{id}', [ProgramAdminController::class, 'deleteActivity'])->name('activity.delete');
    
    // Daily Group Carbon Logs
    Route::get('/carbon/fill', [CarbonLogController::class, 'adminFill'])->name('carbon.fill');
    Route::post('/carbon/store', [CarbonLogController::class, 'adminStore'])->name('carbon.store');

    // New Operations Workspaces
    Route::get('/operations/timeline', [ProgramAdminController::class, 'timelineIndex'])->name('operations.timeline');
    Route::get('/operations/routines', [ProgramAdminController::class, 'routinesIndex'])->name('operations.routines');
    Route::get('/operations/additional-logs', [ProgramAdminController::class, 'additionalLogsIndex'])->name('operations.additional-logs');

    // Approvals: Attendance, Submissions, Logbooks
    Route::get('/attendance/validation', [ProgramAdminController::class, 'attendanceValidation'])->name('attendance.validation');
    Route::post('/attendance/approve/{id}', [ProgramAdminController::class, 'approveAttendance'])->name('attendance.approve');
    Route::get('/attendance/print', [ProgramAdminController::class, 'printAttendance'])->name('attendance.print');
    
    Route::get('/tasks/approval', [ProgramAdminController::class, 'taskApproval'])->name('tasks.approval');
    Route::post('/tasks/approve/{id}', [ProgramAdminController::class, 'approveTaskSubmission'])->name('tasks.approve');
    
    Route::get('/logbooks/approval', [ProgramAdminController::class, 'logbookApproval'])->name('logbooks.approval');
    Route::post('/logbooks/approve/{id}', [ProgramAdminController::class, 'approveLogbook'])->name('logbooks.approve');
    
    // Workspace: Finance
    Route::get('/finance', [ProgramAdminController::class, 'financeIndex'])->name('finance.index');
    Route::post('/finance/transaction/store', [ProgramAdminController::class, 'storeTransaction'])->name('finance.transaction.store');
    Route::post('/finance/transaction/update/{id}', [ProgramAdminController::class, 'updateTransaction'])->name('finance.transaction.update');
    Route::delete('/finance/transaction/delete/{id}', [ProgramAdminController::class, 'deleteTransaction'])->name('finance.transaction.delete');
    Route::post('/finance/pagu/store', [ProgramAdminController::class, 'storeBudgetCeiling'])->name('finance.pagu.store');
    Route::delete('/finance/pagu/delete/{id}', [ProgramAdminController::class, 'deleteBudgetCeiling'])->name('finance.pagu.delete');
    
    // Workspace: Administration
    Route::get('/administration', [ProgramAdminController::class, 'administrationIndex'])->name('administration.index');
    Route::get('/lpj/{programId}', [ProgramAdminController::class, 'viewLpjReport'])->name('lpj.show');
    Route::get('/certificate/print/{programId}/{userId}', [ProgramAdminController::class, 'viewCertificatePrint'])->name('certificate.print');

    // New Administration Workspaces
    Route::get('/administration/resources', [ProgramAdminController::class, 'resourcesIndex'])->name('administration.resources');
    Route::get('/administration/certificates', [ProgramAdminController::class, 'certificatesIndex'])->name('administration.certificates');
    Route::get('/administration/facilities', [ProgramAdminController::class, 'facilitiesIndex'])->name('administration.facilities');
    Route::get('/administration/complaints', [ProgramAdminController::class, 'complaintsIndex'])->name('administration.complaints');
    Route::get('/administration/reprimands', [ProgramAdminController::class, 'reprimandsIndex'])->name('administration.reprimands');
    Route::get('/administration/daily-reports', [ProgramAdminController::class, 'dailyReportsIndex'])->name('administration.daily-reports');
    Route::get('/administration/todos', [ProgramAdminController::class, 'todos'])->name('administration.todos');

    // WBS Targets Output
    Route::post('/workplans/target/update/{id}', [ProgramAdminController::class, 'updateWbsTarget'])->name('workplans.target.update');
    Route::post('/workplans/generate-qr/{id}', [ProgramAdminController::class, 'generateWorkPlanQr'])->name('workplans.generate-qr');

    // Profile & Signature Settings
    Route::get('/settings/profile', [ProgramAdminController::class, 'showProfileSettings'])->name('settings.profile');
    Route::post('/settings/profile/update', [ProgramAdminController::class, 'updateProfileSettings'])->name('settings.profile.update');

    // Calendar Agendas
    Route::post('/agendas/store', [ProgramAdminController::class, 'storeAgenda'])->name('agendas.store');

    // Routine Tasks
    Route::post('/routines/store', [ProgramAdminController::class, 'storeRoutine'])->name('routines.store');

    // Additional Activities Reviews
    Route::post('/additional-activity/approve/{id}', [ProgramAdminController::class, 'approveAdditionalActivity'])->name('additional-activity.approve');
    Route::post('/additional-activity/reject/{id}', [ProgramAdminController::class, 'rejectAdditionalActivity'])->name('additional-activity.reject');

    // Template resources
    Route::post('/resources/store', [ProgramAdminController::class, 'storeResource'])->name('resources.store');
    Route::post('/resources/update/{id}', [ProgramAdminController::class, 'updateResource'])->name('resources.update');
    Route::delete('/resources/delete/{id}', [ProgramAdminController::class, 'deleteResource'])->name('resources.delete');

    // Participant Documents upload
    Route::post('/participant-documents/store', [ProgramAdminController::class, 'storeParticipantDocument'])->name('participant-documents.store');
    Route::post('/participant-documents/update/{id}', [ProgramAdminController::class, 'updateParticipantDocument'])->name('participant-documents.update');
    Route::delete('/participant-documents/delete/{id}', [ProgramAdminController::class, 'deleteParticipantDocument'])->name('participant-documents.delete');

    // Facilities Allocations
    Route::post('/facilities/store', [ProgramAdminController::class, 'storeFacility'])->name('facilities.store');
    Route::post('/facilities/update/{id}', [ProgramAdminController::class, 'updateFacility'])->name('facilities.update');
    Route::delete('/facilities/delete/{id}', [ProgramAdminController::class, 'deleteFacility'])->name('facilities.delete');
    Route::post('/facilities/allocate', [ProgramAdminController::class, 'allocateFacility'])->name('facilities.allocate');

    // Complaints Actions
    Route::post('/complaints/respond/{id}', [ProgramAdminController::class, 'respondComplaint'])->name('complaints.respond');

    // Reprimands Logging
    Route::post('/reprimands/store', [ProgramAdminController::class, 'storeReprimand'])->name('reprimands.store');
    Route::post('/reprimands/update/{id}', [ProgramAdminController::class, 'updateReprimand'])->name('reprimands.update');
    Route::delete('/reprimands/delete/{id}', [ProgramAdminController::class, 'deleteReprimand'])->name('reprimands.delete');

    // Daily Situational Reports
    Route::post('/daily-reports/store', [ProgramAdminController::class, 'storeDailyReport'])->name('daily-reports.store');
    Route::post('/daily-reports/update/{id}', [ProgramAdminController::class, 'updateDailyReport'])->name('daily-reports.update');
    Route::delete('/daily-reports/delete/{id}', [ProgramAdminController::class, 'deleteDailyReport'])->name('daily-reports.delete');

    // Admin To-Dos
    Route::post('/todos/admin/store', [ProgramAdminController::class, 'storeAdminTodo'])->name('todos.admin.store');
    Route::post('/todos/approve/{id}', [ProgramAdminController::class, 'approveTodo'])->name('todos.approve');
    Route::post('/todos/reject/{id}', [ProgramAdminController::class, 'rejectTodo'])->name('todos.reject');
    Route::delete('/todos/delete/{id}', [ProgramAdminController::class, 'deleteTodo'])->name('todos.delete');

    // Inventory Management
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/store', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/show/{id}', [InventoryController::class, 'show'])->name('inventory.show');
    Route::post('/inventory/update/{id}', [InventoryController::class, 'update'])->name('inventory.update');

    // Admin Presensi Volunteer QR Scanning
    Route::get('/scan-volunteer', [SubProgramAttendanceController::class, 'showVolunteerScanner'])->name('scan-volunteer.index');
    Route::post('/scan-volunteer/submit', [SubProgramAttendanceController::class, 'submitVolunteerScan'])->name('scan-volunteer.submit');
});

// PARTICIPANT (VOLUNTEER) routes
Route::middleware(['auth', 'role:participant,volunteer,peserta'])->prefix('volunteer')->name('volunteer.')->group(function () {
    Route::get('/dashboard', [VolunteerController::class, 'dashboard'])->name('dashboard');
    
    // Attendance Actions
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/check-in-daily', [AttendanceController::class, 'showDailyCheckIn'])->name('attendance.check-in-daily');
    Route::post('/attendance/check-in-daily/store', [AttendanceController::class, 'dailyCheckIn'])->name('attendance.check-in-daily.store');
    Route::get('/attendance/check-in/{activityId}', [AttendanceController::class, 'showCheckIn'])->name('attendance.check-in');
    Route::post('/attendance/check-in/store/{activityId}', [AttendanceController::class, 'checkIn'])->name('attendance.check-in.store');
    Route::get('/attendance/check-out/{attendanceId}', [AttendanceController::class, 'showCheckOut'])->name('attendance.check-out');
    Route::post('/attendance/check-out/store/{attendanceId}', [AttendanceController::class, 'checkOut'])->name('attendance.check-out.store');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
    
    // Daily Carbon Logs (Individual)
    Route::get('/carbon/fill', [CarbonLogController::class, 'volunteerFill'])->name('carbon.fill');
    Route::post('/carbon/store', [CarbonLogController::class, 'volunteerStore'])->name('carbon.store');

    // Tasks Submission
    Route::get('/tasks', [VolunteerController::class, 'tasks'])->name('tasks.index');
    Route::get('/tasks/show/{id}', [VolunteerController::class, 'showTask'])->name('tasks.show');
    Route::post('/tasks/submit/{id}', [VolunteerController::class, 'submitTask'])->name('tasks.submit');
    
    // Logbooks
    Route::get('/logbooks', [VolunteerController::class, 'logbooks'])->name('logbooks.index');
    Route::post('/logbooks/store', [VolunteerController::class, 'storeLogbook'])->name('logbooks.store');
    
    // Certificates
    Route::get('/certificates', [VolunteerController::class, 'certificates'])->name('certificates.index');

    // Profile view (User QR & Program QR)
    Route::get('/profile', [VolunteerController::class, 'profile'])->name('profile');
    
    // Calendar & Carbon ESG tracking
    Route::get('/calendar', [VolunteerController::class, 'calendar'])->name('calendar.index');
    Route::get('/carbon', [VolunteerController::class, 'carbonIndex'])->name('carbon.index');

    // New Personal Execution Workspace Modules
    Route::get('/workspace/calendar', [VolunteerWorkspaceController::class, 'calendar'])->name('workspace.calendar');
    Route::get('/workspace/tasks/{id}', [VolunteerWorkspaceController::class, 'showTask'])->name('workspace.tasks.show');
    Route::post('/workspace/tasks/submit/{id}', [VolunteerWorkspaceController::class, 'submitTask'])->name('workspace.tasks.submit');
    Route::get('/workspace/additional-activity', [VolunteerWorkspaceController::class, 'additionalActivities'])->name('workspace.additional.index');
    Route::post('/workspace/additional-activity/store', [VolunteerWorkspaceController::class, 'storeAdditionalActivity'])->name('workspace.additional.store');
    Route::get('/workspace/resources', [VolunteerWorkspaceController::class, 'resources'])->name('workspace.resources.index');
    Route::get('/workspace/documents', [VolunteerWorkspaceController::class, 'documents'])->name('workspace.documents.index');
    Route::get('/workspace/complaints', [VolunteerWorkspaceController::class, 'complaints'])->name('workspace.complaints.index');
    Route::post('/workspace/complaints/store', [VolunteerWorkspaceController::class, 'storeComplaint'])->name('workspace.complaints.store');
    Route::get('/workspace/logbooks/print', [VolunteerWorkspaceController::class, 'printLogbook'])->name('workspace.logbooks.print');
    Route::get('/workspace/reprimands', [VolunteerWorkspaceController::class, 'reprimands'])->name('workspace.reprimands.index');
    Route::get('/workspace/facilities', [VolunteerWorkspaceController::class, 'facilities'])->name('workspace.facilities.index');
    
    // Routine Activities
    Route::get('/workspace/routines', [VolunteerWorkspaceController::class, 'routinesIndex'])->name('workspace.routines.index');
    Route::post('/workspace/routines/submit/{id}', [VolunteerWorkspaceController::class, 'submitRoutine'])->name('workspace.routines.submit');
    Route::get('/workspace/routines/submit/{id}', function() {
        return redirect()->route('volunteer.workspace.routines.index')->with('error', 'Akses metode pengiriman salah atau sesi kedaluwarsa.');
    });

    // Volunteer To-Dos
    Route::get('/workspace/todos', [VolunteerWorkspaceController::class, 'todosIndex'])->name('workspace.todos.index');
    Route::post('/workspace/todos/store', [VolunteerWorkspaceController::class, 'storeTodo'])->name('workspace.todos.store');
    Route::post('/workspace/todos/submit/{id}', [VolunteerWorkspaceController::class, 'submitTodo'])->name('workspace.todos.submit');
    Route::delete('/workspace/todos/delete/{id}', [VolunteerWorkspaceController::class, 'deleteTodo'])->name('workspace.todos.delete');
    
    // Photo Documentation Upload
    Route::post('/documentation/upload', [DocumentationController::class, 'upload'])->name('documentation.upload');

    // Sub Program QR Attendance
    Route::post('/subprogram-attendance/scan', [SubProgramAttendanceController::class, 'scan'])->name('subprogram.attendance.scan');
});

// VIEWER routes
Route::middleware(['auth', 'role:viewer'])->prefix('viewer')->name('viewer.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\ViewerController::class, 'dashboard'])->name('dashboard');
    Route::get('/programs/{id}', [\App\Http\Controllers\ViewerController::class, 'showProgram'])->name('programs.show');
});

require __DIR__.'/auth.php';
