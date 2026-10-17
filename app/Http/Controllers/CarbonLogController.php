<?php

namespace App\Http\Controllers;

use App\Models\CarbonLog;
use App\Models\Program;
use App\Models\CustomForm;
use App\Models\ProgramUserAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CarbonLogController extends Controller
{
    /**
     * Show volunteer daily carbon log form.
     */
    public function volunteerFill()
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();
        
        // 1. Check if logged today
        $filledToday = CarbonLog::where('user_id', $userId)
            ->whereDate('date', $today)
            ->exists();

        // 2. Fetch volunteer assignment to program
        $assignment = ProgramUserAssignment::where('user_id', $userId)->with('program')->first();

        if (!$assignment) {
            return view('volunteer.carbon.fill', [
                'error' => 'Anda belum ditugaskan ke program apa pun oleh Super Admin.',
                'filledToday' => false,
                'form' => null,
                'program' => null,
            ]);
        }

        $program = $assignment->program;

        // 3. Find custom carbon form for this program
        $form = CustomForm::where('target_type', 'program-' . $program->id)->first();

        // Fallback form if not created by Super Admin yet
        if (!$form) {
            $form = (object)[
                'id' => null,
                'name' => 'Formulir Karbon Harian Purbayani',
                'fields' => [
                    ['name' => 'motor_km', 'label' => 'Perjalanan Motor (Km)', 'type' => 'number', 'required' => true],
                    ['name' => 'mobil_km', 'label' => 'Perjalanan Mobil Pribadi (Km)', 'type' => 'number', 'required' => false],
                    ['name' => 'umum_km', 'label' => 'Perjalanan Angkutan Umum (Km)', 'type' => 'number', 'required' => false],
                    ['name' => 'listrik_jam', 'label' => 'Penggunaan Listrik (Jam)', 'type' => 'number', 'required' => true],
                    ['name' => 'ac_jam', 'label' => 'Penggunaan AC (Jam)', 'type' => 'number', 'required' => false],
                    ['name' => 'sampah_organik_kg', 'label' => 'Sampah Organik Diolah (Kg)', 'type' => 'number', 'required' => false],
                    ['name' => 'sampah_anorganik_kg', 'label' => 'Sampah Anorganik Didaur Ulang (Kg)', 'type' => 'number', 'required' => false],
                    ['name' => 'pohon_ditanam', 'label' => 'Jumlah Pohon Ditanam (Batang)', 'type' => 'number', 'required' => false],
                ]
            ];
        }

        return view('volunteer.carbon.fill', compact('filledToday', 'program', 'form'));
    }

    /**
     * Store volunteer daily carbon log.
     */
    public function volunteerStore(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();

        // Prevent double filling
        $alreadyExists = CarbonLog::where('user_id', $userId)
            ->whereDate('date', $today)
            ->exists();

        if ($alreadyExists) {
            return redirect()->back()->withErrors(['date' => 'Anda telah mengisi formulir karbon harian hari ini.']);
        }

        $assignment = ProgramUserAssignment::where('user_id', $userId)->firstOrFail();
        $data = $request->except(['_token', 'form_id']);

        // Calculate emissions & offsets
        $emissions = 0.0;
        $offsets = 0.0;

        if (isset($data['motor_km'])) $emissions += (float)$data['motor_km'] * 0.1;
        if (isset($data['mobil_km'])) $emissions += (float)$data['mobil_km'] * 0.2;
        if (isset($data['umum_km'])) $emissions += (float)$data['umum_km'] * 0.05;
        if (isset($data['listrik_jam'])) $emissions += (float)$data['listrik_jam'] * 0.5;
        if (isset($data['ac_jam'])) $emissions += (float)$data['ac_jam'] * 0.8;

        if (isset($data['sampah_organik_kg'])) $offsets += (float)$data['sampah_organik_kg'] * 2.0;
        if (isset($data['sampah_anorganik_kg'])) $offsets += (float)$data['sampah_anorganik_kg'] * 1.5;
        if (isset($data['pohon_ditanam'])) $offsets += (float)$data['pohon_ditanam'] * 50.0;

        CarbonLog::create([
            'user_id' => $userId,
            'program_id' => $assignment->program_id,
            'form_id' => $request->form_id,
            'type' => 'individual',
            'date' => $today,
            'content_data' => $data,
            'emissions' => $emissions,
            'offsets' => $offsets,
            'net_balance' => $offsets - $emissions,
        ]);

        return redirect()->back()->with('success', 'Jejak Karbon Harian berhasil dikirim!');
    }

    /**
     * Show admin daily group carbon log form.
     */
    public function adminFill()
    {
        $programs = Program::all();
        return view('program-admin.carbon.fill', compact('programs'));
    }

    /**
     * Store admin daily group carbon log.
     */
    public function adminStore(Request $request)
    {
        $request->validate([
            'program_id' => 'required|uuid',
            'date' => 'required|date',
        ]);

        $data = $request->except(['_token', 'program_id', 'date']);

        $emissions = 0.0;
        $offsets = 0.0;

        if (isset($data['motor_km'])) $emissions += (float)$data['motor_km'] * 0.1;
        if (isset($data['mobil_km'])) $emissions += (float)$data['mobil_km'] * 0.2;
        if (isset($data['umum_km'])) $emissions += (float)$data['umum_km'] * 0.05;
        if (isset($data['listrik_jam'])) $emissions += (float)$data['listrik_jam'] * 0.5;
        if (isset($data['ac_jam'])) $emissions += (float)$data['ac_jam'] * 0.8;

        if (isset($data['sampah_organik_kg'])) $offsets += (float)$data['sampah_organik_kg'] * 2.0;
        if (isset($data['sampah_anorganik_kg'])) $offsets += (float)$data['sampah_anorganik_kg'] * 1.5;
        if (isset($data['pohon_ditanam'])) $offsets += (float)$data['pohon_ditanam'] * 50.0;

        CarbonLog::create([
            'user_id' => Auth::id(),
            'program_id' => $request->program_id,
            'type' => 'group',
            'date' => $request->date,
            'content_data' => $data,
            'emissions' => $emissions,
            'offsets' => $offsets,
            'net_balance' => $offsets - $emissions,
        ]);

        return redirect()->back()->with('success', 'Laporan Karbon Kelompok berhasil direkam!');
    }
}
