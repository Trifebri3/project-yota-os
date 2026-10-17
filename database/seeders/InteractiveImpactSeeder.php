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
                'description' => 'Program pemberdayaan masyarakat dan restorasi pesisir Purbayani yang mengintegrasikan penanaman mangrove sabuk hijau, gerakan pemilahan sampah sirkular, dan digitalisasi UMKM nelayan lokal.',
                'cover_image' => '/images/projects/mangrove.jpg',
                'banner_program' => '/images/projects/mangrove.jpg',
                'kategori_program' => 'Restorasi Lingkungan & Pesisir',
                'status' => 'aktif',
                'mitra' => 'DLH Jawa Barat & Komunitas Nelayan Purbayani',
                'sponsor' => 'Pertamina CSR & Yayasan Yota',
                'organisasi_pelaksana' => 'Yota Social Development Taskforce',
                'tujuan_program' => 'Mewujudkan ekosistem pesisir tangguh abrasi, bebas sampah plastik muara, dan mengangkat kemandirian ekonomi keluarga nelayan.',
                'target_capaian' => '2.500 mangrove tertanam, 1.25 ton sampah terolah, 15 UMKM tervalidasi, 52 kg CO2e reduksi bersih.',
                'stats' => [
                    'volunteers_count' => 17,
                    'beneficiaries_count' => 1450,
                    'carbon_offset' => 52.0,
                ],
            ]);

            // Update/Create Charter
            ProjectCharter::updateOrCreate(
                ['program_id' => $cap->id],
                [
                    'vision' => 'Terwujudnya pesisir Purbayani yang lestari, berketahanan iklim, dan berdaya saing ekonomi melalui aksi kolaboratif multi-pihak.',
                    'mission' => "1. Memulihkan 3 hektar sempadan pantai melalui penanaman sabuk hijau mangrove.\n2. Mengedukasi 1.400+ warga tentang pemilahan sampah dari sumber.\n3. Memberdayakan 15 kelompok UMKM pesisir dalam pemasaran digital ramah lingkungan.",
                    'scope' => 'Kawasan Pesisir Pantai Rancabuaya, Muara Purbayani, Desa Purbayani, Garut Selatan, Jawa Barat.',
                ]
            );

            // Populate Story Milestones for CAP
            StoryMilestone::where('program_id', $cap->id)->delete();
            $capMilestones = [
                [
                    'day_number' => 1,
                    'phase' => 'Discover',
                    'title' => 'Observasi Lapangan & Pemetaan Risiko Abrasi',
                    'subtitle' => 'Identifikasi titik kritis 4 km garis pantai bersama tokoh nelayan',
                    'story_narrative' => 'Hari pertama dimulai dengan survei tapak geofence bersama ketua rukun nelayan. Tim mengidentifikasi titik abrasi air laut yang telah menggerus 15 meter daratan dalam 3 tahun terakhir serta mendata sumber sampah kiriman muara sungai.',
                    'activity_name' => 'Survei Geospasial & Diskusi Terfokus',
                    'personnel_involved' => '17 Relawan, 6 Tokoh Nelayan, 2 Fasilitator DLH',
                    'key_achievement' => 'Terpetakan 3 zona intervensi utama seluas 4,2 hektar dengan data GPS tervalidasi.',
                    'date' => Carbon::now()->subDays(14),
                    'photos' => ['/images/projects/mangrove.jpg', '/images/projects/urban_farming.jpg'],
                    'order' => 1,
                ],
                [
                    'day_number' => 2,
                    'phase' => 'Design',
                    'title' => 'Perancangan Sabuk Hijau & Alur Pilah Sampah',
                    'subtitle' => 'Menyusun Blueprint WBS dan alur pengomposan organik',
                    'story_narrative' => 'Tim merancang modul pembibitan mangrove jenis Rhizophora dan Avicennia serta mendesain prototipe Trash Boom terapung dari bambu lokal. Warga menyepakati pembentukan Bale Resik sebagai pusat pengolahan sampah organik.',
                    'activity_name' => 'Lokakarya Rancang Program Komunitas',
                    'personnel_involved' => 'Seluruh Relawan & Pengurus Karang Taruna',
                    'key_achievement' => 'Rancangan WBS disepakati warga desa dan disetujui Project Officer.',
                    'date' => Carbon::now()->subDays(11),
                    'photos' => ['/images/projects/urban_farming.jpg'],
                    'order' => 2,
                ],
                [
                    'day_number' => 3,
                    'phase' => 'Build',
                    'title' => 'Aksi Tanam 2.500 Bibit Mangrove & Pasang Trash Boom',
                    'subtitle' => 'Eksekusi fisik di zona pasang surut Pantai Rancabuaya',
                    'story_narrative' => 'Bersama 100+ warga lokal dan relawan, aksi penanaman serentak 2.500 bibit bakau berhasil dilaksanakan pada saat air surut pagi hari. Di saat bersamaan, penghalang sampah muara berhasil dipasang.',
                    'activity_name' => 'Penanaman Mangrove Masal & Instalasi Sabuk Sabut',
                    'personnel_involved' => '17 Relawan, 102 Warga Lokal, Tim Dinas Lingkungan',
                    'key_achievement' => '100% target penanaman 2.500 bibit tuntas dipasang dengan tiang pancang bambu.',
                    'date' => Carbon::now()->subDays(7),
                    'photos' => ['/images/projects/mangrove.jpg'],
                    'order' => 3,
                ],
                [
                    'day_number' => 4,
                    'phase' => 'Deploy',
                    'title' => 'Aktivasi Bale Resik & Inkubasi 15 UMKM Pesisir',
                    'subtitle' => 'Pemanfaatan pupuk kompos dan branding produk olahan laut',
                    'story_narrative' => 'Pusat daur ulang Bale Resik mulai beroperasi penuh mengolah sampah organik pasar ikan menjadi kompos bernutrisi tinggi. Tim ekonomi mendampingi 15 UMKM olahan abon ikan dan keripik rumput laut dalam sertifikasi halal dan foto produk.',
                    'activity_name' => 'Pelatihan Kemasan Ramah Lingkungan & Digital Marketing',
                    'personnel_involved' => 'Divisi UMKM & 15 Pengusaha Mikro Perempuan',
                    'key_achievement' => '15 UMKM memiliki katalog online terverifikasi dan logo kemasan biodegradable.',
                    'date' => Carbon::now()->subDays(3),
                    'photos' => ['/images/projects/urban_farming.jpg'],
                    'order' => 4,
                ],
                [
                    'day_number' => 5,
                    'phase' => 'Measure',
                    'title' => 'Audit Serapan Karbon & Evaluasi Dampak Bersama',
                    'subtitle' => 'Kalkulasi emisi terhindari dan rilis laporan publik',
                    'story_narrative' => 'Menggunakan Yota Carbon Engine, tim menghitung emisi terhindari dari pengolahan 1.25 ton sampah dan estimasi serapan biomassa mangrove muda. Hasilnya dipresentasikan secara terbuka di balai desa di hadapan kepala desa dan warga.',
                    'activity_name' => 'Audit Dampak Lingkungan & Rapat Pleno Desa',
                    'personnel_involved' => 'Super Admin, Project Officer, Tim Ahli Lingkungan',
                    'key_achievement' => 'Tingkat kelulusan hidup bibit mencapai 94,8% dan reduksi 52 kg CO2e tercatat valid.',
                    'date' => Carbon::now()->subDays(1),
                    'photos' => ['/images/projects/mangrove.jpg'],
                    'order' => 5,
                ],
            ];
            foreach ($capMilestones as $m) {
                $cap->storyMilestones()->create($m);
            }

            // Populate Impact Chain for CAP
            ImpactChain::where('program_id', $cap->id)->delete();
            $capImpactChains = [
                [
                    'stage' => 'input',
                    'stage_order' => 1,
                    'title' => 'Sumber Daya & Kolaborasi Multi-Pihak',
                    'description' => 'Pendanaan kolaboratif CSR Pertamina, 17 personel relawan tersertifikasi, 2.500 bibit mangrove unggul, serta drum komposter higienis.',
                    'metric_value' => '17 Relawan & Rp 45 Juta',
                    'metric_unit' => 'Alokasi Penuh',
                    'target_value' => '100% Siap',
                    'evidence_note' => 'Dokumen MoU kemitraan, purchase order bibit, dan surat tugas relawan terlampir.',
                    'verification_status' => 'verified',
                ],
                [
                    'stage' => 'activity',
                    'stage_order' => 2,
                    'title' => 'Pelaksanaan Aksi Lapangan Berbasis Geofence',
                    'description' => 'Penanaman bibit di zona koordinat tervalidasi, pembersihan muara sungai berkala, absensi geotagging, dan pelatihan UMKM berkesinambungan.',
                    'metric_value' => '65 Sesi Kegiatan',
                    'metric_unit' => 'Aksi Tervalidasi',
                    'target_value' => '60 Sesi',
                    'evidence_note' => '935 entri logbook harian digital dan koordinat GPS poligon zona.',
                    'verification_status' => 'verified',
                ],
                [
                    'stage' => 'output',
                    'stage_order' => 3,
                    'title' => 'Produk Fisik & Infrastruktur Komunitas',
                    'description' => '2.500 mangrove tertanam kuat, 1 unit rumah kompos Bale Resik aktif, 1 unit trash barrier muara sungai, dan 15 UMKM tervalidasi sertifikasinya.',
                    'metric_value' => '2.500 Pohon & 1 Unit Hub',
                    'metric_unit' => 'Hasil Riil',
                    'target_value' => '2.500 Pohon',
                    'evidence_note' => 'Berita Acara Serah Terima (BAST) sarana dan daftar registrasi UMKM.',
                    'verification_status' => 'verified',
                ],
                [
                    'stage' => 'outcome',
                    'stage_order' => 4,
                    'title' => 'Perubahan Perilaku & Pengurangan Polusi',
                    'description' => 'Terjadi penurunan 78% pembuangan sampah ke sungai oleh warga, 1.250 kg sampah terolah, dan adopsi kemasan organik oleh pedagang lokal.',
                    'metric_value' => '1.250 Kg Sampah',
                    'metric_unit' => 'Teralihkan dari Laut',
                    'target_value' => '1.000 Kg',
                    'evidence_note' => 'Survei baseline vs endline pengetahuan lingkungan warga desa (skor naik dari 42% ke 89%).',
                    'verification_status' => 'verified',
                ],
                [
                    'stage' => 'impact',
                    'stage_order' => 5,
                    'title' => 'Ketahanan Pesisir & Kesejahteraan Jangka Panjang',
                    'description' => 'Tercapainya perlindungan abrasi pada 3 hektar sempadan pantai, peningkatan rata-rata omzet UMKM binaan sebesar 24%, dan model desa percontohan pesisir tangguh iklim.',
                    'metric_value' => '1.450 Jiwa & 3 Ha Pesisir',
                    'metric_unit' => 'Dampak Berkelanjutan',
                    'target_value' => 'Target Tercapai',
                    'evidence_note' => 'Surat Pengakuan Program Berkelanjutan dari DLH dan Kantor Desa Purbayani.',
                    'verification_status' => 'verified',
                ],
            ];
            foreach ($capImpactChains as $ic) {
                $cap->impactChains()->create($ic);
            }

            // Populate KPI Indicators for CAP
            KpiIndicator::where('program_id', $cap->id)->delete();
            $capKpis = [
                [
                    'code' => 'KPI-ENV-01',
                    'name' => 'Bibit Mangrove Tertanam & Bertumbuh',
                    'category' => 'Lingkungan & Pesisir',
                    'definition' => 'Jumlah bibit Rhizophora Mucronata yang berhasil ditanam pada zona pasang surut dengan survival rate minimal 90%.',
                    'baseline' => 0,
                    'target' => 2500,
                    'actual' => 2500,
                    'unit' => 'Pohon',
                    'measurement_method' => 'Sensus fisik berkala dan penandaan titik GPS per blok tanam.',
                    'verified_by' => 'Tim Pengawas Lingkungan Hidup Kab. Garut',
                    'status' => 'achieved',
                    'evidence_doc' => 'Berita Acara Lapangan No. 042/DLH/MNG/2026',
                    'trend_data' => ['M-1' => 0, 'M-2' => 800, 'M-3' => 1800, 'M-4' => 2500],
                ],
                [
                    'code' => 'KPI-ENV-02',
                    'name' => 'Sampah Organik & Anorganik Teralihkan',
                    'category' => 'Pengelolaan Sampah',
                    'definition' => 'Volume total limbah padat pesisir yang dipilah, diolah menjadi kompos, atau disalurkan ke fasilitas daur ulang resmi.',
                    'baseline' => 0,
                    'target' => 1500,
                    'actual' => 1250,
                    'unit' => 'Kg',
                    'measurement_method' => 'Timbangan gantung digital harian di Bale Resik terhubung logbook digital.',
                    'verified_by' => 'Supervisor Pengelolaan Sampah Yota',
                    'status' => 'achieved',
                    'evidence_doc' => 'Rekapitulasi Timbangan Bale Resik Periode Juni-Juli 2026',
                    'trend_data' => ['M-1' => 120, 'M-2' => 450, 'M-3' => 890, 'M-4' => 1250],
                ],
                [
                    'code' => 'KPI-CAR-03',
                    'name' => 'Reduksi & Serapan Emisi Karbon Bersih',
                    'category' => 'Mitigasi Karbon & Iklim',
                    'definition' => 'Kalkulasi dekonstruksi karbon: Emisi operasional vs Emisi terhindari (waste avoidance) vs Potensi serapan biomassa.',
                    'baseline' => 0,
                    'target' => 50,
                    'actual' => 52,
                    'unit' => 'Kg CO2e',
                    'measurement_method' => 'Yota Carbon Engine berbasis IPCC Guidelines 2019 Tier 1.',
                    'verified_by' => 'Yota Scientific Carbon Auditor',
                    'status' => 'achieved',
                    'evidence_doc' => 'Sertifikat Audit Jejak Karbon Program No. YOT-CARB-2026-091',
                    'trend_data' => ['M-1' => 10, 'M-2' => 24, 'M-3' => 41, 'M-4' => 52],
                    'carbon_breakdown' => [
                        'emissions_produced' => 802.9, // dari transportasi & operasional
                        'emissions_avoided' => 52.0,   // dari daur ulang & pengomposan
                        'carbon_sequestered' => 310.0, // estimasi 3 tahun pertumbuhan mangrove
                    ],
                ],
                [
                    'code' => 'KPI-SOC-04',
                    'name' => 'Penerima Manfaat Sosialisasi & Bantuan',
                    'category' => 'Sosial & Komunitas',
                    'definition' => 'Individu dari keluarga nelayan yang menerima dampak langsung program: pelatihan, fasilitas, dan edukasi.',
                    'baseline' => 150,
                    'target' => 1200,
                    'actual' => 1450,
                    'unit' => 'Jiwa',
                    'measurement_method' => 'Daftar hadir digital kegiatan sosialisasi dan kartu keluarga penerima sarana.',
                    'verified_by' => 'Pemerintah Desa Purbayani',
                    'status' => 'achieved',
                    'evidence_doc' => 'Data Kependudukan Desa Purbayani & Lembar Absensi Warga',
                    'trend_data' => ['M-1' => 250, 'M-2' => 650, 'M-3' => 1100, 'M-4' => 1450],
                ],
                [
                    'code' => 'KPI-ECO-05',
                    'name' => 'UMKM Binaan Naik Kelas & Terfasilitasi',
                    'category' => 'Penguatan Ekonomi',
                    'definition' => 'Kelompok usaha mikro keluarga nelayan yang mengadopsi standar higienis kemasan ramah lingkungan dan kanal jual daring.',
                    'baseline' => 0,
                    'target' => 15,
                    'actual' => 15,
                    'unit' => 'Unit Usaha',
                    'measurement_method' => 'Verifikasi kemasan produk dan transaksi penjualan tercatat.',
                    'verified_by' => 'Pendamping Lapangan Bidang UMKM',
                    'status' => 'achieved',
                    'evidence_doc' => 'Katalog Profil 15 UMKM Nelayan Purbayani',
                    'trend_data' => ['M-1' => 2, 'M-2' => 7, 'M-3' => 12, 'M-4' => 15],
                ],
            ];
            foreach ($capKpis as $kpi) {
                $cap->kpiIndicators()->create($kpi);
            }

            // Populate Project Outputs for CAP
            ProjectOutput::where('program_id', $cap->id)->delete();
            $capOutputs = [
                [
                    'category' => 'prototype',
                    'title' => 'Trash Barrier Boom Ramah Lingkungan (Bambu & Jaring)',
                    'description' => 'Penghalang sampah terapung berbahan bambu lokal yang dipasang melintang muara sungai untuk menahan sampah plastik sebelum masuk ke perairan laut terbuka.',
                    'thumbnail' => '/images/projects/mangrove.jpg',
                    'release_date' => Carbon::now()->subDays(6),
                    'authors_or_team' => 'Divisi Inovasi Teknologi Relawan Yota & Nelayan Lokal',
                    'evidence_url' => 'https://example.com/evidence/trash-boom-spec.pdf',
                    'ip_number' => 'PAT-PROTO-2026-08',
                    'tags' => ['Teknologi Tepat Guna', 'Pengendalian Sampah', 'Bambu Lokal'],
                ],
                [
                    'category' => 'publication',
                    'title' => 'Buku Saku: Panduan Aksi Restorasi Mangrove Komunitas Pesisir',
                    'description' => 'Buku panduan bergambar setebal 48 halaman memuat teknik pemilihan bibit, jarak tanam di zona pasang surut, hingga perawatan berkala dari hama teritip.',
                    'thumbnail' => '/images/projects/mangrove.jpg',
                    'release_date' => Carbon::now()->subDays(4),
                    'authors_or_team' => 'Tim Modul Riset Yota Foundation & DLH Jabar',
                    'evidence_url' => 'https://example.com/books/panduan-restorasi-purbayani.pdf',
                    'ip_number' => 'ISBN: 978-623-09-8812-1',
                    'tags' => ['Buku Pedoman', 'Pendidikan Lingkungan', 'Open Access'],
                ],
                [
                    'category' => 'ip_recognition',
                    'title' => 'Sertifikat HAKI Kemenkumham: Metodologi Manajemen Relawan Berbasis Geofence',
                    'description' => 'Surat Pencatatan Ciptaan resmi Kementerian Hukum dan HAM RI untuk karya tulis ilmiah perancangan sistem operasional monitoring dampak sosial terdesentralisasi.',
                    'thumbnail' => '/images/logo.webp',
                    'release_date' => Carbon::now()->subDays(10),
                    'authors_or_team' => 'Yota Foundation Innovation Center',
                    'evidence_url' => 'https://example.com/haki/EC00202619821',
                    'ip_number' => 'No. Pencatatan: 000591241 / HAKI RI',
                    'tags' => ['Hak Cipta', 'Kemenkumham RI', 'Intelektual'],
                ],
                [
                    'category' => 'digital',
                    'title' => 'Interactive GIS Map Dashboard Pesisir Purbayani',
                    'description' => 'Web map interaktif berbasis GeoJSON yang menampilkan poligon zona lindung, titik kegiatan penanaman, serta data logbook terkoneksi secara real-time.',
                    'thumbnail' => '/images/projects/mangrove.jpg',
                    'release_date' => Carbon::now()->subDays(2),
                    'authors_or_team' => 'Tim Engineering Yota OS',
                    'evidence_url' => '/map-data',
                    'ip_number' => 'v2.4-STABLE',
                    'tags' => ['Web GIS', 'Leaflet', 'Open Data'],
                ],
            ];
            foreach ($capOutputs as $po) {
                $cap->projectOutputs()->create($po);
            }

            // Populate Before & After for CAP
            BeforeAfterRecord::where('program_id', $cap->id)->delete();
            $cap->beforeAfterRecords()->create([
                'title' => 'Muara Sungai & Bibir Pantai Purbayani',
                'location' => 'Blok Pasang Surut Rancabuaya (Koordinat -6.0890, 106.7200)',
                'before_image' => '/images/projects/urban_farming.jpg', // baseline condition
                'after_image' => '/images/projects/mangrove.jpg', // lush green after
                'before_date' => Carbon::now()->subMonths(2),
                'after_date' => Carbon::now()->subDays(2),
                'before_description' => 'Kondisi awal bibir pantai mengalami erosi akibat gelombang pasang, minim vegetasi pelindung, dan dipenuhi timbunan sampah kiriman muara sungai.',
                'after_description' => '2.500 bibit mangrove tertanam rapi dilindungi sabuk bambu, area muara bersih dari sampah terapung, dan terbentuk ekosistem pantai yang kokoh.',
                'metric_impact' => 'Penurunan 80% sampah muara & 94.8% bibit hidup subur',
            ]);
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
