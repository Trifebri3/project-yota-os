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
use App\Models\ProgramUserAssignment;
use App\Models\ActivityLog;
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
        // 1. Create Core Users for 6 Roles
        $superAdmin = User::create([
            'name' => 'Yota Head of CSR (Super Admin)',
            'email' => 'admin@yota.com',
            'password' => Hash::make('password'),
            'role' => 'super-admin',
            'phone' => '+62811111111',
            'organisasi' => 'Yota Foundation',
            'status' => 'active',
        ]);

        $progAdmin = User::create([
            'name' => 'Yota Project Coordinator (Program Admin)',
            'email' => 'coordinator@yota.com',
            'password' => Hash::make('password'),
            'role' => 'program-admin',
            'phone' => '+62822222222',
            'organisasi' => 'Yota Foundation',
            'status' => 'active',
        ]);

        $projectOfficer = User::create([
            'name' => 'Budi Santoso (Project Officer)',
            'email' => 'officer@yota.com',
            'password' => Hash::make('password'),
            'role' => 'project-officer',
            'phone' => '+62833333333',
            'organisasi' => 'LSM Hijau Bumi',
            'status' => 'active',
        ]);

        $supervisor = User::create([
            'name' => 'Siti Aminah (Supervisor)',
            'email' => 'supervisor@yota.com',
            'password' => Hash::make('password'),
            'role' => 'supervisor',
            'phone' => '+62844444444',
            'organisasi' => 'Dinas Lingkungan Hidup',
            'status' => 'active',
        ]);

        $peserta = User::create([
            'name' => 'Rian Hidayat (Peserta)',
            'email' => 'peserta@yota.com',
            'password' => Hash::make('password'),
            'role' => 'peserta',
            'phone' => '+62855555555',
            'organisasi' => 'Karang Taruna Kamal',
            'status' => 'active',
        ]);

        $volunteer = User::create([
            'name' => 'Andi Wijaya (Volunteer)',
            'email' => 'volunteer@yota.com',
            'password' => Hash::make('password'),
            'role' => 'volunteer',
            'phone' => '+62866666666',
            'organisasi' => 'Universitas Indonesia',
            'status' => 'active',
        ]);

        // 2. Create Program: Collaborative Action Purbayani 2026
        $program = Program::create([
            'name' => 'Collaborative Action Purbayani 2026',
            'description' => 'Restorasi kawasan pesisir Purbayani melalui aksi penanaman pohon mangrove, pembersihan sampah plastik, dan pengembangan UMKM ramah lingkungan.',
            'cover_image' => null,
            'kategori_program' => 'Lingkungan',
            'status' => 'aktif',
            'start_date' => Carbon::now()->subMonths(1),
            'end_date' => Carbon::now()->addMonths(5),
            'project_officer_id' => $projectOfficer->id,
            'supervisor_id' => $supervisor->id,
            'mitra' => 'DLH Jawa Barat',
            'sponsor' => 'Pertamina CSR',
            'organisasi_pelaksana' => 'Yota Foundation',
            'tujuan_program' => 'Membangun ketahanan ekosistem pesisir dan meningkatkan literasi karbon warga lokal.',
            'target_capaian' => '10.000 mangrove tertanam, reduction 500 Kg CO2e emisi harian, 2 ton sampah terolah.',
            'stats' => [
                'volunteers_count' => 15,
                'beneficiaries_count' => 800,
                'carbon_offset' => 120,
            ],
            'settings' => [
                'workflow' => ['stages' => ['draft', 'active', 'completed']],
            ]
        ]);

        // 3. Create Program-specific Geofence Zones
        // Zone 1: Pantai Rancabuaya
        $zone1 = Zone::create([
            'program_id' => $program->id,
            'name' => 'Zona Pantai Rancabuaya',
            'description' => 'Area utama penanaman mangrove dan bersih pantai.',
            'type' => 'attendance',
            'status_zona' => 'aktif',
            'geojson' => [
                [-6.089000, 106.720000],
                [-6.089000, 106.750000],
                [-6.110000, 106.750000],
                [-6.110000, 106.720000]
            ],
        ]);

        // Zone 2: Bale Resik
        $zone2 = Zone::create([
            'program_id' => $program->id,
            'name' => 'Zona Bale Resik',
            'description' => 'Pusat pemilahan sampah organik dan anorganik.',
            'type' => 'activity',
            'status_zona' => 'monitoring',
            'geojson' => [
                [-6.120000, 106.730000],
                [-6.120000, 106.760000],
                [-6.140000, 106.760000],
                [-6.140000, 106.730000]
            ],
        ]);

        // 4. Create Pivot User Assignments
        ProgramUserAssignment::create([
            'user_id' => $volunteer->id,
            'program_id' => $program->id,
            'zone_id' => $zone1->id,
        ]);

        ProgramUserAssignment::create([
            'user_id' => $peserta->id,
            'program_id' => $program->id,
            'zone_id' => $zone2->id,
        ]);

        // 5. Create Custom Form Schema for Carbon Logs
        $customForm = CustomForm::create([
            'name' => 'Form Pengukuran Karbon Harian',
            'code' => 'carbon-purbayani',
            'description' => 'Form pelaporan emisi perjalanan dan pengurangan karbon dari pengolahan sampah/penanaman.',
            'target_type' => 'program-' . $program->id,
            'fields' => [
                [
                    'name' => 'motor_km',
                    'label' => 'Perjalanan Motor (Km)',
                    'type' => 'number',
                    'required' => true,
                ],
                [
                    'name' => 'mobil_km',
                    'label' => 'Perjalanan Mobil (Km)',
                    'type' => 'number',
                    'required' => false,
                ],
                [
                    'name' => 'listrik_jam',
                    'label' => 'Listrik Dinyalakan (Jam)',
                    'type' => 'number',
                    'required' => true,
                ],
                [
                    'name' => 'sampah_organik_kg',
                    'label' => 'Sampah Organik Diolah (Kg)',
                    'type' => 'number',
                    'required' => true,
                ],
                [
                    'name' => 'pohon_ditanam',
                    'label' => 'Pohon Mangrove Ditanam (Batang)',
                    'type' => 'number',
                    'required' => true,
                ],
                [
                    'name' => 'lokasi_gps',
                    'label' => 'Koordinat Lapangan',
                    'type' => 'gps',
                    'required' => true,
                ]
            ]
        ]);

        // 6. Create Work Plan (WBS)
        $workplan = ProgramKerja::create([
            'program_id' => $program->id,
            'name' => 'Fase Restorasi Pesisir',
            'description' => 'Pembersihan pantai dan penanaman vegetasi mangrove.',
            'start_date' => Carbon::now()->subDays(10),
            'end_date' => Carbon::now()->addDays(20),
            'budget' => 45000000.00,
            'status' => 'active',
        ]);

        // 7. Create Kegiatan (Activity)
        $activity = Kegiatan::create([
            'work_plan_id' => $workplan->id,
            'name' => 'Aksi Tanam Bakau Purbayani',
            'description' => 'Aksi penanaman bersama relawan hijau di Pantai Rancabuaya.',
            'start_date' => Carbon::now()->subDays(2),
            'end_date' => Carbon::now()->addDays(5),
            'status' => 'active',
            'zone_id' => $zone1->id,
        ]);

        // 8. Create Task with Carbon custom form
        $task = Task::create([
            'activity_id' => $activity->id,
            'title' => 'Laporan Harian Karbon & Daur Ulang',
            'description' => 'Laporkan kilometer berkendara, durasi listrik, dan jumlah bakau yang ditanam di Pantai Rancabuaya.',
            'due_date' => Carbon::now()->addDays(3),
            'status' => 'pending',
            'type' => 'individual',
            'assigned_to' => [$volunteer->id, $peserta->id],
            'form_id' => $customForm->id,
        ]);

        // 9. Add mock finance transactions
        FinanceTransaction::create([
            'program_id' => $program->id,
            'title' => 'Pembelian Alat Komposting & Karung Sampah',
            'description' => 'Pembelian drum komposter untuk Zona Bale Resik.',
            'amount' => 5500000.00,
            'type' => 'expense',
            'category' => 'logistics',
            'status' => 'approved',
            'created_by' => $progAdmin->id,
            'approved_by' => $superAdmin->id,
        ]);

        // 10. Fill audit trail records
        ActivityLog::create([
            'user_id' => $superAdmin->id,
            'action' => 'publish',
            'model_type' => CustomForm::class,
            'model_id' => $customForm->id,
            'new_values' => ['name' => $customForm->name],
        ]);
    }
}
