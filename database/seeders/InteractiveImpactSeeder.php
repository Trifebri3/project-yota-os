<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\StoryMilestone;
use App\Models\ImpactChain;
use App\Models\KpiIndicator;
use App\Models\ProjectOutput;
use App\Models\BeforeAfterRecord;
use App\Models\Zone;
use App\Models\ProgramKerja;
use App\Models\Kegiatan;
use App\Models\ProjectCharter;
use App\Models\User;
use App\Models\FinanceTransaction;
use Carbon\Carbon;
use Illuminate\Support\Str;

class InteractiveImpactSeeder extends Seeder
{
    public function run(): void
    {
        $officer = User::where('role', 'project-officer')->first() ?? User::first();
        $supervisor = User::where('role', 'supervisor')->first() ?? User::first();

        // 1. UPDATE EXISTING PROGRAM: CAP PURBAYANI
        $cap = Program::first();
        if ($cap) {
            $cap->update([
                'name' => 'Collaborative Action Program Purbayani 2026 (CAP)',
                'description' => 'Intervensi berbasis komunitas yang menggabungkan aksi bersih kawasan pesisir, pengembangan fasilitas pengelolaan sampah, edukasi, penguatan kelompok masyarakat Bale Resik, serta dukungan digitalisasi dan promosi kawasan.',
                'cover_image' => '/images/projects/mangrove.jpg',
                'banner_program' => '/images/projects/mangrove.jpg',
                'kategori_program' => 'Pengelolaan Sampah & Komunitas',
                'status' => 'aktif',
                'start_date' => Carbon::create(2026, 7, 5),
                'end_date' => Carbon::create(2026, 8, 5), // 31 hari
                'mitra' => 'Bale Resik, Dinas Lingkungan Hidup (DLH), UMKM, Pelaku Wisata & Hotel/Vila',
                'sponsor' => 'Yayasan Yota & Kontribusi Swadaya Anggota',
                'organisasi_pelaksana' => 'Collaborative Action Program Taskforce, Yayasan Yota & Kelompok Bale Resik',
                'tujuan_program' => 'Penguatan kapasitas komunitas dan sistem lokal untuk mengelola sampah, meningkatkan partisipasi masyarakat, memperluas edukasi, dan mendukung visibilitas ekonomi lokal melalui sarana fisik serta digital.',
                'target_capaian' => '372 kg sampah pesisir dibawa ke TPS3R, 400 kg residu diproses tungku minim asap, 17 kg maggot, 12 anggota Bale Resik, 127 peserta edukasi, 34 KK permukiman, 83 UMKM & 37 titik GIS terdata.',
                'stats' => [
                    'volunteers_count' => 33, // 33 peserta Coastal Clean Up
                    'beneficiaries_count' => 127, // ~127 peserta edukasi & warga (+ 34 KK)
                    'carbon_offset' => 52.0, // estimasi reduksi emisi pengolahan sampah terhindari
                ],
            ]);

            // Update/Create Charter
            ProjectCharter::updateOrCreate(
                ['program_id' => $cap->id],
                [
                    'vision' => 'Terwujudnya sistem pengelolaan sampah mandiri dan ekonomi lokal yang tangguh di kawasan pesisir Purbayani melalui aksi kolaboratif, penguatan fasilitas fisik, dan integrasi ekosistem digital.',
                    'mission' => "1. Melaksanakan Coastal Clean Up dan pengangkutan 372 kg sampah pesisir ke TPS3R bersama 33 peserta aksi bersih.\n2. Membangun dan mengoperasikan sarana organik (1 bangunan, 3 komposter, 1 pencacah, 17 kg maggot) serta fasilitas residu tungku minim asap (~400 kg) bersama Bale Resik.\n3. Menguatkan 12 anggota kelompok Bale Resik, menjangkau 127 peserta edukasi/warga, serta memfasilitasi 34 KK di kawasan permukiman.\n4. Mendukung digitalisasi dan promosi kawasan melalui website purbayani-eco-tourism.siyota.org (83 UMKM, 37 titik GIS, 7 destinasi, 5 adopsi QRIS).\n5. Menyiapkan tata kelola, SOP keselamatan pengoperasian residu, rekonsiliasi data, dan peta jalan keberlanjutan 12 bulan bersama DLH.",
                    'scope' => 'Kawasan Pesisir, Sentra Pengolahan Bale Resik, Permukiman Warga (34 KK), dan Zona Wisata Desa Purbayani, Garut Selatan, Jawa Barat (5 Juli – 5 Agustus 2026).',
                    'budget' => 15607041.00,
                ]
            );

            // Populate Story Milestones for CAP (Metodologi 6 Tahap Siklus Kerja CAP)
            StoryMilestone::where('program_id', $cap->id)->delete();
            $capMilestones = [
                [
                    'day_number' => 1,
                    'phase' => 'Discovery',
                    'title' => 'Tahap 1: Discovery — Pemetaan Persoalan, Alur Sampah & Baseline',
                    'subtitle' => 'Memahami persoalan, aktor kunci, alur sampah pesisir, dan kondisi awal kawasan',
                    'story_narrative' => 'Tim CAP bersama tokoh masyarakat dan nelayan melakukan observasi lapangan, wawancara mendalam, serta pemetaan alur timbulan sampah pesisir dan permukiman (34 KK). Mengidentifikasi titik kritis penumpukan sampah muara serta menyusun baseline awal sebelum intervensi program.',
                    'activity_name' => 'Observasi Lapangan, Wawancara Pemangku Kepentingan & Baseline Mapping',
                    'personnel_involved' => 'Tim CAP, Yayasan Yota, Tokoh Nelayan & Warga Pesisir Purbayani',
                    'key_achievement' => 'Peta alur sampah pesisir, identifikasi pemangku kepentingan kunci, dan inventarisasi persoalan awal berhasil didokumentasikan.',
                    'date' => Carbon::create(2026, 7, 8),
                    'photos' => ['/images/projects/mangrove.jpg', '/images/projects/urban_farming.jpg'],
                    'order' => 1,
                ],
                [
                    'day_number' => 2,
                    'phase' => 'Prioritization',
                    'title' => 'Tahap 2: Prioritization — Musyawarah & Penentuan Intervensi Kunci',
                    'subtitle' => 'Memilih persoalan prioritas dan menyepakati intervensi fisik serta komunitas',
                    'story_narrative' => 'Musyawarah bersama warga dan calon pengurus komunitas menyepakati fokus aksi: kebersihan garis pantai, pengalihan sampah pesisir ke TPS3R, pembentukan kelompok Bale Resik, serta pengadaan sarana pengolahan sampah organik dan residu.',
                    'activity_name' => 'Musyawarah Warga & Penyusunan Matriks Prioritas Intervensi',
                    'personnel_involved' => 'Tokoh Nelayan, Calon Pengurus Bale Resik, Perangkat Desa & Tim CAP',
                    'key_achievement' => 'Matriks prioritas disepakati warga: aksi bersih pantai, aktivasi Bale Resik, fasilitas organik, dan pengolahan residu minim asap.',
                    'date' => Carbon::create(2026, 7, 13),
                    'photos' => ['/images/projects/urban_farming.jpg'],
                    'order' => 2,
                ],
                [
                    'day_number' => 3,
                    'phase' => 'Design',
                    'title' => 'Tahap 3: Design — Perancangan Fasilitas, Alur Kerja & RAB Definitif',
                    'subtitle' => 'Merancang kegiatan, fasilitas, pembagian peran kelompok, dan alokasi anggaran Rp15,6M',
                    'story_narrative' => 'Menyusun rancangan teknis 1 unit bangunan pengelolaan sampah organik, 3 unit komposter komunal, 1 unit mesin pencacah organik, dan 1 fasilitas tungku residu minim asap. Merumuskan alokasi 5 set tempat sampah terpilah untuk zona wisata serta RAB definitif sebesar Rp15.607.041.',
                    'activity_name' => 'Penyusunan Rencana Kerja, Desain Teknis Sarana & Finalisasi RAB',
                    'personnel_involved' => 'Tim Teknis CAP, Koordinator Bale Resik, Fasilitator Yota & DLH',
                    'key_achievement' => 'RAB Rp15.607.041 dan desain fasilitas pengolahan fisik disetujui, pembagian peran kelompok Bale Resik dimatangkan.',
                    'date' => Carbon::create(2026, 7, 17),
                    'photos' => ['/images/projects/mangrove.jpg'],
                    'order' => 3,
                ],
                [
                    'day_number' => 4,
                    'phase' => 'Small-Scale Test',
                    'title' => 'Tahap 4: Small-Scale Test — Uji Operasi Pengolahan Organik, Maggot & Residu',
                    'subtitle' => 'Menguji kegiatan aksi bersih, komposter, biokonversi maggot, dan tungku residu',
                    'story_narrative' => 'Pelaksanaan Coastal Clean Up melibatkan 33 peserta mengumpulkan 372 kg sampah pesisir untuk dibawa ke TPS3R. Di sentra Bale Resik, uji coba dekomposisi komposter dan biokonversi larva maggot BSF berhasil memanen 17 kg maggot, serta uji bakar awal residu dicatat di logbook timbang.',
                    'activity_name' => 'Coastal Clean Up Lapangan, Uji Coba Pengomposan, Budidaya Maggot & Tungku Residu',
                    'personnel_involved' => '33 Peserta Coastal Clean Up, 12 Anggota Bale Resik, Tim Monitoring Lapangan',
                    'key_achievement' => '372 kg sampah pesisir terangkut ke TPS3R, 17 kg maggot dipanen, dan uji awal tungku residu tuntas dicatat.',
                    'date' => Carbon::create(2026, 7, 24),
                    'photos' => ['/images/projects/urban_farming.jpg', '/images/projects/mangrove.jpg'],
                    'order' => 4,
                ],
                [
                    'day_number' => 5,
                    'phase' => 'Community Validation',
                    'title' => 'Tahap 5: Community Validation — Edukasi 127 Peserta & Digitalisasi 83 UMKM',
                    'subtitle' => 'Sosialisasi pemilahan sampah, edukasi sekolah, dan aktivasi portal purbayani-eco-tourism.siyota.org',
                    'story_narrative' => 'Menyelenggarakan rangkaian edukasi lingkungan menjangkau ~127 peserta didik dan masyarakat, pemasangan 5 set tempat sampah terpilah di zona wisata, pendampingan pemilahan di 34 KK permukiman, serta peluncuran website purbayani-eco-tourism.siyota.org (83 UMKM, 37 titik GIS, 7 destinasi, 5 adopsi QRIS).',
                    'activity_name' => 'Sesi Edukasi Pemilahan Sampah, Verifikasi UMKM Pesisir & Peluncuran Web GIS',
                    'personnel_involved' => '127 Peserta Edukasi, 83 Pelaku UMKM Terdata, Relawan & Divisi Digital Yota',
                    'key_achievement' => '127 peserta teredukasi, portal ekowisata aktif dengan rata-rata ~300 kunjungan/bulan, dan 5 UMKM mengadopsi QRIS.',
                    'date' => Carbon::create(2026, 7, 30),
                    'photos' => ['/images/projects/urban_farming.jpg'],
                    'order' => 5,
                ],
                [
                    'day_number' => 6,
                    'phase' => 'Institutionalization',
                    'title' => 'Tahap 6: Institutionalization — Tata Kelola, SOP Fasilitas & Roadmap Keberlanjutan',
                    'subtitle' => 'Penetapan struktur 12 anggota Bale Resik, SOP keselamatan APD, dan Register Bukti E-01 s/d E-15',
                    'story_narrative' => 'Konsolidasi operasional kelompok Bale Resik (12 anggota), penyusunan SOP pengumpulan, pemilahan, keselamatan tungku residu minim asap, serta perumusan Register Bukti E-01 s/d E-15 dan Laporan Program Versi 1.0. Menetapkan roadmap keberlanjutan 12 bulan dan penjajakan kemitraan DLH & B2B usaha lokal.',
                    'activity_name' => 'Pengesahan Struktur Kelompok, SOP Operasional Keselamatan & Pleno Rekonsiliasi Dokumen',
                    'personnel_involved' => '12 Anggota Bale Resik, Pengurus Desa Purbayani, Tim Auditor Yota & Pendamping DLH',
                    'key_achievement' => 'Dokumen Laporan Program & Tinjauan Bukti Versi 1.0 (19 Okt 2026) rampung bersama register bukti dan SOP fasilitas.',
                    'date' => Carbon::create(2026, 8, 5),
                    'photos' => ['/images/projects/mangrove.jpg'],
                    'order' => 6,
                ],
            ];
            foreach ($capMilestones as $m) {
                $cap->storyMilestones()->create($m);
            }

            // Populate Impact Chain for CAP (Kerangka Perubahan 5 Tingkat Hasil)
            ImpactChain::where('program_id', $cap->id)->delete();
            $capImpactChains = [
                [
                    'stage' => 'input',
                    'stage_order' => 1,
                    'title' => 'Input Sumber Daya, Tenaga & Kanal Digital',
                    'description' => 'Total anggaran program dilaporkan Rp 15.607.041 (living kit Rp 8.5M, swadaya Rp 856rb, selisih Rp 6.25M), 33 personel aksi bersih, 12 anggota Bale Resik, 5 set tempat sampah terpilah, dan portal purbayani-eco-tourism.siyota.org.',
                    'metric_value' => 'Rp 15.607.041 & 33 Orang',
                    'metric_unit' => 'Alokasi Penuh',
                    'target_value' => '100% Siap',
                    'evidence_note' => 'E-01, E-03, E-11 (RAB program, bukti pencatatan kas, daftar hadir aksi, dan inventaris awal).',
                    'verification_status' => 'verified',
                ],
                [
                    'stage' => 'activity',
                    'stage_order' => 2,
                    'title' => 'Pelaksanaan Aksi Lapangan Siklus 6 Tahap',
                    'description' => 'Aksi Coastal Clean Up pesisir, edukasi bagi 127 peserta didik/warga, pembangunan/pengadaan fasilitas pengolahan sampah organik dan residu, pemetaan 83 UMKM & 37 titik GIS kawasan ekowisata.',
                    'metric_value' => '31 Hari Pelaksanaan',
                    'metric_unit' => '6 Tahap Siklus',
                    'target_value' => '31 Hari Penuh',
                    'evidence_note' => 'E-01, E-05, E-13 (Logbook harian 5 Juli – 5 Agustus 2026, daftar hadir edukasi, dan listing data web).',
                    'verification_status' => 'verified',
                ],
                [
                    'stage' => 'output',
                    'stage_order' => 3,
                    'title' => 'Keluaran Fisik, Fasilitas & Penguatan Komunitas',
                    'description' => '372 kg sampah pesisir dibawa ke TPS3R, sekitar 400 kg residu diproses tungku minim asap, 17 kg maggot, 1 bangunan organik, 3 komposter, 1 pencacah, 5 set tempat sampah, 12 anggota Bale Resik, dan 5 UMKM QRIS.',
                    'metric_value' => '12 Item Keluaran',
                    'metric_unit' => 'Output Dilaporkan',
                    'target_value' => '12 Output Selesai',
                    'evidence_note' => 'E-02, E-04, E-07, E-08, E-09, E-10 (Berita acara serah terima fasilitas, inventaris aset bertanggal, dan log timbang).',
                    'verification_status' => 'verified',
                ],
                [
                    'stage' => 'outcome',
                    'stage_order' => 4,
                    'title' => 'Outcome Awal Penggunaan Sarana & Kanal Digital',
                    'description' => 'Penggunaan fasilitas organik dan tungku residu secara rutin oleh Bale Resik, pemilahan mulai diterapkan di 34 KK permukiman, dan rata-rata ~300 kunjungan web/bulan selama 2 bulan terakhir.',
                    'metric_value' => '300 Web Visits & 34 KK',
                    'metric_unit' => 'Adopsi Komunitas',
                    'target_value' => 'Rutin Berjalan',
                    'evidence_note' => 'E-06, E-12, E-14 (Ekspor analytics web, survei keterlibatan warga, dan konfirmasi merchant QRIS).',
                    'verification_status' => 'verified',
                ],
                [
                    'stage' => 'impact',
                    'stage_order' => 5,
                    'title' => 'Dampak Jangka Panjang & Ketahanan Lingkungan',
                    'description' => 'Terbentuknya sistem pengelolaan sampah berbasis komunitas dan ekonomi lokal yang mandiri, penurunan akumulasi sampah pesisir, serta penjajakan kemitraan layanan sampah B2B hotel, vila dan pelaku pariwisata.',
                    'metric_value' => 'Kawasan Purbayani Mandiri',
                    'metric_unit' => 'Arah Berkelanjutan',
                    'target_value' => 'Sustainabilitas',
                    'evidence_note' => 'E-15 (Peta jalan keberlanjutan 6-12 bulan, koordinasi DLH, inisiasi Perdes & kesepakatan B2B).',
                    'verification_status' => 'verified',
                ],
            ];
            foreach ($capImpactChains as $ic) {
                $cap->impactChains()->create($ic);
            }

            // Populate KPI Indicators for CAP (Rekapitulasi Output & Kerangka Pemantauan)
            KpiIndicator::where('program_id', $cap->id)->delete();
            $capKpis = [
                [
                    'code' => 'KPI-WST-01',
                    'name' => 'Sampah Pesisir Terkumpul & Dikirim ke TPS3R',
                    'category' => 'Pengelolaan Sampah Pesisir',
                    'definition' => 'Volume sampah kawasan pesisir yang dikumpulkan melalui Coastal Clean Up dan dipindahkan ke TPS3R (dilaporkan).',
                    'baseline' => 0,
                    'target' => 400,
                    'actual' => 372,
                    'unit' => 'Kg',
                    'measurement_method' => 'Timbangan digital dan serah terima TPS3R (dilaporkan; bukti timbang perlu dilampirkan).',
                    'verified_by' => 'Pengelola TPS3R & Tim Kebersihan Pesisir',
                    'status' => 'achieved',
                    'evidence_doc' => 'E-02: Tiket timbang / log timbang dan bukti serah terima TPS3R',
                    'trend_data' => ['M-1' => 85, 'M-2' => 190, 'M-3' => 280, 'M-4' => 372],
                ],
                [
                    'code' => 'KPI-WST-02',
                    'name' => 'Sampah Residu Diproses Tungku Minim Asap',
                    'category' => 'Pengolahan Residu',
                    'definition' => 'Estimasi volume sampah residu yang diproses melalui fasilitas tungku minim asap dengan kontrol operasional (dilaporkan).',
                    'baseline' => 0,
                    'target' => 400,
                    'actual' => 400,
                    'unit' => 'Kg',
                    'measurement_method' => 'Log per batch, jenis sampah, timbangan, periode, dan prosedur keselamatan.',
                    'verified_by' => 'Operator Tungku Residu Bale Resik',
                    'status' => 'achieved',
                    'evidence_doc' => 'E-09: Log batch timbang, jenis sampah, periode, dan checklist keselamatan',
                    'trend_data' => ['M-1' => 60, 'M-2' => 160, 'M-3' => 290, 'M-4' => 400],
                ],
                [
                    'code' => 'KPI-ORG-03',
                    'name' => 'Produksi Biokonversi Maggot Organik',
                    'category' => 'Pengolahan Organik',
                    'definition' => 'Hasil panen larva maggot Black Soldier Fly (BSF) dari biokonversi sisa sampah organik pasar dan makanan.',
                    'baseline' => 0,
                    'target' => 20,
                    'actual' => 17,
                    'unit' => 'Kg',
                    'measurement_method' => 'Catatan produksi panen bertanggal dan penimbangan neto panen larva maggot.',
                    'verified_by' => 'Divisi Pengolahan Organik Bale Resik',
                    'status' => 'achieved',
                    'evidence_doc' => 'E-08: Catatan produksi panen, tanggal panen, dan log pakan organik harian',
                    'trend_data' => ['M-1' => 2, 'M-2' => 6, 'M-3' => 12, 'M-4' => 17],
                ],
                [
                    'code' => 'KPI-VOL-04',
                    'name' => 'Peserta Aksi Bersih Pesisir (Coastal Clean Up)',
                    'category' => 'Partisipasi Warga',
                    'definition' => 'Jumlah sukarelawan dan warga lokal yang berpartisipasi aktif dalam aksi kebersihan sempadan pantai.',
                    'baseline' => 0,
                    'target' => 35,
                    'actual' => 33,
                    'unit' => 'Orang',
                    'measurement_method' => 'Daftar hadir kegiatan Coastal Clean Up bertanggal dan dokumentasi visual.',
                    'verified_by' => 'Koordinator Lapangan CAP',
                    'status' => 'achieved',
                    'evidence_doc' => 'E-03: Daftar hadir 33 orang, dokumentasi foto bertanggal, dan catatan lokasi',
                    'trend_data' => ['Aksi 1' => 18, 'Aksi 2' => 25, 'Aksi 3' => 30, 'Aksi 4' => 33],
                ],
                [
                    'code' => 'KPI-EDU-05',
                    'name' => 'Jangkauan Edukasi & Pelatihan Masyarakat',
                    'category' => 'Edukasi & Sosialisasi',
                    'definition' => 'Peserta didik dan anggota masyarakat yang mengikuti sesi edukasi pemilahan sampah dan kelestarian lingkungan.',
                    'baseline' => 0,
                    'target' => 130,
                    'actual' => 127,
                    'unit' => 'Peserta',
                    'measurement_method' => 'Rekapitulasi lembar absensi peserta per sesi dan materi edukasi terdistribusi.',
                    'verified_by' => 'Tim Fasilitator Edukasi Lingkungan',
                    'status' => 'achieved',
                    'evidence_doc' => 'E-05: Daftar hadir per sesi, materi pelatihan, dan rekap peserta unik',
                    'trend_data' => ['Sesi 1' => 35, 'Sesi 2' => 70, 'Sesi 3' => 105, 'Sesi 4' => 127],
                ],
                [
                    'code' => 'KPI-GRP-06',
                    'name' => 'Kesiapan & Keaktifan Kelompok Komunitas Bale Resik',
                    'category' => 'Kelembagaan Komunitas',
                    'definition' => 'Anggota kelompok masyarakat lokal yang terorganisasi mengelola operasional fasilitas organik dan residu.',
                    'baseline' => 0,
                    'target' => 12,
                    'actual' => 12,
                    'unit' => 'Anggota',
                    'measurement_method' => 'Daftar nama anggota terverifikasi, struktur pengurus, dan jadwal giliran kerja.',
                    'verified_by' => 'Pemerintah Desa Purbayani',
                    'status' => 'achieved',
                    'evidence_doc' => 'E-04: Daftar anggota 12 orang, struktur pengurus, jadwal kerja, dan draft SK',
                    'trend_data' => ['M-1' => 6, 'M-2' => 10, 'M-3' => 12, 'M-4' => 12],
                ],
                [
                    'code' => 'KPI-DIG-07',
                    'name' => 'Digitalisasi Kawasan, Pemetaan GIS & UMKM',
                    'category' => 'Ekonomi & Digital',
                    'definition' => 'Pelaku UMKM terdata, titik koordinat GIS, destinasi wisata, serta adopsi pembayaran non-tunai QRIS.',
                    'baseline' => 0,
                    'target' => 80,
                    'actual' => 83,
                    'unit' => 'UMKM',
                    'measurement_method' => 'Listing database portal web purbayani-eco-tourism.siyota.org dan koordinat GIS.',
                    'verified_by' => 'Tim Digitalisasi Kawasan Yota OS',
                    'status' => 'achieved',
                    'evidence_doc' => 'E-12, E-13, E-14: Database 83 UMKM, 37 titik GIS, 7 destinasi, 5 merchant QRIS',
                    'trend_data' => ['M-1' => 20, 'M-2' => 45, 'M-3' => 70, 'M-4' => 83],
                ],
                [
                    'code' => 'KPI-FIN-08',
                    'name' => 'Realisasi Anggaran Program & Swadaya',
                    'category' => 'Akuntabilitas Keuangan',
                    'definition' => 'Akuntabilitas realisasi anggaran program mencakup biaya hidup kelompok, swadaya anggota, dan biaya aset.',
                    'baseline' => 0,
                    'target' => 15607041,
                    'actual' => 15607041,
                    'unit' => 'Rupiah',
                    'measurement_method' => 'Rekonsiliasi buku kas masuk-keluar, RAB, kuitansi, dan bukti transaksi.',
                    'verified_by' => 'Divisi Keuangan Program Yota',
                    'status' => 'achieved',
                    'evidence_doc' => 'E-11: Buku kas, RAB final, nota pembelian aset, biaya hidup Rp8.5M & swadaya Rp856rb',
                    'trend_data' => ['M-1' => 4500000, 'M-2' => 9200000, 'M-3' => 13100000, 'M-4' => 15607041],
                ],
            ];
            foreach ($capKpis as $kpi) {
                $cap->kpiIndicators()->create($kpi);
            }

            // Populate Project Outputs for CAP (Section 5: Rekapitulasi Output yang Dilaporkan)
            ProjectOutput::where('program_id', $cap->id)->delete();
            $capOutputs = [
                [
                    'category' => 'prototype',
                    'title' => 'Bangunan Pengelolaan Sampah Organik Bale Resik (1 Unit)',
                    'description' => 'Satu unit bangunan fisik beratap untuk sentra pemilahan dan pengolahan sampah organik warga, fermentasi pupuk kompos, serta budidaya maggot BSF.',
                    'thumbnail' => '/images/projects/mangrove.jpg',
                    'release_date' => Carbon::create(2026, 7, 28),
                    'authors_or_team' => 'Tim CAP & Kelompok Bale Resik',
                    'evidence_url' => '#evidence-e07',
                    'ip_number' => 'ASET-FISIK-ORG-01',
                    'tags' => ['Bangunan Organik', 'Bale Resik', 'Sentra Daur Ulang'],
                ],
                [
                    'category' => 'hardware',
                    'title' => 'Unit Komposter Komunal (3 Unit)',
                    'description' => 'Tiga unit drum komposter higienis untuk mendekomposisi sisa sampah makanan dan limbah rumah tangga menjadi pupuk kompos padat.',
                    'thumbnail' => '/images/projects/urban_farming.jpg',
                    'release_date' => Carbon::create(2026, 7, 22),
                    'authors_or_team' => 'Divisi Pengolahan Organik Bale Resik',
                    'evidence_url' => '#evidence-e07',
                    'ip_number' => 'ASET-KMP-01-03',
                    'tags' => ['Komposter', 'Pupuk Kompos', 'Sampah Organik'],
                ],
                [
                    'category' => 'hardware',
                    'title' => 'Mesin Pencacah Sampah Organik (1 Unit)',
                    'description' => 'Satu unit mesin pencacah bertenaga motor untuk mereduksi volume dan memperkecil ukuran limbah organik sebelum diproses di komposter atau media maggot.',
                    'thumbnail' => '/images/projects/mangrove.jpg',
                    'release_date' => Carbon::create(2026, 7, 25),
                    'authors_or_team' => 'Divisi Peralatan Teknis CAP',
                    'evidence_url' => '#evidence-e07',
                    'ip_number' => 'ASET-MCN-PCC-01',
                    'tags' => ['Mesin Pencacah', 'Teknologi Tepat Guna', 'Efisiensi Olah'],
                ],
                [
                    'category' => 'hardware',
                    'title' => 'Fasilitas Residu: Tungku Minim Asap (1 Unit)',
                    'description' => 'Satu unit fasilitas pemrosesan residu berupa tungku pembakaran minim asap dengan kapasitas olah sekitar 400 kg residu, dilengkapi APD dan SOP keselamatan kerja.',
                    'thumbnail' => '/images/projects/urban_farming.jpg',
                    'release_date' => Carbon::create(2026, 7, 26),
                    'authors_or_team' => 'Kelompok Bale Resik & Pendamping Teknis',
                    'evidence_url' => '#evidence-e09',
                    'ip_number' => 'ASET-TNK-RSD-01',
                    'tags' => ['Tungku Minim Asap', 'Sampah Residu', 'SOP Keselamatan'],
                ],
                [
                    'category' => 'hardware',
                    'title' => 'Set Tempat Sampah Terpilah di Zona Wisata (5 Set)',
                    'description' => 'Lima set tempat sampah terpilah (organik, anorganik, residu) yang ditempatkan pada titik strategis zona wisata pantai untuk mengedukasi wisatawan dan mencegah sampah tercecer.',
                    'thumbnail' => '/images/projects/mangrove.jpg',
                    'release_date' => Carbon::create(2026, 7, 20),
                    'authors_or_team' => 'Tim Pengelola Wisata & Bale Resik',
                    'evidence_url' => '#evidence-e07',
                    'ip_number' => 'ASET-TS-PILAH-05',
                    'tags' => ['Tempat Sampah Terpilah', 'Zona Wisata', 'Edukasi Publik'],
                ],
                [
                    'category' => 'digital',
                    'title' => 'Portal Ekowisata Kawasan (purbayani-eco-tourism.siyota.org)',
                    'description' => 'Website informasi kawasan yang memuat pemetaan 83 UMKM lokal, 37 titik koordinat GIS, 7 destinasi wisata, fasilitasi 5 merchant QRIS, dengan rata-rata ~300 kunjungan/bulan.',
                    'thumbnail' => '/images/logo.webp',
                    'release_date' => Carbon::create(2026, 8, 1),
                    'authors_or_team' => 'Tim Digitalisasi & Informasi Kawasan Yota OS',
                    'evidence_url' => 'https://purbayani-eco-tourism.siyota.org/',
                    'ip_number' => 'https://purbayani-eco-tourism.siyota.org/',
                    'tags' => ['Web GIS', 'Digitalisasi UMKM', 'Ekowisata', 'QRIS'],
                ],
                [
                    'category' => 'publication',
                    'title' => 'Dokumen Kerja: Laporan Program & Tinjauan Bukti (Versi 1.0)',
                    'description' => 'Laporan komprehensif CAP 2026 memuat rekonsiliasi data sampah 372kg/400kg, paket audit keuangan Rp15.6M, register bukti E-01 s/d E-15, dan roadmap keberlanjutan 12 bulan.',
                    'thumbnail' => '/images/logo.webp',
                    'release_date' => Carbon::create(2026, 10, 19),
                    'authors_or_team' => 'Collaborative Action Taskforce & Bale Resik',
                    'evidence_url' => '#laporan-lengkap',
                    'ip_number' => 'DOC-CAP-2026-VER1.0',
                    'tags' => ['Laporan Program', 'Register Bukti', 'Governance', 'SOP'],
                ],
            ];
            foreach ($capOutputs as $po) {
                $cap->projectOutputs()->create($po);
            }

            // Populate Before & After for CAP
            BeforeAfterRecord::where('program_id', $cap->id)->delete();
            $cap->beforeAfterRecords()->create([
                'title' => 'Aksi Bersih Pesisir & Pengangkutan ke TPS3R',
                'location' => 'Kawasan Garis Pantai Purbayani (33 Peserta Coastal Clean Up)',
                'before_image' => '/images/projects/urban_farming.jpg',
                'after_image' => '/images/projects/mangrove.jpg',
                'before_date' => Carbon::create(2026, 7, 5),
                'after_date' => Carbon::create(2026, 8, 5),
                'before_description' => 'Kondisi sempadan pantai dipenuhi timbunan sampah plastik muara dan sisa material laut tanpa ada sistem pengangkutan rutin ke TPS3R.',
                'after_description' => '372 kg sampah pesisir berhasil dikumpulkan melalui aksi Coastal Clean Up 33 orang dan langsung dipindahkan secara tertib ke TPS3R.',
                'metric_impact' => '372 kg Sampah Pesisir Terangkut ke TPS3R (33 Peserta)',
            ]);

            $cap->beforeAfterRecords()->create([
                'title' => 'Sentra Pengolahan Sampah Organik Bale Resik',
                'location' => 'Fasilitas Pengolahan Organik Bale Resik Desa Purbayani',
                'before_image' => '/images/projects/urban_farming.jpg',
                'after_image' => '/images/projects/mangrove.jpg',
                'before_date' => Carbon::create(2026, 7, 5),
                'after_date' => Carbon::create(2026, 8, 5),
                'before_description' => 'Limbah sisa makanan rumah tangga dan pasar membusuk di sudut jalan tanpa proses pengomposan maupun pemanfaatan nilai ekonomi.',
                'after_description' => '1 unit bangunan organik beroperasi dengan 3 unit komposter komunal, 1 mesin pencacah, dan berhasil memanen 17 kg maggot BSF bernilai pakan ternak.',
                'metric_impact' => '1 Bangunan Organik, 3 Komposter, 1 Pencacah & 17 kg Maggot',
            ]);

            $cap->beforeAfterRecords()->create([
                'title' => 'Fasilitas Pemrosesan Residu: Tungku Minim Asap',
                'location' => 'Zona Pengolahan Residu Komunitas Bale Resik',
                'before_image' => '/images/projects/urban_farming.jpg',
                'after_image' => '/images/projects/mangrove.jpg',
                'before_date' => Carbon::create(2026, 7, 5),
                'after_date' => Carbon::create(2026, 8, 5),
                'before_description' => 'Sampah residu anorganik yang tidak dapat didaur ulang kerap dibakar di ruang terbuka menghasilkan asap tebal pekat yang membahayakan warga.',
                'after_description' => 'Sekitar 400 kg sampah residu diproses melalui fasilitas tungku minim asap tertutup dengan SOP pengoperasian dan penggunaan APD keselamatan.',
                'metric_impact' => 'Sekitar 400 kg Residu Diproses Tungku Minim Asap Ber-SOP',
            ]);

            // Reconciled Finance Transactions for CAP (Section 9)
            if ($cap->financeTransactions()->where('title', 'Alokasi Biaya Hidup & Living Kit Kelompok')->count() === 0) {
                $cap->financeTransactions()->create([
                    'title' => 'Alokasi Biaya Hidup & Living Kit Kelompok',
                    'description' => 'Alokasi living allowance dan perlengkapan kit operasional tim kelompok (disebutkan dalam dokumen CAP 2026).',
                    'amount' => 8500000.00,
                    'type' => 'expense',
                    'category' => 'living_kit',
                    'status' => 'approved',
                    'created_by' => $officer->id,
                    'approved_by' => $supervisor->id,
                ]);
            }
            if ($cap->financeTransactions()->where('title', 'Kontribusi Tambahan Swadaya Anggota')->count() === 0) {
                $cap->financeTransactions()->create([
                    'title' => 'Kontribusi Tambahan Swadaya Anggota',
                    'description' => 'Kontribusi dana swadaya tambahan dari anggota kelompok masyarakat (disebutkan dalam dokumen CAP 2026).',
                    'amount' => 856000.00,
                    'type' => 'income',
                    'category' => 'contribution',
                    'status' => 'approved',
                    'created_by' => $officer->id,
                    'approved_by' => $supervisor->id,
                ]);
            }
        }

        // 2. CREATE PROGRAM 2: MUARA GEMBONG BLUE CARBON
        $prog2 = Program::firstOrCreate(
            ['name' => 'Muara Gembong Blue Carbon Sanctuary & Mangrove Nursery'],
            [
                'description' => 'Inisiatif konservasi ekosistem pesisir utara melalui pembibitan 15.000 bibit mangrove avicennia, sabuk hijau pencegah abrasi pantura, serta pemantauan cadangan karbon biru.',
                'cover_image' => '/images/projects/mangrove.jpg',
                'banner_program' => '/images/projects/mangrove.jpg',
                'kategori_program' => 'Konservasi & Karbon Biru',
                'status' => 'aktif',
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->addMonths(4),
                'project_officer_id' => $officer->id,
                'supervisor_id' => $supervisor->id,
                'mitra' => 'Kelompok Tani Hutan Muara Gembong & BPDASHL',
                'sponsor' => 'PLN Nusantara Power CSR',
                'organisasi_pelaksana' => 'Yayasan Bahari Lestari & Yota',
                'tujuan_program' => 'Menahan laju abrasi laut Jawa, memulihkan tambak terlantar menjadi hutan mangrove, dan mengukur potensi kredit karbon biru.',
                'target_capaian' => '15.000 bibit mangrove disemai, 420 kg CO2e reduksi emisi, 2.100 penerima manfaat.',
                'stats' => [
                    'volunteers_count' => 28,
                    'beneficiaries_count' => 2100,
                    'carbon_offset' => 420.0,
                ],
            ]
        );

        ProjectCharter::updateOrCreate(
            ['program_id' => $prog2->id],
            [
                'vision' => 'Mengembalikan benteng alami pesisir Muara Gembong dari ancaman tenggelamnya daratan melalui restorasi karbon biru.',
                'mission' => "1. Membangun nursery pembibitan mangrove berkapasitas 20.000 bibit.\n2. Menanam sabuk pelindung sepanjang 5 kilometer garis pantai.\n3. Melibatkan pemuda pesisir dalam ekowisata dan pemantauan satwa burung migran.",
                'scope' => 'Desa Pantai Bahagia, Muara Gembong, Kabupaten Bekasi, Jawa Barat.',
            ]
        );

        // Add 1 Zone for Prog 2
        Zone::firstOrCreate(
            ['program_id' => $prog2->id, 'name' => 'Zona Konservasi Pantai Bahagia'],
            [
                'description' => 'Area sabuk hijau dan persemaian bibit mangrove Muara Gembong.',
                'type' => 'activity',
                'status_zona' => 'aktif',
                'geojson' => [
                    [-5.960000, 107.010000],
                    [-5.960000, 107.050000],
                    [-5.990000, 107.050000],
                    [-5.990000, 107.010000]
                ]
            ]
        );

        // Add Milestone for Prog 2
        if ($prog2->storyMilestones()->count() === 0) {
            $prog2->storyMilestones()->create([
                'day_number' => 1,
                'phase' => 'Discover',
                'title' => 'Pemetaan Kawasan Abrasi Kritis Muara Gembong',
                'subtitle' => 'Drone mapping sepanjang 5 km garis pantai tambak terlantar',
                'story_narrative' => 'Melakukan pemetaan fotogrametri udara untuk menentukan batas sabuk hijau penahan ombak serta titik rumah warga yang terancam rob tinggi.',
                'activity_name' => 'Aerial Drone Survey & Koordinasi Kelompok Tani',
                'personnel_involved' => '28 Relawan & 12 Anggota Kelompok Tani Bahari',
                'key_achievement' => 'Peta kontur abrasi skala 1:2.000 berhasil diterbitkan.',
                'date' => Carbon::now()->subDays(20),
                'photos' => ['/images/projects/mangrove.jpg'],
                'order' => 1,
            ]);
            $prog2->storyMilestones()->create([
                'day_number' => 2,
                'phase' => 'Build',
                'title' => 'Pembangunan Rumah Bibit & Persemaian 15.000 Polybag',
                'subtitle' => 'Pemilahan propagul buah bakau unggulan',
                'story_narrative' => 'Pembangunan bedeng semai nursery bambu berukuran 20x15 meter untuk menampung bibit bakau hingga siap tanam.',
                'activity_name' => 'Penyemaian Propagul Mangrove',
                'personnel_involved' => 'Relawan Lapangan & Ibu-ibu Pesisir',
                'key_achievement' => '15.000 bibit tertata rapi dalam naungan jaring paranet.',
                'date' => Carbon::now()->subDays(10),
                'photos' => ['/images/projects/mangrove.jpg'],
                'order' => 2,
            ]);
        }

        // Add KPI for Prog 2
        if ($prog2->kpiIndicators()->count() === 0) {
            $prog2->kpiIndicators()->create([
                'code' => 'KPI-MGB-01',
                'name' => 'Kapasitas Nursery Mangrove Mandiri',
                'category' => 'Konservasi',
                'definition' => 'Jumlah bibit bakau yang berhasil disemai dan dirawat di rumah semai.',
                'baseline' => 0,
                'target' => 15000,
                'actual' => 15000,
                'unit' => 'Bibit',
                'verified_by' => 'BPDASHL Citarum-Ciliwung',
                'status' => 'achieved',
            ]);
            $prog2->kpiIndicators()->create([
                'code' => 'KPI-MGB-02',
                'name' => 'Estimasi Serapan Karbon Biru Terverifikasi',
                'category' => 'Karbon Biru',
                'definition' => 'Kalkulasi potensi simpanan karbon sedimen dan biomassa tegakan mangrove.',
                'baseline' => 0,
                'target' => 500,
                'actual' => 420,
                'unit' => 'Kg CO2e',
                'verified_by' => 'Yota Carbon Verification Engine',
                'status' => 'in_progress',
                'carbon_breakdown' => [
                    'emissions_produced' => 210.0,
                    'emissions_avoided' => 420.0,
                    'carbon_sequestered' => 890.0,
                ]
            ]);
        }

        // Add Output for Prog 2
        if ($prog2->projectOutputs()->count() === 0) {
            $prog2->projectOutputs()->create([
                'category' => 'prototype',
                'title' => 'Breakwater Bambu Geotekstil Sabuk Pantai',
                'description' => 'Struktur pemecah ombak ramah lingkungan dari cerucuk bambu ganda berlapis geotekstil penahan lumpur endapan sedimen.',
                'thumbnail' => '/images/projects/mangrove.jpg',
                'release_date' => Carbon::now()->subDays(8),
                'authors_or_team' => 'Insinyur Sipil Relawan Yota & Nelayan Bahari',
                'ip_number' => 'DES-TEK-2026-44',
                'tags' => ['Pemecah Ombak', 'Bambu', 'Sedimentasi'],
            ]);
        }

        // 3. CREATE PROGRAM 3: CISARUA ZERO WASTE & URBAN FARMING
        $prog3 = Program::firstOrCreate(
            ['name' => 'Cisarua Zero-Waste Eco-Hub & Urban Agriculture'],
            [
                'description' => 'Pengembangan bank sampah organik terpadu, instalasi biodigester komunal limbah sayur, dan kebun pangan hidroponik di kawasan pegunungan Cisarua Lembang.',
                'cover_image' => '/images/projects/urban_farming.jpg',
                'banner_program' => '/images/projects/urban_farming.jpg',
                'kategori_program' => 'Ketahanan Pangan & Zero Waste',
                'status' => 'aktif',
                'start_date' => Carbon::now()->subMonths(1),
                'end_date' => Carbon::now()->addMonths(5),
                'project_officer_id' => $officer->id,
                'supervisor_id' => $supervisor->id,
                'mitra' => 'Koperasi Petani Sayur Lembang & Dinas Pertanian',
                'sponsor' => 'Bank Mandiri CSR & Tokopedia Hijau',
                'organisasi_pelaksana' => 'Komunitas Cisarua Berseri & Yota',
                'tujuan_program' => 'Mencegah pembuangan limbah sayuran ke sungai, memproduksi pupuk organik cair gratis untuk petani, dan membangun kebun gizi anak sekolah.',
                'target_capaian' => '5 ton sampah sayur terolah bulanan, 3.500 penerima manfaat, 310 kg CO2e reduksi emisi metana.',
                'stats' => [
                    'volunteers_count' => 22,
                    'beneficiaries_count' => 3500,
                    'carbon_offset' => 310.0,
                ],
            ]
        );

        ProjectCharter::updateOrCreate(
            ['program_id' => $prog3->id],
            [
                'vision' => 'Mewujudkan Cisarua sebagai sentra percontohan circular economy pertanian nol sampah di Jawa Barat.',
                'mission' => "1. Membangun 2 unit biodigester komunal pengolah limbah sisa pasar sayur.\n2. Mengedukasi 500 keluarga tentang pemilahan sampah organik dari dapur.\n3. Mengembangkan kebun edukasi hidroponik sayuran organik untuk nutrisi balita.",
                'scope' => 'Kecamatan Cisarua, Kabupaten Bandung Barat, Jawa Barat.',
            ]
        );

        // Add 1 Zone for Prog 3
        Zone::firstOrCreate(
            ['program_id' => $prog3->id, 'name' => 'Zona Kebun Pangan & Biodigester Cisarua'],
            [
                'description' => 'Pusat pengomposan limbah sayur dan greenhouse hidroponik.',
                'type' => 'activity',
                'status_zona' => 'aktif',
                'geojson' => [
                    [-6.800000, 107.560000],
                    [-6.800000, 107.600000],
                    [-6.830000, 107.600000],
                    [-6.830000, 107.560000]
                ]
            ]
        );

        // Milestones Prog 3
        if ($prog3->storyMilestones()->count() === 0) {
            $prog3->storyMilestones()->create([
                'day_number' => 1,
                'phase' => 'Build',
                'title' => 'Instalasi Biodigester Anaerobik 10 m³',
                'subtitle' => 'Konversi gas metana limbah sayuran menjadi energi kompor masak',
                'story_narrative' => 'Memasang tangki digester biogas kubah beton yang mengolah 200 kg limbah sayuran busuk setiap hari menjadi gas masak gratis bagi 8 keluarga sekitar.',
                'activity_name' => 'Pemasangan Tabung Reaktor Biogas',
                'personnel_involved' => 'Teknisi Energi Terbarukan & 22 Relawan',
                'key_achievement' => 'Tekanan gas stabil dan berhasil menyalakan 2 kompor tanpa bau.',
                'date' => Carbon::now()->subDays(15),
                'photos' => ['/images/projects/urban_farming.jpg'],
                'order' => 1,
            ]);
            $prog3->storyMilestones()->create([
                'day_number' => 2,
                'phase' => 'Deploy',
                'title' => 'Panen Perdana Sayur Hidroponik Kebun Gizi Anak',
                'subtitle' => '120 kg selada, pakcoy, dan kangkung organik dibagikan ke posyandu',
                'story_narrative' => 'Panen bersama ibu-ibu posyandu dari instalasi hidroponik vertikal yang memanfaatkan nutrisi pupuk cair organik hasil biodigester.',
                'activity_name' => 'Panen Raya & Edukasi Menu Sehat Anak',
                'personnel_involved' => 'Ibu-ibu Kader Posyandu & Relawan Gizi',
                'key_achievement' => 'Distribusi sayur segar ke 65 balita terindikasi stunting.',
                'date' => Carbon::now()->subDays(5),
                'photos' => ['/images/projects/urban_farming.jpg'],
                'order' => 2,
            ]);
        }

        // KPIs Prog 3
        if ($prog3->kpiIndicators()->count() === 0) {
            $prog3->kpiIndicators()->create([
                'code' => 'KPI-CSR-01',
                'name' => 'Limbah Sayuran Pasar Terolah Menjadi Pupuk',
                'category' => 'Circular Economy',
                'definition' => 'Total berat sisa panen sayur yang masuk ke digester dan komposter.',
                'baseline' => 0,
                'target' => 5000,
                'actual' => 3800,
                'unit' => 'Kg',
                'verified_by' => 'Dinas Pertanian Bandung Barat',
                'status' => 'achieved',
            ]);
            $prog3->kpiIndicators()->create([
                'code' => 'KPI-CSR-02',
                'name' => 'Metana Terhindari dari TPA (Methane Avoidance)',
                'category' => 'Reduksi Emisi',
                'definition' => 'Potensi emisi gas metana CH4 yang berhasil ditangkap dan dibakar untuk energi.',
                'baseline' => 0,
                'target' => 300,
                'actual' => 310,
                'unit' => 'Kg CO2e',
                'verified_by' => 'Yota Scientific Carbon Auditor',
                'status' => 'achieved',
                'carbon_breakdown' => [
                    'emissions_produced' => 95.0,
                    'emissions_avoided' => 310.0,
                    'carbon_sequestered' => 180.0,
                ]
            ]);
        }

        // Outputs Prog 3
        if ($prog3->projectOutputs()->count() === 0) {
            $prog3->projectOutputs()->create([
                'category' => 'prototype',
                'title' => 'Biodigester Anaerobik Portable Skala Komunal',
                'description' => 'Desain biodigester modular berbiaya terjangkau dengan sensor tekanan gas berbasis IoT mikrokontroler.',
                'thumbnail' => '/images/projects/urban_farming.jpg',
                'release_date' => Carbon::now()->subDays(12),
                'authors_or_team' => 'Laboratorium Rekayasa Lingkungan Yota',
                'ip_number' => 'PAT-BIO-2026-19',
                'tags' => ['Biogas', 'Energi Bersih', 'Zero Waste'],
            ]);
            $prog3->projectOutputs()->create([
                'category' => 'publication',
                'title' => 'Modul Praktis: Budidaya Hidroponik Sayur Dataran Tinggi Ramah Kantong',
                'description' => 'Pedoman mandiri bagi keluarga tani merakit rak hidroponik pipa PVC dan racikan nutrisi organik cair.',
                'thumbnail' => '/images/projects/urban_farming.jpg',
                'release_date' => Carbon::now()->subDays(6),
                'authors_or_team' => 'Tim Penyuluh Pertanian & Relawan Yota',
                'ip_number' => 'ISBN: 978-623-09-9014-8',
                'tags' => ['Pertanian Organik', 'Pangan Mandiri'],
            ]);
        }

        // 4. CREATE PROGRAM 4: AIR BERSIH & SANITASI CISOLOK
        $prog4 = Program::firstOrCreate(
            ['name' => 'Pipatisasi Air Bersih & MCK Sehat Pegunungan Cisolok'],
            [
                'description' => 'Penyaluran air bersih gravitasi sepanjang 3,8 kilometer dari mata air pegunungan Halimun Salak serta renovasi 4 MCK umum komunal higienis di desa terpencil.',
                'cover_image' => '/images/projects/clean_water.jpg',
                'banner_program' => '/images/projects/clean_water.jpg',
                'kategori_program' => 'Air Bersih & Sanitasi Higienis',
                'status' => 'aktif',
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->addMonths(3),
                'project_officer_id' => $officer->id,
                'supervisor_id' => $supervisor->id,
                'mitra' => 'Puskesmas Cisolok & Badan Pengelola Air Desa',
                'sponsor' => 'BCA Bakti Komunitas CSR',
                'organisasi_pelaksana' => 'Aksi Cepat Relawan Nusantara & Yota',
                'tujuan_program' => 'Memberikan akses air minum layak 24 jam bagi 1.850 warga yang selama ini mengalami krisis air saat kemarau serta menurunkan angka diare pada balita.',
                'target_capaian' => '3.800 meter pipa terpasang, 4 unit MCK higienis, 1.850 jiwa terlayani air bersih.',
                'stats' => [
                    'volunteers_count' => 19,
                    'beneficiaries_count' => 1850,
                    'carbon_offset' => 85.0,
                ],
            ]
        );

        ProjectCharter::updateOrCreate(
            ['program_id' => $prog4->id],
            [
                'vision' => 'Terbebasnya warga Cisolok dari krisis air bersih dan penyakit berbasis lingkungan menuju masyarakat sehat produktif.',
                'mission' => "1. Membangun bak penangkap mata air (broncaptering) di lereng gunung.\n2. Memasang jaringan pipa HDPE anti-pecah sepanjang 3,8 km.\n3. Membentuk panitia HIPPAM (Himpunan Pengguna Air Minum) mandiri berbasis warga.",
                'scope' => 'Desa Cikelat & Cisolok, Kabupaten Sukabumi, Jawa Barat.',
            ]
        );

        // Add 1 Zone for Prog 4
        Zone::firstOrCreate(
            ['program_id' => $prog4->id, 'name' => 'Zona Mata Air & MCK Sehat Cisolok'],
            [
                'description' => 'Jalur transmisi pipa gravitasi dan lokasi MCK komunal warga.',
                'type' => 'activity',
                'status_zona' => 'aktif',
                'geojson' => [
                    [-6.930000, 106.450000],
                    [-6.930000, 106.490000],
                    [-6.970000, 106.490000],
                    [-6.970000, 106.450000]
                ]
            ]
        );

        // Milestones Prog 4
        if ($prog4->storyMilestones()->count() === 0) {
            $prog4->storyMilestones()->create([
                'day_number' => 1,
                'phase' => 'Build',
                'title' => 'Gotong Royong Penarikan Pipa HDPE 3,8 Km Melintasi Jurang',
                'subtitle' => 'Pemasangan jalur pipa gravitasi tanpa menggunakan bahan bakar pompa',
                'story_narrative' => 'Bersama 80 warga desa, tim relawan mengangkut pipa HDPE gulungan ke punggung bukit terjal dan merakit sambungan pipa penyalur air bersih.',
                'activity_name' => 'Pemasangan Pipa Gravitasi Gunung',
                'personnel_involved' => '19 Relawan & Tokoh Warga Adat',
                'key_achievement' => 'Pipa tersambung sempurna dari hulu sumber mata air ke tandon penampung.',
                'date' => Carbon::now()->subDays(25),
                'photos' => ['/images/projects/clean_water.jpg'],
                'order' => 1,
            ]);
            $prog4->storyMilestones()->create([
                'day_number' => 2,
                'phase' => 'Deploy',
                'title' => 'Air Bersih Pertama Mengalir ke Kran Komunal Desa',
                'subtitle' => 'Uji kelayakan laboratorium parameter fisik, kimia, dan bakteriologi',
                'story_narrative' => 'Kran air pertama dibuka di hadapan seluruh warga desa. Air pegunungan jernih mengalir deras tanpa membutuhkan listrik dengan debit 1,8 liter per detik.',
                'activity_name' => 'Uji Kualitas Air & Peresmian Kran Desa',
                'personnel_involved' => 'Petugas Sanitarian Puskesmas & Seluruh Tim',
                'key_achievement' => 'Lolos uji bebas E. Coli standar Permenkes 2023.',
                'date' => Carbon::now()->subDays(9),
                'photos' => ['/images/projects/clean_water.jpg'],
                'order' => 2,
            ]);
        }

        // KPIs Prog 4
        if ($prog4->kpiIndicators()->count() === 0) {
            $prog4->kpiIndicators()->create([
                'code' => 'KPI-CSL-01',
                'name' => 'Akses Air Bersih Mengalir 24 Jam',
                'category' => 'Kesehatan Masyarakat',
                'definition' => 'Jumlah jiwa yang terlayani air bersih gravitasi langsung ke pekarangan rumah dan MCK.',
                'baseline' => 0,
                'target' => 1800,
                'actual' => 1850,
                'unit' => 'Jiwa',
                'verified_by' => 'Puskesmas Cisolok & Kantor Kecamatan',
                'status' => 'achieved',
            ]);
            $prog4->kpiIndicators()->create([
                'code' => 'KPI-CSL-02',
                'name' => 'Penurunan Angka Penyakit Diare Anak',
                'category' => 'Kesehatan',
                'definition' => 'Penurunan persentase kasus diare pada balita pasca intervensi air bersih.',
                'baseline' => 18,
                'target' => 2,
                'actual' => 1.2,
                'unit' => '% Kasus',
                'verified_by' => 'Surveilans Kesehatan Dinkes Sukabumi',
                'status' => 'achieved',
            ]);
        }

        // Outputs Prog 4
        if ($prog4->projectOutputs()->count() === 0) {
            $prog4->projectOutputs()->create([
                'category' => 'prototype',
                'title' => 'Filter Pasir Lambat (Slow Sand Filter) Batu Alami',
                'description' => 'Sistem penyaringan air alami tanpa bahan kimia memanfaatkan pasir silika, arang batok, dan kerikil sungai lokal.',
                'thumbnail' => '/images/projects/clean_water.jpg',
                'release_date' => Carbon::now()->subDays(15),
                'authors_or_team' => 'Tim Air Bersih Yota & Warga Cisolok',
                'ip_number' => 'DES-SAN-2026-03',
                'tags' => ['Filtrasi Alami', 'Air Minum', 'Sanitasi'],
            ]);
        }
    }
}
