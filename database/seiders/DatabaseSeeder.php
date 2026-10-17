<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Zone;
use App\Models\CustomForm;
use App\Models\Program;
use App\Models\ProjectCharter;
use App\Models\ProgramKerja;
use App\Models\Kegiatan;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\FinanceTransaction;
use App\Models\Logbook;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Core Users
        $superAdmin = User::create([
            'name' => 'Yota Head of CSR (Super Admin)',
            'email' => 'admin@yota.com',
            'password' => Hash::make('password'),
            'role' => 'super-admin',
            'phone' => '+62811111111',
            'status' => 'active',
        ]);

        $progAdmin = User::create([
            'name' => 'Yota Project Coordinator (Program Admin)',
            'email' => 'coordinator@yota.com',
            'password' => Hash::make('password'),
            'role' => 'program-admin',
            'phone' => '+62822222222',
            'status' => 'active',
        ]);

        $volunteer = User::create([
            'name' => 'Andi Wijaya (Participant)',
            'email' => 'volunteer@yota.com',
            'password' => Hash::make('password'),
            'role' => 'participant',
            'phone' => '+62833333333',
            'status' => 'active',
        ]);

        // 2. Create Geofence Zone
        // Polygon around Jakarta Bay Mangrove area
        $zone = Zone::create([
            'name' => 'Mangrove Shore Conservation Zone A',
            'description' => 'Geofence area for planting mangroves in PIK shore.',
            'type' => 'attendance',
            'geojson' => [
                [-6.089000, 106.720000],
                [-6.089000, 106.750000],
                [-6.110000, 106.750000],
                [-6.110000, 106.720000]
            ],
        ]);

        // 3. Create Custom Form Schema
        $customForm = CustomForm::create([
            'name' => 'Laporan Penanaman Bibit Bakau',
            'code' => 'mangrove-plantation-log',
            'description' => 'Form pelaporan bibit bakau yang berhasil ditanam.',
            'target_type' => 'task',
            'fields' => [
                [
                    'name' => 'bibit_terdarat',
                    'label' => 'Jumlah Bibit Tanam (Pcs)',
                    'type' => 'number',
                    'required' => true,
                ],
                [
                    'name' => 'carbon_offset',
                    'label' => 'Carbon Offset (Kg CO2e)',
                    'type' => 'number',
                    'required' => true,
                    'default' => '25',
                ],
                [
                    'name' => 'kondisi_tanah',
                    'label' => 'Kondisi Tanah Rawa',
                    'type' => 'select',
                    'options' => ['Sangat Basah', 'Kering / Retak', 'Berlumpur Tebal'],
                    'required' => true,
                ],
                [
                    'name' => 'lokasi_gps',
                    'label' => 'Koordinat Tanam',
                    'type' => 'gps',
                    'required' => true,
                ]
            ]
        ]);

        // 4. Create Program
        $program = Program::create([
            'name' => 'Yota Green Coastal Project 2026',
            'description' => 'Program restorasi ekosistem pesisir utara Jakarta untuk penyerapan emisi carbon dan perlindungan pemukiman warga dari abrasi.',
            'cover_image' => null,
            'status' => 'active',
            'start_date' => Carbon::now()->subMonths(1),
            'end_date' => Carbon::now()->addMonths(5),
            'settings' => [
                'workflow' => ['stages' => ['draft', 'active', 'completed']],
            ],
            'stats' => [
                'volunteers_count' => 12,
                'beneficiaries_count' => 1500,
                'carbon_offset' => 150,
            ]
        ]);

        // 5. Create Project Charter
        ProjectCharter::create([
            'program_id' => $program->id,
            'vision' => 'Pulihnya ekosistem pesisir hijau dengan pemberdayaan nelayan lokal.',
            'mission' => 'Menanam 10.000 bibit mangrove dan melatih 100 nelayan pengolah buah mangrove.',
            'background' => 'Abrasi pesisir utara semakin meningkat akibat ketiadaan benteng alami bakau.',
            'scope' => 'Pesisir Pantai Indah Kapuk dan Kamal Muara.',
            'budget' => 250000000.00,
            'stakeholder_info' => ['Nelayan Kamal Muara', 'Dinas Lingkungan Hidup DKI Jakarta'],
        ]);

        // 6. Create Work Plan (Program Kerja WBS)
        $workplan = ProgramKerja::create([
            'program_id' => $program->id,
            'name' => 'Penanaman Mangrove Shoreline',
            'description' => 'Fase 1 penanaman benteng alami pesisir pantai.',
            'start_date' => Carbon::now()->subDays(10),
            'end_date' => Carbon::now()->addDays(20),
            'budget' => 75000000.00,
            'status' => 'active',
        ]);

        // 7. Create Activity (Kegiatan)
        $activity = Kegiatan::create([
            'work_plan_id' => $workplan->id,
            'name' => 'Penyebaran Bibit Mangrove Bakau',
            'description' => 'Pemberian dan penanaman bersama relawan di Kamal Muara.',
            'start_date' => Carbon::now()->subDays(2),
            'end_date' => Carbon::now()->addDays(5),
            'status' => 'active',
            'zone_id' => $zone->id, // Linked to Geofence Zone
        ]);

        // 8. Create Task
        $task = Task::create([
            'activity_id' => $activity->id,
            'title' => 'Laporan Penanaman 50 Mangrove',
            'description' => 'Tanam mangrove di zona yang ditentukan, laporkan jumlah bibit ditanam dan koordinat tanam beserta selfie.',
            'due_date' => Carbon::now()->addDays(3),
            'status' => 'pending',
            'type' => 'individual',
            'assigned_to' => [$volunteer->id],
            'form_id' => $customForm->id,
        ]);

        // 9. Seed mock finance transactions
        FinanceTransaction::create([
            'program_id' => $program->id,
            'title' => 'Pembelian 1000 Bibit Mangrove Bakau',
            'description' => 'Bibit unggul dari pembudidaya Kamal Muara.',
            'amount' => 15000000.00,
            'type' => 'expense',
            'category' => 'logistics',
            'status' => 'approved',
            'created_by' => $progAdmin->id,
            'approved_by' => $superAdmin->id,
        ]);

        FinanceTransaction::create([
            'program_id' => $program->id,
            'title' => 'Sponsor Dana CSR Bakrie Group',
            'description' => 'Pendanaan hibah fase pertama proyek.',
            'amount' => 50000000.00,
            'type' => 'income',
            'category' => 'operational',
            'status' => 'approved',
            'created_by' => $progAdmin->id,
            'approved_by' => $superAdmin->id,
        ]);

        // 10. Seed mock Volunteer Logbook
        Logbook::create([
            'user_id' => $volunteer->id,
            'program_id' => $program->id,
            'title' => 'Membantu Mengangkut Bibit',
            'content' => 'Membantu nelayan lokal memindahkan 500 bibit mangrove dari perahu ke dermaga Kamal Muara.',
            'date' => Carbon::now()->subDays(1),
            'hours_spent' => 4.5,
            'status' => 'approved',
        ]);
    }
}
