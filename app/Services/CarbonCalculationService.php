<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\Program;
use App\Models\CarbonLog;

class CarbonCalculationService
{
    /**
     * Calculate carbon metrics for a program based on volunteer submissions and daily carbon logs.
     *
     * Coefficients (Kg CO2e):
     * - Motor: +0.1 per km
     * - Mobil: +0.2 per km
     * - Angkutan umum: +0.05 per km
     * - Listrik (lampu/kipas/AC): +0.5 per hour
     * - Sampah organik diolah: -2.0 per kg
     * - Sampah anorganik didaur ulang: -1.5 per kg
     * - Pohon ditanam: -50.0 per tree
     * - Bibit dirawat: -10.0 per tree
     *
     * @param string $programId
     * @return array
     */
    public function getProgramMetrics(string $programId): array
    {
        // 1. Calculate from Task submissions
        $submissions = Submission::whereHas('task.activity.workPlan', function ($query) use ($programId) {
            $query->where('program_id', $programId);
        })->where('status', 'approved')->get();

        $totalEmissions = 0.0;
        $totalOffsets = 0.0;
        $totalTrees = 0;
        $totalWaste = 0.0; // Kg

        foreach ($submissions as $sub) {
            $data = $sub->content_data;
            if (empty($data)) continue;

            // Mobility Emissions
            if (isset($data['motor_km'])) {
                $totalEmissions += (float)$data['motor_km'] * 0.1;
            }
            if (isset($data['mobil_km'])) {
                $totalEmissions += (float)$data['mobil_km'] * 0.2;
            }
            if (isset($data['umum_km'])) {
                $totalEmissions += (float)$data['umum_km'] * 0.05;
            }

            // Energy Emissions
            if (isset($data['listrik_jam'])) {
                $totalEmissions += (float)$data['listrik_jam'] * 0.5;
            }
            if (isset($data['ac_jam'])) {
                $totalEmissions += (float)$data['ac_jam'] * 0.8;
            }

            // Waste Offsets (Recycled/Composted)
            if (isset($data['sampah_organik_kg'])) {
                $weight = (float)$data['sampah_organik_kg'];
                $totalOffsets += $weight * 2.0;
                $totalWaste += $weight;
            }
            if (isset($data['sampah_anorganik_kg'])) {
                $weight = (float)$data['sampah_anorganik_kg'];
                $totalOffsets += $weight * 1.5;
                $totalWaste += $weight;
            }

            // Planting Offsets
            if (isset($data['pohon_ditanam'])) {
                $count = (int)$data['pohon_ditanam'];
                $totalOffsets += $count * 50.0;
                $totalTrees += $count;
            }
            if (isset($data['bibit_dirawat'])) {
                $count = (int)$data['bibit_dirawat'];
                $totalOffsets += $count * 10.0;
            }

            if (isset($data['carbon_offset'])) {
                $totalOffsets += (float)$data['carbon_offset'];
            }
        }

        // 2. Calculate from Daily Carbon Logs
        $logs = CarbonLog::where('program_id', $programId)->get();
        foreach ($logs as $log) {
            $totalEmissions += (float)$log->emissions;
            $totalOffsets += (float)$log->offsets;

            $data = $log->content_data;
            if (empty($data)) continue;

            if (isset($data['sampah_organik_kg'])) {
                $totalWaste += (float)$data['sampah_organik_kg'];
            }
            if (isset($data['sampah_anorganik_kg'])) {
                $totalWaste += (float)$data['sampah_anorganik_kg'];
            }
            if (isset($data['pohon_ditanam'])) {
                $totalTrees += (int)$data['pohon_ditanam'];
            }
        }

        $balance = $totalOffsets - $totalEmissions;

        return [
            'total_emissions' => $totalEmissions,
            'total_offsets' => $totalOffsets,
            'net_balance' => $balance,
            'total_trees' => $totalTrees,
            'total_waste' => $totalWaste,
        ];
    }
}
