<?php

namespace App\Services;

use App\Models\Program;
use App\Models\FinanceTransaction;
use App\Models\Submission;
use App\Models\User;

class ReportGeneratorService
{
    /**
     * Generate HTML layout for an LPJ (Laporan Pertanggungjawaban) Report based on program data.
     *
     * @param Program $program
     * @return string
     */
    public function generateLpjHtml(Program $program): string
    {
        $program->load(['projectCharter', 'programKerja.kegiatan.tasks.submissions', 'financeTransactions']);
        
        $charter = $program->projectCharter;
        $workPlans = $program->programKerja;
        $transactions = $program->financeTransactions;

        $totalBudget = $charter ? $charter->budget : 0;
        $totalSpent = $transactions->where('type', 'expense')->where('status', 'approved')->sum('amount');
        $remainingBudget = $totalBudget - $totalSpent;

        // Count volunteer submissions and outcomes
        $totalSubmissions = 0;
        foreach ($workPlans as $wp) {
            foreach ($wp->kegiatan as $keg) {
                foreach ($keg->tasks as $task) {
                    $totalSubmissions += $task->submissions->where('status', 'approved')->count();
                }
            }
        }

        $html = "<div class='max-w-4xl mx-auto p-8 bg-white border border-gray-200 rounded-xl shadow-sm text-gray-800 font-sans'>";
        $html .= "  <div class='text-center border-b pb-6 mb-6'>";
        $html .= "    <h1 class='text-2xl font-bold uppercase tracking-wide text-gray-900'>LAPORAN PERTANGGUNGJAWABAN (LPJ)</h1>";
        $html .= "    <p class='text-lg font-medium text-gray-600 mt-1'>Program Kerja: {$program->name}</p>";
        $html .= "    <p class='text-sm text-gray-400 mt-1'>Periode: " . ($program->start_date?->format('d M Y') ?? '-') . " s/d " . ($program->end_date?->format('d M Y') ?? '-') . "</p>";
        $html .= "  </div>";

        // Section 1: Ringkasan Eksekutif
        $html .= "  <div class='mb-6'>";
        $html .= "    <h2 class='text-lg font-bold text-gray-800 border-l-4 border-[#4CAF50] pl-3 mb-3'>I. RINGKASAN EKSEKUTIF</h2>";
        $html .= "    <p class='text-sm leading-relaxed mb-2'>" . e($program->description) . "</p>";
        if ($charter) {
            $html .= "    <div class='grid grid-cols-2 gap-4 mt-3 bg-gray-50 p-4 rounded-lg text-sm'>";
            $html .= "      <div><strong>Visi:</strong> " . e($charter->vision) . "</div>";
            $html .= "      <div><strong>Misi:</strong> " . e($charter->mission) . "</div>";
            $html .= "    </div>";
        }
        $html .= "  </div>";

        // Section 2: Realisasi Program Kerja & Kegiatan
        $html .= "  <div class='mb-6'>";
        $html .= "    <h2 class='text-lg font-bold text-gray-800 border-l-4 border-[#4CAF50] pl-3 mb-3'>II. REALISASI KEGIATAN LAPANGAN</h2>";
        $html .= "    <table class='w-full text-left border-collapse text-sm'>";
        $html .= "      <thead>";
        $html .= "        <tr class='bg-gray-100 border-b'><th class='p-2'>Program Kerja</th><th class='p-2'>Kegiatan</th><th class='p-2'>Status</th><th class='p-2'>Anggaran (IDR)</th></tr>";
        $html .= "      </thead>";
        $html .= "      <tbody>";
        foreach ($workPlans as $wp) {
            $html .= "      <tr class='border-b font-medium bg-gray-50'><td class='p-2' colspan='2'>{$wp->name}</td><td class='p-2'>" . ucfirst($wp->status) . "</td><td class='p-2'>" . number_format($wp->budget, 0, ',', '.') . "</td></tr>";
            foreach ($wp->kegiatan as $keg) {
                $html .= "    <tr class='border-b'><td class='p-2'></td><td class='p-2'>- {$keg->name}</td><td class='p-2'>" . ucfirst($keg->status) . "</td><td class='p-2'>-</td></tr>";
            }
        }
        $html .= "      </tbody>";
        $html .= "    </table>";
        $html .= "  </div>";

        // Section 3: Laporan Keuangan
        $html .= "  <div class='mb-6'>";
        $html .= "    <h2 class='text-lg font-bold text-gray-800 border-l-4 border-[#4CAF50] pl-3 mb-3'>III. REALISASI KEUANGAN</h2>";
        $html .= "    <div class='grid grid-cols-3 gap-4 mb-4 text-center'>";
        $html .= "      <div class='bg-blue-50 p-3 rounded-lg'><div class='text-xs text-blue-700 font-semibold uppercase'>Total Anggaran</div><div class='text-lg font-bold text-blue-900 mt-1'>IDR " . number_format($totalBudget, 0, ',', '.') . "</div></div>";
        $html .= "      <div class='bg-red-50 p-3 rounded-lg'><div class='text-xs text-red-700 font-semibold uppercase'>Realisasi Pengeluaran</div><div class='text-lg font-bold text-red-900 mt-1'>IDR " . number_format($totalSpent, 0, ',', '.') . "</div></div>";
        $html .= "      <div class='bg-green-50 p-3 rounded-lg'><div class='text-xs text-green-700 font-semibold uppercase'>Sisa Anggaran</div><div class='text-lg font-bold text-green-900 mt-1'>IDR " . number_format($remainingBudget, 0, ',', '.') . "</div></div>";
        $html .= "    </div>";

        $html .= "    <table class='w-full text-left border-collapse text-xs'>";
        $html .= "      <thead>";
        $html .= "        <tr class='bg-gray-100 border-b'><th class='p-2'>Transaksi</th><th class='p-2'>Kategori</th><th class='p-2'>Tipe</th><th class='p-2'>Jumlah (IDR)</th><th class='p-2'>Status</th></tr>";
        $html .= "      </thead>";
        $html .= "      <tbody>";
        foreach ($transactions as $tx) {
            $statusColor = $tx->status === 'approved' ? 'text-green-600' : ($tx->status === 'rejected' ? 'text-red-600' : 'text-yellow-600');
            $html .= "      <tr class='border-b'>";
            $html .= "        <td class='p-2'>{$tx->title}</td>";
            $html .= "        <td class='p-2'>" . ucfirst($tx->category) . "</td>";
            $html .= "        <td class='p-2'>" . strtoupper($tx->type) . "</td>";
            $html .= "        <td class='p-2'>" . number_format($tx->amount, 0, ',', '.') . "</td>";
            $html .= "        <td class='p-2 font-bold {$statusColor}'>" . strtoupper($tx->status) . "</td>";
            $html .= "      </tr>";
        }
        if ($transactions->isEmpty()) {
            $html .= "      <tr><td colspan='5' class='p-4 text-center text-gray-400'>Belum ada riwayat transaksi keuangan.</td></tr>";
        }
        $html .= "      </tbody>";
        $html .= "    </table>";
        $html .= "  </div>";

        // Section 4: Dampak & Sustainability
        $html .= "  <div class='mb-6'>";
        $html .= "    <h2 class='text-lg font-bold text-gray-800 border-l-4 border-[#4CAF50] pl-3 mb-3'>IV. EVALUASI DAMPAK & TARGET SOSIAL</h2>";
        $html .= "    <div class='grid grid-cols-2 gap-4 bg-lime-50 p-4 rounded-lg text-sm text-lime-900'>";
        $carbon = $program->stats['carbon_offset'] ?? 0;
        $benCount = $program->stats['beneficiaries_count'] ?? 0;
        $html .= "      <div><strong>Estimasi Penurunan Emisi Carbon:</strong> {$carbon} Kg CO2e</div>";
        $html .= "      <div><strong>Jumlah Penerima Manfaat (Beneficiaries):</strong> {$benCount} Orang</div>";
        $html .= "    </div>";
        $html .= "  </div>";

        // Section 5: Signature block
        $html .= "  <div class='mt-12 pt-8 border-t flex justify-between items-center text-sm'>";
        $html .= "    <div class='text-center'>";
        $html .= "      <p class='text-gray-400'>Disusun Oleh,</p>";
        $html .= "      <p class='font-bold text-gray-700 mt-16'>Program Admin Coordinator</p>";
        $html .= "    </div>";
        $html .= "    <div class='text-center'>";
        $html .= "      <p class='text-gray-400'>Disetujui Oleh,</p>";
        $html .= "      <p class='font-bold text-gray-700 mt-16'>Super Admin (Head of CSR)</p>";
        $html .= "    </div>";
        $html .= "  </div>";

        $html .= "</div>";

        return $html;
    }

    /**
     * Generate HTML layout for a certificate print template.
     *
     * @param User $user
     * @param Program $program
     * @param string $certNumber
     * @param string $verifyCode
     * @return string
     */
    public function generateCertificateHtml(User $user, Program $program, string $certNumber, string $verifyCode): string
    {
        $html = "<div class='max-w-4xl mx-auto p-12 bg-white border-[10px] border-lime-100 rounded-2xl text-center font-sans shadow-md relative overflow-hidden'>";
        $html .= "  <div class='absolute -top-10 -right-10 w-40 h-40 bg-[#D9F99D] rounded-full opacity-35'></div>";
        $html .= "  <div class='absolute -bottom-10 -left-10 w-40 h-40 bg-[#4CAF50] rounded-full opacity-10'></div>";

        $html .= "  <p class='text-xs tracking-widest text-lime-800 font-bold uppercase mb-4'>YOTA PROJECT OPERATING SYSTEM</p>";
        $html .= "  <h1 class='text-4xl font-extrabold text-gray-900 tracking-wider font-serif mb-2'>CERTIFICATE OF APPRECIATION</h1>";
        $html .= "  <p class='text-sm text-gray-400 font-mono mb-8'>No: {$certNumber}</p>";
        
        $html .= "  <p class='text-lg text-gray-500 italic mb-2'>This certificate is proudly presented to</p>";
        $html .= "  <h2 class='text-3xl font-bold text-gray-800 border-b-2 border-lime-300 inline-block px-8 py-2 mb-6 font-serif'>{$user->name}</h2>";
        
        $html .= "  <p class='text-base text-gray-600 max-w-2xl mx-auto leading-relaxed mb-8'>";
        $html .= "    for their outstanding dedication and active contribution as a volunteer in the social action program: <br>";
        $html .= "    <strong class='text-gray-900 font-semibold'>\"{$program->name}\"</strong> <br>";
        $html .= "    helping in community empowerment and building sustainable environmental impacts.";
        $html .= "  </p>";

        $html .= "  <div class='flex justify-between items-end mt-12 border-t pt-6'>";
        $html .= "    <div class='text-left'>";
        $html .= "      <p class='text-xs text-gray-400'>Issued on</p>";
        $html .= "      <p class='text-sm font-semibold text-gray-700'>" . date('d F Y') . "</p>";
        $html .= "    </div>";
        
        // Verification QR-code representation
        $html .= "    <div class='flex flex-col items-center'>";
        $verifyUrl = route('certificates.verify', ['code' => $verifyCode]);
        $html .= "      <div class='p-2 bg-gray-50 border rounded'>";
        // Using a dynamic google chart QR API or simple visualization
        $html .= "        <img src='https://api.qrserver.com/v1/create-qr-code/?size=70x70&data=" . urlencode($verifyUrl) . "' alt='Verification QR' class='w-16 h-16'>";
        $html .= "      </div>";
        $html .= "      <span class='text-[10px] text-gray-400 mt-1 font-mono'>Verify: {$verifyCode}</span>";
        $html .= "    </div>";

        $html .= "    <div class='text-right'>";
        $html .= "      <p class='text-xs text-gray-400'>Authorized by</p>";
        $html .= "      <p class='text-sm font-bold text-gray-700'>YOTA Social Impact Lead</p>";
        $html .= "    </div>";
        $html .= "  </div>";

        $html .= "</div>";

        return $html;
    }
}
