<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Program & Tinjauan Bukti — Collaborative Action Program (CAP) 2026</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800,900|jetbrains-mono:400,600,700&display=swap" rel="stylesheet" />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        .font-mono-code {
            font-family: 'JetBrains Mono', monospace;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; font-size: 11pt; }
            .page-break { page-break-before: always; }
            .shadow-sm, .shadow-md, .shadow-xl, .shadow-2xl { box-shadow: none !important; }
            .border { border-color: #cbd5e1 !important; }
        }
    </style>
</head>
<body class="antialiased min-h-screen text-slate-800">

    <!-- Top Action Bar (Fixed, Hidden on Print) -->
    <div class="no-print sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200 px-4 sm:px-8 py-3">
        <div class="max-w-6xl mx-auto flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('public.projects.show', $program->id) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-emerald-700 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-xl transition shadow-sm">
                    &larr; <span>Kembali ke Slideshow Interaktif</span>
                </a>
                <span class="text-xs text-slate-300">|</span>
                <span class="text-xs font-black text-slate-800 hidden sm:inline">CAP PURBAYANI 2026</span>
                <span class="text-[10px] font-mono font-bold bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full uppercase">Versi 1.0 &bull; 19 Okt 2026</span>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-xl shadow transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H7a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak Dokumen / Simpan PDF</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Document Container -->
    <main class="max-w-5xl mx-auto p-4 sm:p-8 lg:p-12 space-y-10 my-4 sm:my-8 bg-white rounded-3xl border border-slate-200 shadow-xl print:m-0 print:p-0 print:border-0 print:shadow-none">

        <!-- ============================================== -->
        <!-- HEADER & DOCUMENT COVER                       -->
        <!-- ============================================== -->
        <header class="border-b border-slate-200 pb-8 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-700 text-white font-black text-xl flex items-center justify-center shadow-md">
                        Y
                    </div>
                    <div>
                        <span class="text-xs font-mono uppercase tracking-widest text-slate-400 font-bold block">Yota Foundation Collaborative Action</span>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight uppercase">
                            COLLABORATIVE ACTION PROGRAM
                        </h1>
                    </div>
                </div>

                <div class="text-right">
                    <span class="inline-block text-xs font-black uppercase tracking-wider bg-slate-900 text-white px-3 py-1 rounded-lg">
                        Laporan Program & Tinjauan Bukti
                    </span>
                    <span class="text-[11px] text-slate-500 block mt-1 font-mono">Versi 1.0 &bull; 19 Oktober 2026</span>
                </div>
            </div>

            <!-- Execution Meta Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                <div>
                    <span class="text-slate-400 uppercase text-[10px] font-bold block">Periode Pelaksanaan:</span>
                    <strong class="text-slate-900 text-sm">5 Juli – 5 Agustus 2026 (31 Hari)</strong>
                </div>
                <div>
                    <span class="text-slate-400 uppercase text-[10px] font-bold block">Fokus Wilayah / Lokasi:</span>
                    <strong class="text-slate-900 text-sm">Pesisir & Kawasan Desa Purbayani</strong>
                </div>
                <div>
                    <span class="text-slate-400 uppercase text-[10px] font-bold block">Klasifikasi Dokumen:</span>
                    <strong class="text-emerald-800 text-sm">Dokumen Kerja, Rekonsiliasi & Keberlanjutan</strong>
                </div>
            </div>

            <!-- CATATAN PENTING ALERT BOX -->
            <div class="p-5 rounded-2xl bg-amber-50/90 border-l-4 border-amber-500 text-xs text-amber-950 space-y-1 shadow-sm">
                <div class="flex items-center gap-2 font-black uppercase text-[11px] text-amber-900">
                    <span class="text-base">⚠️</span>
                    <span>CATATAN PENTING & BATASAN AUDIT</span>
                </div>
                <p class="leading-relaxed text-amber-900/90">
                    Laporan ini disusun dari informasi program yang tersedia dalam percakapan dan ringkasan data. Dokumen ini bukan opini audit independen dan tidak menyatakan bahwa seluruh angka telah diverifikasi. Nilai, keluaran, dan klaim yang belum memiliki bukti pendukung ditandai untuk validasi.
                </p>
            </div>
        </header>

        <!-- ============================================== -->
        <!-- RINGKASAN EKSEKUTIF                           -->
        <!-- ============================================== -->
        <section class="space-y-4">
            <h2 class="text-xl font-black text-slate-900 flex items-center gap-2">
                <span class="w-2.5 h-6 rounded-full bg-emerald-600"></span>
                <span>Ringkasan Eksekutif</span>
            </h2>

            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed text-justify">
                Collaborative Action Program (CAP) 2026 dilaksanakan pada <strong>5 Juli–5 Agustus 2026 selama 31 hari</strong>. Informasi yang tersedia menggambarkan intervensi berbasis komunitas yang menggabungkan aksi bersih kawasan pesisir, pengembangan fasilitas pengelolaan sampah, edukasi, penguatan kelompok masyarakat, serta dukungan digitalisasi dan promosi kawasan.
            </p>

            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed text-justify">
                Sejumlah keluaran telah dilaporkan, antara lain pengumpulan <strong>372 kg sampah pesisir</strong>, keterlibatan <strong>33 orang dalam Coastal Clean Up</strong>, pembentukan atau penguatan kelompok <strong>Bale Resik yang beranggotakan 12 orang</strong>, pengadaan fasilitas pengolahan sampah, serta aktivitas edukasi yang diperkirakan menjangkau sekitar <strong>127 peserta didik/anggota masyarakat</strong>. Informasi tambahan menyebutkan sekitar <strong>34 kepala keluarga</strong> di kawasan permukiman, produksi <strong>17 kg maggot</strong>, dan pemrosesan sekitar <strong>400 kg sampah residu melalui tungku minim asap</strong>. Semua angka tersebut perlu ditautkan dengan catatan sumber, tanggal, metode pengukuran, dan bukti dokumentasi sebelum dipublikasikan sebagai hasil terverifikasi.
            </p>

            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-950">
                <strong class="text-emerald-900 uppercase font-black text-[10px] tracking-wider block mb-1">KESIMPULAN UTAMA</strong>
                <p class="leading-relaxed">
                    Program menunjukkan portofolio kegiatan dan keluaran yang beragam. Prioritas berikutnya adalah memperkuat jejak bukti, menyelesaikan rekonsiliasi angka sampah dan keuangan, menguji tata kelola operasional fasilitas, serta menetapkan mekanisme keberlanjutan yang dapat dipertanggungjawabkan.
                </p>
            </div>
        </section>

        <!-- ============================================== -->
        <!-- TABEL SOROTAN DATA SEMENTARA                  -->
        <!-- ============================================== -->
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900">
                    Sorotan Data Sementara
                </h3>
                <span class="text-[11px] font-mono text-slate-400">Tabel Rekap Ringkas</span>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="p-3.5">Aspek</th>
                            <th class="p-3.5">Informasi yang Tersedia</th>
                            <th class="p-3.5">Status Pelaporan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 font-bold text-slate-900">Periode</td>
                            <td class="p-3">5 Juli–5 Agustus 2026 (31 hari)</td>
                            <td class="p-3"><span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Perlu dicocokkan dengan dokumen program</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 font-bold text-slate-900">Sampah pesisir</td>
                            <td class="p-3">372 kg dikumpulkan dan dibawa ke TPS3R</td>
                            <td class="p-3"><span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Dilaporkan; bukti timbang/serah terima perlu dilampirkan</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 font-bold text-slate-900">Peserta aksi bersih</td>
                            <td class="p-3">33 orang</td>
                            <td class="p-3"><span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Dilaporkan; verifikasi daftar hadir</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 font-bold text-slate-900">Kelompok masyarakat</td>
                            <td class="p-3">Bale Resik, 12 anggota</td>
                            <td class="p-3"><span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Dilaporkan; verifikasi daftar anggota dan status operasional</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 font-bold text-slate-900">Edukasi</td>
                            <td class="p-3">Sekitar 127 peserta didik/anggota masyarakat</td>
                            <td class="p-3"><span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Perkiraan; perlu daftar hadir dan rincian sesi</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 font-bold text-slate-900">Fasilitas/keluaran</td>
                            <td class="p-3">1 bangunan organik, 3 komposter, 1 pencacah organik, 1 fasilitas residu, 5 set tempat sampah terpilah</td>
                            <td class="p-3"><span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Dilaporkan; perlu inventaris dan foto bertanggal</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 font-bold text-slate-900">Sampah residu</td>
                            <td class="p-3">Sekitar 400 kg diproses melalui tungku minim asap</td>
                            <td class="p-3"><span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Perlu catatan timbang, periode, jenis sampah, dan SOP</span></td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 font-bold text-slate-900">Keuangan</td>
                            <td class="p-3">Total anggaran dilaporkan Rp15.607.041</td>
                            <td class="p-3"><span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Belum dianggap tervalidasi secara audit</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Cara Membaca Laporan Ini -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1.5">
                <strong class="text-slate-900 font-bold uppercase text-[10px] tracking-wider block">Cara Membaca Laporan Ini:</strong>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Data yang disebut “dilaporkan” merupakan informasi yang tersedia, bukan pernyataan bahwa bukti audit sudah diperiksa.</li>
                    <li>Angka perkiraan harus tetap ditulis sebagai perkiraan sampai didukung bukti primer.</li>
                    <li>Keluaran (output) tidak otomatis membuktikan perubahan perilaku, dampak lingkungan, peningkatan pendapatan, atau keberlanjutan.</li>
                    <li>Rekomendasi dan rencana ke depan dibedakan dari kegiatan yang sudah selesai dilakukan.</li>
                </ul>
            </div>
        </section>

        <!-- ============================================== -->
        <!-- 1. LATAR BELAKANG DAN KONTEKS PROGRAM          -->
        <!-- ============================================== -->
        <section class="space-y-4">
            <h3 class="text-lg font-black text-slate-900 border-l-4 border-emerald-600 pl-3">
                1. Latar Belakang dan Konteks Program
            </h3>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed text-justify">
                Program CAP 2026 diposisikan sebagai upaya kolaboratif untuk memperkuat pengelolaan sampah, keterlibatan masyarakat, edukasi, serta dukungan informasi dan promosi bagi kawasan. Dalam kerangka ini, aksi bersih pesisir menjadi kegiatan yang terlihat langsung, sementara fasilitas pengelolaan sampah dan kelompok masyarakat dimaksudkan untuk mendukung praktik yang berkelanjutan.
            </p>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed text-justify">
                Namun, dokumen sumber yang tersedia belum memuat baseline terukur tentang volume sampah sebelum program, komposisi sampah, pola pengumpulan rutin, tingkat partisipasi sebelum intervensi, atau kondisi ekonomi pelaku usaha. Karena itu, laporan ini tidak menyimpulkan adanya penurunan sampah, perubahan perilaku permanen, peningkatan pendapatan, atau perbaikan kualitas lingkungan tanpa data pembanding.
            </p>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                <strong class="text-slate-900 text-xs font-bold uppercase tracking-wider block">1.1 Fokus intervensi yang teridentifikasi:</strong>
                <ul class="list-disc pl-5 text-xs text-slate-700 space-y-1">
                    <li>Aksi bersih kawasan pesisir dan pemindahan sampah ke TPS3R.</li>
                    <li>Pengembangan sarana pengelolaan sampah organik dan residu.</li>
                    <li>Penguatan kelompok komunitas Bale Resik.</li>
                    <li>Edukasi/pelatihan bagi peserta didik dan masyarakat.</li>
                    <li>Pemanfaatan kanal digital untuk informasi kawasan, titik lokasi, dan promosi UMKM/destinasi.</li>
                    <li>Penjajakan kemitraan dan model keberlanjutan bersama pemangku kepentingan lokal.</li>
                </ul>
            </div>
        </section>

        <!-- ============================================== -->
        <!-- 2. TUJUAN DAN KERANGKA PERUBAHAN              -->
        <!-- ============================================== -->
        <section class="space-y-4">
            <h3 class="text-lg font-black text-slate-900 border-l-4 border-emerald-600 pl-3">
                2. Tujuan dan Kerangka Perubahan
            </h3>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed text-justify">
                Berdasarkan portofolio kegiatan yang dilaporkan, tujuan operasional program dapat dirumuskan sebagai penguatan kapasitas komunitas dan sistem lokal untuk mengelola sampah, meningkatkan partisipasi masyarakat, memperluas edukasi, dan mendukung visibilitas ekonomi lokal melalui sarana fisik serta digital.
            </p>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="p-3">Tingkat Hasil</th>
                            <th class="p-3">Rumusan</th>
                            <th class="p-3">Contoh Indikator yang Perlu Dipantau</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Input</td>
                            <td class="p-3">Dana, tenaga komunitas, fasilitas, pendampingan, kanal digital</td>
                            <td class="p-3 text-slate-500">Realisasi biaya, jam kerja, dukungan mitra, ketersediaan aset</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Aktivitas</td>
                            <td class="p-3">Aksi bersih, edukasi, pembangunan/pengadaan fasilitas, pendampingan digital</td>
                            <td class="p-3 text-slate-500">Jumlah sesi, frekuensi kegiatan, peserta, dokumentasi</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Output</td>
                            <td class="p-3">Sampah terkumpul, fasilitas tersedia, kelompok terbentuk/aktif, materi edukasi tersampaikan</td>
                            <td class="p-3 text-slate-500">Kg terukur, jumlah aset, daftar anggota, jumlah peserta</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Outcome awal</td>
                            <td class="p-3">Penggunaan fasilitas dan kanal digital secara rutin; praktik pemilahan mulai diterapkan</td>
                            <td class="p-3 text-slate-500">Log penggunaan, audit spot-check, survei sebelum-sesudah</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Dampak jangka panjang</td>
                            <td class="p-3">Sistem pengelolaan sampah dan ekonomi lokal yang lebih tangguh</td>
                            <td class="p-3 text-slate-500">Data berkala, baseline dan endline, bukti lintas periode</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-100 border border-slate-200 text-xs text-slate-600">
                <strong class="text-slate-800 font-bold uppercase text-[10px]">BATASAN:</strong>
                Outcome dan dampak jangka panjang merupakan arah perubahan yang diharapkan, bukan hasil yang telah dibuktikan oleh angka keluaran yang tersedia.
            </div>
        </section>

        <!-- ============================================== -->
        <!-- 3. METODOLOGI PELAKSANAAN DAN SIKLUS KERJA     -->
        <!-- ============================================== -->
        <section class="space-y-4">
            <h3 class="text-lg font-black text-slate-900 border-l-4 border-emerald-600 pl-3">
                3. Metodologi Pelaksanaan dan Siklus Kerja (6 Tahap)
            </h3>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed text-justify">
                Kerangka kerja yang disarankan untuk membaca dan menata pelaksanaan CAP adalah siklus enam tahap: Discovery, Prioritization, Design, Small-Scale Test, Community Validation, dan Institutionalization. Siklus ini dapat digunakan sebagai struktur pelaporan, tetapi tanggal dan bukti pelaksanaan per tahap perlu dikonfirmasi dari logbook, notulen, rencana kerja, dan dokumentasi lapangan.
            </p>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="p-3 w-1/4">Tahap</th>
                            <th class="p-3 w-1/3">Tujuan</th>
                            <th class="p-3">Bukti yang Perlu Dikumpulkan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-3 font-bold text-slate-900">1. Discovery</td>
                            <td class="p-3">Memahami persoalan, aktor, alur sampah, dan kondisi awal</td>
                            <td class="p-3 text-slate-600">Catatan observasi, wawancara, peta alur, baseline</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">2. Prioritization</td>
                            <td class="p-3">Memilih persoalan dan intervensi prioritas</td>
                            <td class="p-3 text-slate-600">Notulen musyawarah, matriks prioritas, keputusan</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">3. Design</td>
                            <td class="p-3">Merancang kegiatan, fasilitas, peran, dan sumber daya</td>
                            <td class="p-3 text-slate-600">Rencana kerja, desain fasilitas, RAB, pembagian peran</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">4. Small-Scale Test</td>
                            <td class="p-3">Menguji kegiatan/fasilitas pada skala terbatas</td>
                            <td class="p-3 text-slate-600">Log operasi, data timbang, catatan masalah dan perbaikan</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">5. Community Validation</td>
                            <td class="p-3">Meminta masukan pengguna dan komunitas</td>
                            <td class="p-3 text-slate-600">Daftar hadir, umpan balik, berita acara, revisi desain</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">6. Institutionalization</td>
                            <td class="p-3">Menetapkan tata kelola, pembiayaan, SOP, dan tanggung jawab</td>
                            <td class="p-3 text-slate-600">SK kelompok, SOP, kesepakatan mitra, rencana biaya operasi</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-slate-600 italic">
                Siklus perlu bersifat iteratif: temuan pada uji skala kecil dan validasi komunitas dapat memicu revisi desain atau prioritas. Untuk pelaporan final, setiap tahap harus ditautkan ke tanggal, penanggung jawab, dan bukti yang dapat ditelusuri.
            </p>
        </section>

        <!-- ============================================== -->
        <!-- 4. PELAKSANAAN PROGRAM                        -->
        <!-- ============================================== -->
        <section class="space-y-4">
            <h3 class="text-lg font-black text-slate-900 border-l-4 border-emerald-600 pl-3">
                4. Pelaksanaan Program
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <strong class="text-slate-900 text-xs font-bold uppercase block">4.1 Aksi Bersih Pesisir</strong>
                    <p class="text-xs text-slate-700 leading-relaxed text-justify">
                        Informasi yang tersedia melaporkan <strong>372 kg sampah pesisir</strong> dikumpulkan dan dibawa ke TPS3R, dengan keterlibatan <strong>33 orang</strong>. Angka ini sebaiknya didukung oleh catatan timbang, foto sebelum-sesudah, daftar hadir, bukti pemindahan/serah terima ke TPS3R, serta informasi tentang tanggal, lokasi, dan jenis sampah. Tanpa bukti tersebut, angka tetap disajikan sebagai data yang dilaporkan.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <strong class="text-slate-900 text-xs font-bold uppercase block">4.2 Pengembangan Fasilitas Pengelolaan Sampah</strong>
                    <p class="text-xs text-slate-700 leading-relaxed text-justify">
                        Keluaran fisik yang dilaporkan mencakup satu bangunan pengelolaan sampah organik, tiga komposter, satu mesin pencacah organik, satu fasilitas pengolahan residu berupa tungku minim asap, dan lima set tempat sampah terpilah di zona wisata. Verifikasi perlu mencakup inventaris aset, lokasi, kondisi aktual, tanggal serah terima, pihak pemilik/pengelola, status fungsi, kebutuhan perawatan, serta bukti penggunaan.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <strong class="text-slate-900 text-xs font-bold uppercase block">4.3 Kelompok Bale Resik</strong>
                    <p class="text-xs text-slate-700 leading-relaxed text-justify">
                        Bale Resik dilaporkan beranggotakan <strong>12 orang</strong> dan berperan dalam pengelolaan fasilitas. Untuk menyatakan kelompok telah beroperasi secara rutin, diperlukan bukti lebih lanjut berupa daftar anggota, struktur pengurus, jadwal kerja, log aktivitas, pembagian tanggung jawab, catatan pemasukan/pengeluaran, dan keputusan formal seperti Surat Keputusan (SK) atau dokumen pengakuan yang relevan.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <strong class="text-slate-900 text-xs font-bold uppercase block">4.4 Edukasi dan Keterlibatan Masyarakat</strong>
                    <p class="text-xs text-slate-700 leading-relaxed text-justify">
                        Program dilaporkan menjangkau sekitar <strong>127 peserta didik/anggota masyarakat</strong> melalui edukasi atau pelatihan. Selain itu, terdapat informasi mengenai sekitar <strong>34 kepala keluarga</strong> di kawasan permukiman. Kedua angka tersebut perlu dipisahkan menurut kelompok sasaran dan jenis interaksi agar tidak terjadi penghitungan ganda. Pelaporan ideal memuat jumlah sesi, lokasi, tanggal, daftar hadir, materi, foto kegiatan, serta hasil evaluasi singkat.
                    </p>
                </div>
            </div>
        </section>

        <!-- ============================================== -->
        <!-- 5. REKAPITULASI OUTPUT YANG DILAPORKAN         -->
        <!-- ============================================== -->
        <section class="space-y-4">
            <h3 class="text-lg font-black text-slate-900 border-l-4 border-emerald-600 pl-3">
                5. Rekapitulasi Output yang Dilaporkan
            </h3>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="p-3 w-12 text-center">No.</th>
                            <th class="p-3">Output</th>
                            <th class="p-3">Jumlah/Status yang Dilaporkan</th>
                            <th class="p-3">Bukti Minimum untuk Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono-code">
                        <tr>
                            <td class="p-3 text-center font-bold">1</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Sampah pesisir terkumpul</td>
                            <td class="p-3 text-emerald-700 font-bold">372 kg</td>
                            <td class="p-3 font-sans text-slate-600">Tiket timbang/log timbang, foto, serah terima TPS3R</td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold">2</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Peserta Coastal Clean Up</td>
                            <td class="p-3 text-emerald-700 font-bold">33 orang</td>
                            <td class="p-3 font-sans text-slate-600">Daftar hadir, dokumentasi, tanggal dan lokasi</td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold">3</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Anggota Bale Resik</td>
                            <td class="p-3 text-emerald-700 font-bold">12 orang</td>
                            <td class="p-3 font-sans text-slate-600">Daftar anggota, struktur, konfirmasi keaktifan</td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold">4</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Bangunan pengelolaan organik</td>
                            <td class="p-3 text-emerald-700 font-bold">1 unit</td>
                            <td class="p-3 font-sans text-slate-600">Foto bertanggal, lokasi, berita acara, status fungsi</td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold">5</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Komposter</td>
                            <td class="p-3 text-emerald-700 font-bold">3 unit</td>
                            <td class="p-3 font-sans text-slate-600">Inventaris, lokasi, bukti penggunaan</td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold">6</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Mesin pencacah organik</td>
                            <td class="p-3 text-emerald-700 font-bold">1 unit</td>
                            <td class="p-3 font-sans text-slate-600">Inventaris, spesifikasi, status fungsi dan operator</td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold">7</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Fasilitas residu/tungku minim asap</td>
                            <td class="p-3 text-emerald-700 font-bold">1 unit</td>
                            <td class="p-3 font-sans text-slate-600">Spesifikasi, SOP, catatan operasi, pemeriksaan keselamatan</td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold">8</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Tempat sampah terpilah di zona wisata</td>
                            <td class="p-3 text-emerald-700 font-bold">5 set</td>
                            <td class="p-3 font-sans text-slate-600">Daftar lokasi, foto, rencana pengangkutan lanjutan</td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold">9</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Maggot yang dihasilkan</td>
                            <td class="p-3 text-emerald-700 font-bold">17 kg</td>
                            <td class="p-3 font-sans text-slate-600">Catatan produksi, tanggal panen, metode ukur</td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold">10</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Sampah residu diproses</td>
                            <td class="p-3 text-emerald-700 font-bold">Sekitar 400 kg</td>
                            <td class="p-3 font-sans text-slate-600">Log per batch, jenis sampah, timbang, periode, prosedur</td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold">11</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Jangkauan edukasi</td>
                            <td class="p-3 text-emerald-700 font-bold">Sekitar 127 orang</td>
                            <td class="p-3 font-sans text-slate-600">Daftar hadir, sesi, materi, evaluasi</td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold">12</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Keluarga di kawasan permukiman</td>
                            <td class="p-3 text-emerald-700 font-bold">Sekitar 34 KK</td>
                            <td class="p-3 font-sans text-slate-600">Definisi cakupan, data wilayah, bukti keterlibatan</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900">
                <strong class="font-bold uppercase text-[10px]">PENTING:</strong>
                Daftar di atas adalah rekap angka yang dilaporkan. Dokumen ini tidak mengubahnya menjadi angka terverifikasi dan tidak menganggap semua output telah berfungsi atau digunakan secara rutin.
            </div>
        </section>

        <!-- ============================================== -->
        <!-- 6. REKONSILIASI DATA SAMPAH                   -->
        <!-- ============================================== -->
        <section class="space-y-4">
            <h3 class="text-lg font-black text-slate-900 border-l-4 border-emerald-600 pl-3">
                6. Rekonsiliasi Data Sampah dan Metode Pengukuran
            </h3>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed text-justify">
                Informasi yang tersedia menyebutkan <strong>372 kg sampah pesisir</strong>, sekitar <strong>400 kg sampah residu yang diproses</strong>, serta angka total <strong>sekitar 700 kg</strong> yang belum jelas definisi dan cakupannya. Angka-angka tersebut tidak boleh langsung dijumlahkan atau dipresentasikan sebagai satu total program karena dapat merujuk pada kategori, periode, atau alur pengolahan yang berbeda.
            </p>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="p-3 w-1/5">Angka</th>
                            <th class="p-3 w-2/5">Makna Sementara</th>
                            <th class="p-3">Pertanyaan Rekonsiliasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-3 font-bold font-mono-code text-slate-900">372 kg</td>
                            <td class="p-3">Sampah pesisir dikumpulkan dan dibawa ke TPS3R</td>
                            <td class="p-3 text-slate-600">Apakah angka hasil penimbangan? Apakah sampah yang sama kemudian masuk kategori residu?</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold font-mono-code text-slate-900">±400 kg</td>
                            <td class="p-3">Sampah residu dilaporkan diproses melalui tungku minim asap</td>
                            <td class="p-3 text-slate-600">Periode pengukuran kapan? Apakah berasal dari sampah pesisir, sampah harian, atau sumber lain?</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold font-mono-code text-slate-900">±700 kg</td>
                            <td class="p-3">Disebut sebagai total perkiraan</td>
                            <td class="p-3 text-slate-600">Apa definisi totalnya? Apakah termasuk 372 kg dan/atau 400 kg? Apakah ada potensi penghitungan ganda?</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                <strong class="text-slate-900 text-xs font-bold uppercase tracking-wider block">6.1 Protokol rekonsiliasi yang disarankan:</strong>
                <ul class="list-disc pl-5 text-xs text-slate-700 space-y-1">
                    <li>Tetapkan definisi kategori: sampah terkumpul, sampah terangkut, sampah terpilah, sampah organik terolah, residu diproses, dan residu akhir.</li>
                    <li>Catat setiap batch dengan ID unik, tanggal, lokasi asal, kategori, berat bruto/neto, alat timbang, operator, dan tujuan akhir.</li>
                    <li>Tautkan data timbang dengan foto atau bukti serah terima; hindari menjumlahkan dua tahap alur untuk material yang sama.</li>
                    <li>Buat tabel rekonsiliasi masuk–keluar untuk setiap fasilitas dan periode pelaporan.</li>
                    <li>Publikasikan angka total hanya setelah definisi, periode, dan potensi duplikasi disepakati serta diperiksa.</li>
                </ul>
            </div>
        </section>

        <!-- ============================================== -->
        <!-- 7. DIGITALISASI DAN DUKUNGAN EKONOMI LOKAL     -->
        <!-- ============================================== -->
        <section class="space-y-4">
            <h3 class="text-lg font-black text-slate-900 border-l-4 border-emerald-600 pl-3">
                7. Digitalisasi dan Dukungan Ekonomi Lokal
            </h3>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed text-justify">
                Situs <a href="https://purbayani-eco-tourism.siyota.org/" target="_blank" class="text-emerald-700 font-bold underline hover:text-emerald-800">https://purbayani-eco-tourism.siyota.org/</a> dilaporkan menjadi salah satu kanal informasi kawasan. Informasi yang tersedia menyebutkan rata-rata sekitar 300 kunjungan per bulan selama dua bulan terakhir, 83 UMKM, 37 titik GIS, dan 7 destinasi; angka-angka ini harus dikonfirmasi melalui analitik situs dan daftar data yang digunakan. Lima UMKM dilaporkan telah menggunakan QRIS, namun bukti aktivasi dan penggunaan perlu diperiksa.
            </p>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="p-3 w-1/5">Komponen</th>
                            <th class="p-3 w-2/5">Informasi Tersedia</th>
                            <th class="p-3">Bukti / Validasi yang Diperlukan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Website</td>
                            <td class="p-3 font-mono-code text-emerald-700">purbayani-eco-tourism.siyota.org</td>
                            <td class="p-3 text-slate-600">Tautan aktif, tanggal pembaruan, pemilik/pengelola, log pemeliharaan</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Kunjungan web</td>
                            <td class="p-3">±300 kunjungan/bulan selama dua bulan terakhir (perkiraan)</td>
                            <td class="p-3 text-slate-600">Ekspor analytics, definisi metrik, rentang tanggal, filter bot/internal</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Data UMKM</td>
                            <td class="p-3">83 UMKM dilaporkan tercatat</td>
                            <td class="p-3 text-slate-600">Daftar unik, tanggal pemutakhiran, persetujuan publikasi kontak</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Peta GIS</td>
                            <td class="p-3">37 titik dilaporkan</td>
                            <td class="p-3 text-slate-600">Daftar koordinat, kategori titik, validasi lapangan dan izin publikasi</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Destinasi</td>
                            <td class="p-3">7 destinasi dilaporkan</td>
                            <td class="p-3 text-slate-600">Daftar destinasi, konfirmasi pengelola, status informasi</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">QRIS</td>
                            <td class="p-3">5 UMKM dilaporkan menggunakan QRIS</td>
                            <td class="p-3 text-slate-600">Konfirmasi merchant, bukti aktivasi atau transaksi agregat dengan persetujuan</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-100 border border-slate-200 text-xs text-slate-700">
                <strong class="text-slate-900 font-bold uppercase text-[10px]">PRINSIP PRIVASI:</strong>
                Data publik harus dibatasi pada informasi yang disetujui untuk dipublikasikan. Hindari menampilkan nomor pribadi, data transaksi individual, atau koordinat sensitif tanpa persetujuan dan alasan yang jelas.
            </div>
        </section>

        <!-- ============================================== -->
        <!-- 8. KEMITRAAN, TATA KELOLA, DAN PERAN           -->
        <!-- ============================================== -->
        <section class="space-y-4">
            <h3 class="text-lg font-black text-slate-900 border-l-4 border-emerald-600 pl-3">
                8. Kemitraan, Tata Kelola, dan Peran
            </h3>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed text-justify">
                Informasi yang tersedia menyebut Bale Resik, Yayasan Yota, Dinas Lingkungan Hidup (DLH), UMKM, serta pelaku usaha/pariwisata sebagai pihak yang relevan dalam ekosistem program. Penyebutan pihak dalam laporan ini tidak otomatis berarti adanya perjanjian formal atau komitmen pendanaan. Status masing-masing peran perlu dikonfirmasi melalui dokumen atau persetujuan tertulis.
            </p>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="p-3 w-1/4">Pihak / Aktor</th>
                            <th class="p-3 w-1/2">Peran yang Relevan untuk Dikonfirmasi</th>
                            <th class="p-3">Dokumen Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Bale Resik</td>
                            <td class="p-3">Operasional fasilitas, pemilahan/pengolahan, pencatatan dan pemeliharaan</td>
                            <td class="p-3 text-slate-600">Daftar anggota, SK/struktur, SOP, log operasi</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Yayasan Yota</td>
                            <td class="p-3">Pendampingan desa binaan, koordinasi, penguatan kapasitas dan monitoring</td>
                            <td class="p-3 text-slate-600">Rencana pendampingan, laporan kegiatan, pembagian peran</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">DLH</td>
                            <td class="p-3">Arahan teknis, koordinasi sistem persampahan, rujukan pengelolaan yang sesuai</td>
                            <td class="p-3 text-slate-600">Surat, notulen, rekomendasi atau catatan konsultasi</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">UMKM dan pelaku wisata</td>
                            <td class="p-3">Penerapan pemilahan, penggunaan kanal digital, potensi layanan persampahan</td>
                            <td class="p-3 text-slate-600">Daftar mitra, kesepakatan, bukti aktivitas</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Hotel/penginapan/vila/usaha lokal</td>
                            <td class="p-3">Potensi pelanggan layanan pengelolaan sampah berbasis B2B</td>
                            <td class="p-3 text-slate-600">Pemetaan kebutuhan, uji minat, proposal/kontrak bila ada</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ============================================== -->
        <!-- 9. TINJAUAN KEUANGAN DAN REKONSILIASI ANGGARAN -->
        <!-- ============================================== -->
        <section class="space-y-4">
            <h3 class="text-lg font-black text-slate-900 border-l-4 border-emerald-600 pl-3">
                9. Tinjauan Keuangan dan Rekonsiliasi Anggaran
            </h3>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed text-justify">
                Total anggaran yang dilaporkan adalah <strong>Rp15.607.041</strong>. Informasi pembiayaan yang tersedia menyebut Rp8.500.000 untuk biaya hidup/kit kelompok dan kontribusi tambahan anggota sebesar Rp856.000. Jika dua komponen tersebut merupakan bagian dari total anggaran yang sama, selisih aritmetisnya adalah <strong>Rp6.251.041</strong>. Selisih ini hanya rekonstruksi hitung berdasarkan angka yang tersedia, bukan angka sumber dana mitra yang telah diverifikasi.
            </p>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="p-3 w-1/3">Komponen</th>
                            <th class="p-3 w-1/4">Nilai</th>
                            <th class="p-3">Status dan Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono-code">
                        <tr>
                            <td class="p-3 font-sans font-bold text-slate-900">Total anggaran dilaporkan</td>
                            <td class="p-3 text-slate-900 font-bold">Rp15.607.041</td>
                            <td class="p-3 font-sans text-slate-600">Perlu dicocokkan dengan RAB final dan buku kas</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-sans font-medium text-slate-900">Biaya hidup/kit kelompok (disebutkan)</td>
                            <td class="p-3 text-blue-700">Rp8.500.000</td>
                            <td class="p-3 font-sans text-slate-600">Perlu rincian item, bukti transaksi, dan dasar klasifikasi</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-sans font-medium text-slate-900">Kontribusi tambahan anggota</td>
                            <td class="p-3 text-emerald-700">Rp856.000</td>
                            <td class="p-3 font-sans text-slate-600">Perlu daftar penyetor, tanggal, bukti, dan perlakuan akuntansi</td>
                        </tr>
                        <tr class="bg-slate-50 font-bold">
                            <td class="p-3 font-sans text-slate-900">Selisih aritmetis (Pengadaan Aset & Fasilitas)</td>
                            <td class="p-3 text-amber-800">Rp6.251.041</td>
                            <td class="p-3 font-sans text-slate-600">Hasil hitung: total dikurangi dua komponen di atas; bukan sumber dana terverifikasi</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                <strong class="text-slate-900 text-xs font-bold uppercase tracking-wider block">9.1 Dokumen keuangan yang perlu disiapkan:</strong>
                <ul class="list-disc pl-5 text-xs text-slate-700 space-y-1">
                    <li>RAB awal dan revisi yang disetujui, termasuk dasar perubahan jika ada.</li>
                    <li>Buku kas masuk dan keluar, dengan tanggal, uraian, kode akun, penerima, dan penanggung jawab.</li>
                    <li>Kuitansi, invoice, bukti transfer, nota, daftar pembayaran, dan bukti penerimaan barang.</li>
                    <li>Rekonsiliasi sumber dana: pendanaan program, kontribusi anggota, kontribusi mitra, barang/jasa in-kind, dan sumber lain.</li>
                    <li>Rekonsiliasi realisasi terhadap anggaran dan penjelasan atas selisih.</li>
                    <li>Daftar aset yang dibeli/dibangun beserta nilai, pemilik, lokasi, dan penanggung jawab pemeliharaan.</li>
                </ul>
            </div>

            <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-900">
                <strong class="font-bold uppercase text-[10px]">BATASAN AUDIT:</strong>
                Laporan ini tidak memberikan opini audit, tidak menyatakan transaksi telah diuji, dan tidak menyimpulkan kepatuhan keuangan. Penilaian tersebut memerlukan dokumen primer dan prosedur pemeriksaan yang memadai.
            </div>
        </section>

        <!-- ============================================== -->
        <!-- 10. RISIKO, SAFEGUARDS, DAN PEMBELAJARAN       -->
        <!-- ============================================== -->
        <section class="space-y-4">
            <h3 class="text-lg font-black text-slate-900 border-l-4 border-emerald-600 pl-3">
                10. Risiko, Safeguards, dan Pembelajaran
            </h3>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="p-3 w-1/4">Risiko / Isu</th>
                            <th class="p-3 w-1/3">Mengapa Penting</th>
                            <th class="p-3">Tindakan Mitigasi yang Disarankan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Penghitungan ganda massa sampah</td>
                            <td class="p-3 text-slate-600">Material yang sama dapat tercatat pada tahap pengumpulan dan pengolahan</td>
                            <td class="p-3 text-slate-800">Gunakan ID batch dan rekonsiliasi masuk–keluar</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Keselamatan tungku minim asap</td>
                            <td class="p-3 text-slate-600">Operasi dapat melibatkan panas, asap, emisi, dan residu berbahaya</td>
                            <td class="p-3 text-slate-800">Tinjauan teknis, SOP, pelatihan operator, APD, log operasi, evaluasi kepatuhan oleh pihak berwenang</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Fasilitas tidak digunakan rutin</td>
                            <td class="p-3 text-slate-600">Aset tidak otomatis menghasilkan layanan berkelanjutan</td>
                            <td class="p-3 text-slate-800">Tetapkan operator, jadwal, biaya operasi, indikator penggunaan dan perawatan</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Tata kelola kelompok belum formal</td>
                            <td class="p-3 text-slate-600">Tanggung jawab dan akuntabilitas dapat tidak jelas</td>
                            <td class="p-3 text-slate-800">Susun struktur, SK bila relevan, SOP, mekanisme keputusan dan pencatatan</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Data digital tidak mutakhir</td>
                            <td class="p-3 text-slate-600">Informasi publik dapat salah atau menyesatkan pengguna</td>
                            <td class="p-3 text-slate-800">Tetapkan pemilik data, tanggal pembaruan, kanal koreksi dan pemeriksaan berkala</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Privasi UMKM/masyarakat</td>
                            <td class="p-3 text-slate-600">Publikasi informasi dapat menimbulkan risiko bagi pemilik data</td>
                            <td class="p-3 text-slate-800">Dapatkan persetujuan, batasi data pribadi, dokumentasikan dasar publikasi</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Keberlanjutan pembiayaan</td>
                            <td class="p-3 text-slate-600">Biaya perawatan dan operasional dapat tidak tertutup setelah program</td>
                            <td class="p-3 text-slate-800">Hitung biaya bulanan, uji permintaan layanan, dan buat rencana pendapatan/dukungan</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs text-amber-950">
                <strong class="font-bold text-amber-900 uppercase block mb-1">Catatan Keselamatan Khusus Tungku Minim Asap:</strong>
                Khusus untuk tungku minim asap, istilah “minim asap” merupakan deskripsi yang dilaporkan dan tidak dapat dianggap sebagai bukti bahwa emisi aman atau fasilitas telah memenuhi seluruh ketentuan teknis. Klaim tersebut perlu didukung spesifikasi, prosedur operasi, catatan bahan yang diproses, pengendalian emisi, dan penilaian pihak berwenang/kompeten.
            </div>
        </section>

        <!-- ============================================== -->
        <!-- 11. KERANGKA PEMANTAUAN HASIL (KPI)           -->
        <!-- ============================================== -->
        <section class="space-y-4">
            <h3 class="text-lg font-black text-slate-900 border-l-4 border-emerald-600 pl-3">
                11. Kerangka Pemantauan Hasil (KPI)
            </h3>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed text-justify">
                Tabel berikut merupakan rancangan kerangka pemantauan untuk periode lanjutan. Nilai target belum ditetapkan karena baseline dan kapasitas operasi belum tersedia secara memadai.
            </p>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="p-3 w-1/6">Area</th>
                            <th class="p-3">Indikator</th>
                            <th class="p-3 w-1/5">Frekuensi</th>
                            <th class="p-3 w-1/4">Sumber Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Sampah</td>
                            <td class="p-3">Kg per kategori, sumber, dan tujuan akhir; persentase data dengan bukti timbang</td>
                            <td class="p-3 text-slate-600">Per batch/bulanan</td>
                            <td class="p-3 font-mono-code text-[11px]">Log timbang, bukti serah terima, rekonsiliasi</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Fasilitas</td>
                            <td class="p-3">Jumlah aset berfungsi; frekuensi penggunaan; downtime; biaya perawatan</td>
                            <td class="p-3 text-slate-600">Bulanan</td>
                            <td class="p-3 font-mono-code text-[11px]">Checklist inspeksi, log operator, bukti perawatan</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Kelompok</td>
                            <td class="p-3">Kehadiran anggota aktif; rapat; kegiatan rutin; kelengkapan pembukuan</td>
                            <td class="p-3 text-slate-600">Bulanan</td>
                            <td class="p-3 font-mono-code text-[11px]">Daftar hadir, notulen, buku kas</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Edukasi</td>
                            <td class="p-3">Peserta unik, jumlah sesi, tingkat penyelesaian, perubahan pengetahuan</td>
                            <td class="p-3 text-slate-600">Setiap sesi/triwulan</td>
                            <td class="p-3 font-mono-code text-[11px]">Daftar hadir, pre/post test, evaluasi</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Digital</td>
                            <td class="p-3">Pengguna/kunjungan menurut definisi yang konsisten; data yang diperbarui; rujukan ke UMKM</td>
                            <td class="p-3 text-slate-600">Bulanan</td>
                            <td class="p-3 font-mono-code text-[11px]">Ekspor analytics, log pembaruan, survei</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">QRIS/UMKM</td>
                            <td class="p-3">Jumlah merchant aktif dan transaksi agregat yang disetujui untuk dibagikan</td>
                            <td class="p-3 text-slate-600">Bulanan/triwulan</td>
                            <td class="p-3 font-mono-code text-[11px]">Konfirmasi merchant, laporan agregat</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Keuangan</td>
                            <td class="p-3">Realisasi anggaran, biaya operasi, pendapatan layanan, saldo kas</td>
                            <td class="p-3 text-slate-600">Bulanan</td>
                            <td class="p-3 font-mono-code text-[11px]">Buku kas, rekening, kuitansi</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">Safeguards</td>
                            <td class="p-3">Insiden, kepatuhan SOP, pelatihan operator, tindak lanjut keluhan</td>
                            <td class="p-3 text-slate-600">Bulanan/insidental</td>
                            <td class="p-3 font-mono-code text-[11px]">Log insiden, checklist, catatan tindak lanjut</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ============================================== -->
        <!-- 12. PETA JALAN KEBERLANJUTAN                  -->
        <!-- ============================================== -->
        <section class="space-y-4">
            <h3 class="text-lg font-black text-slate-900 border-l-4 border-emerald-600 pl-3">
                12. Peta Jalan Keberlanjutan
            </h3>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="p-3 w-1/5">Tahap</th>
                            <th class="p-3 w-1/4">Fokus</th>
                            <th class="p-3">Langkah Prioritas</th>
                            <th class="p-3 w-1/4">Bukti Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-3 font-bold text-slate-900">0–30 hari</td>
                            <td class="p-3 text-emerald-800 font-bold">Verifikasi dan stabilisasi</td>
                            <td class="p-3">Rekonsiliasi angka sampah dan keuangan; inventaris aset; cek fungsi fasilitas; rapikan bukti kegiatan</td>
                            <td class="p-3 font-mono-code text-[11px]">Register bukti, tabel rekonsiliasi, daftar aset</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">31–90 hari</td>
                            <td class="p-3 text-emerald-800 font-bold">Tata kelola dan SOP</td>
                            <td class="p-3">Konfirmasi pengurus Bale Resik; tetapkan peran; siapkan SOP pengumpulan, pemilahan, operasi, keselamatan, dan pencatatan</td>
                            <td class="p-3 font-mono-code text-[11px]">SK/struktur bila relevan, SOP, jadwal operasi</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">3–6 bulan</td>
                            <td class="p-3 text-emerald-800 font-bold">Uji model layanan</td>
                            <td class="p-3">Pemetaan kebutuhan usaha lokal; konsultasi DLH; hitung biaya layanan; uji minat B2B tanpa mengklaim kontrak sebelum ada kesepakatan</td>
                            <td class="p-3 font-mono-code text-[11px]">Studi biaya, hasil konsultasi, bukti uji minat</td>
                        </tr>
                        <tr>
                            <td class="p-3 font-bold text-slate-900">6–12 bulan</td>
                            <td class="p-3 text-emerald-800 font-bold">Monitoring dan pembiayaan</td>
                            <td class="p-3">Tinjau KPI triwulanan; rencana perawatan; laporan keuangan ringkas; perbarui data digital dan daftar mitra</td>
                            <td class="p-3 font-mono-code text-[11px]">Dashboard KPI, laporan triwulan, rencana pembiayaan</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-slate-700 leading-relaxed text-justify">
                Rekomendasi yang telah muncul dalam informasi program meliputi penguatan status desa binaan Yayasan Yota, koordinasi dengan DLH, pertimbangan Peraturan Desa (Perdes) yang sesuai konteks dan kewenangan, penerbitan SK kelompok pengelola bila relevan, serta penjajakan layanan pengelolaan sampah untuk hotel, penginapan, vila, dan pelaku usaha lainnya. Semua hal tersebut diperlakukan sebagai agenda/rekomendasi sampai ada bukti keputusan atau perjanjian formal.
            </p>
        </section>

        <!-- ============================================== -->
        <!-- 13. KESIMPULAN DAN REKOMENDASI PRIORITAS       -->
        <!-- ============================================== -->
        <section class="space-y-4">
            <h3 class="text-lg font-black text-slate-900 border-l-4 border-emerald-600 pl-3">
                13. Kesimpulan dan Rekomendasi Prioritas
            </h3>
            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed text-justify">
                Informasi yang tersedia menunjukkan bahwa CAP 2026 telah melaksanakan berbagai aktivitas dan melaporkan keluaran pada aspek kebersihan pesisir, sarana pengelolaan sampah, keterlibatan komunitas, edukasi, dan digitalisasi. Agar laporan dapat digunakan untuk pertanggungjawaban publik dan pengambilan keputusan, langkah selanjutnya bukan sekadar memperbanyak klaim hasil, melainkan memastikan setiap angka dapat ditelusuri dan setiap fasilitas memiliki tata kelola operasional.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                    <strong class="text-emerald-700 block font-bold mb-1">Prioritas 1 — Selesaikan rekonsiliasi data sampah</strong>
                    <p class="text-slate-600">Jelaskan hubungan antara 372 kg, sekitar 400 kg, dan estimasi total sekitar 700 kg; hindari penjumlahan sebelum alur material dipastikan.</p>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                    <strong class="text-emerald-700 block font-bold mb-1">Prioritas 2 — Lengkapi berkas keuangan</strong>
                    <p class="text-slate-600">Cocokkan total Rp15.607.041 dengan RAB, buku kas, bukti transaksi, kontribusi anggota, dan sumber dana mitra.</p>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                    <strong class="text-emerald-700 block font-bold mb-1">Prioritas 3 — Verifikasi keluaran fisik</strong>
                    <p class="text-slate-600">Lakukan pemeriksaan inventaris, kondisi, lokasi, operator, fungsi, dan kebutuhan pemeliharaan fasilitas.</p>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                    <strong class="text-emerald-700 block font-bold mb-1">Prioritas 4 — Terapkan safeguards</strong>
                    <p class="text-slate-600">Pastikan pengoperasian fasilitas residu memiliki SOP, pelatihan, APD, pencatatan, dan penilaian teknis yang sesuai.</p>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                    <strong class="text-emerald-700 block font-bold mb-1">Prioritas 5 — Formalisasi tata kelola</strong>
                    <p class="text-slate-600">Tetapkan struktur, pembagian peran, mekanisme pengambilan keputusan, dan pencatatan kelompok pengelola Bale Resik.</p>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
                    <strong class="text-emerald-700 block font-bold mb-1">Prioritas 6 — Verifikasi digitalisasi</strong>
                    <p class="text-slate-600">Simpan ekspor analytics, validasi daftar UMKM/GIS/destinasi, dan dokumentasikan penggunaan QRIS dengan persetujuan.</p>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs sm:col-span-2">
                    <strong class="text-emerald-700 block font-bold mb-1">Prioritas 7 — Jalankan monitoring triwulanan</strong>
                    <p class="text-slate-600">Gunakan indikator yang konsisten dan pisahkan output, outcome, target, dan dampak.</p>
                </div>
            </div>
        </section>

        <!-- ============================================== -->
        <!-- LAMPIRAN A: REGISTER BUKTI & STATUS VERIFIKASI -->
        <!-- ============================================== -->
        <section class="space-y-4 pt-6 border-t border-slate-200">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-mono uppercase tracking-widest text-emerald-700 font-bold block">Dokumen Pelengkap</span>
                    <h3 class="text-lg font-black text-slate-900">
                        Lampiran A. Register Bukti dan Status Verifikasi (E-01 s/d E-15)
                    </h3>
                </div>
                <span class="text-xs bg-slate-100 text-slate-700 font-bold px-3 py-1 rounded-full">15 Item Terdaftar</span>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px]">
                        <tr>
                            <th class="p-3 w-16 text-center">ID</th>
                            <th class="p-3 w-1/4">Klaim / Data</th>
                            <th class="p-3">Bukti yang Diharapkan</th>
                            <th class="p-3 w-1/4">Status Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono-code">
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-900">E-01</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Periode 5 Juli–5 Agustus 2026</td>
                            <td class="p-3 font-sans text-slate-600">Rencana kerja/laporan kegiatan bertanggal</td>
                            <td class="p-3 font-sans"><span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Perlu dicocokkan</span></td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-900">E-02</td>
                            <td class="p-3 font-sans font-medium text-slate-900">372 kg sampah pesisir</td>
                            <td class="p-3 font-sans text-slate-600">Tiket timbang/log timbang dan bukti TPS3R</td>
                            <td class="p-3 font-sans"><span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Belum ditautkan ke bukti dalam dokumen ini</span></td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-900">E-03</td>
                            <td class="p-3 font-sans font-medium text-slate-900">33 peserta aksi bersih</td>
                            <td class="p-3 font-sans text-slate-600">Daftar hadir dan dokumentasi</td>
                            <td class="p-3 font-sans"><span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Belum ditautkan ke bukti</span></td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-900">E-04</td>
                            <td class="p-3 font-sans font-medium text-slate-900">12 anggota Bale Resik</td>
                            <td class="p-3 font-sans text-slate-600">Daftar anggota dan konfirmasi pengurus</td>
                            <td class="p-3 font-sans"><span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Perlu verifikasi</span></td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-900">E-05</td>
                            <td class="p-3 font-sans font-medium text-slate-900">127 peserta edukasi</td>
                            <td class="p-3 font-sans text-slate-600">Daftar hadir per sesi dan rekap peserta unik</td>
                            <td class="p-3 font-sans"><span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Perkiraan; perlu rincian</span></td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-900">E-06</td>
                            <td class="p-3 font-sans font-medium text-slate-900">34 KK</td>
                            <td class="p-3 font-sans text-slate-600">Definisi cakupan dan daftar agregat/wilayah</td>
                            <td class="p-3 font-sans"><span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Perkiraan; perlu verifikasi</span></td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-900">E-07</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Fasilitas dan aset</td>
                            <td class="p-3 font-sans text-slate-600">Inventaris, foto, berita acara, status fungsi</td>
                            <td class="p-3 font-sans"><span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Perlu inspeksi</span></td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-900">E-08</td>
                            <td class="p-3 font-sans font-medium text-slate-900">17 kg maggot</td>
                            <td class="p-3 font-sans text-slate-600">Catatan produksi dan metode ukur</td>
                            <td class="p-3 font-sans"><span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Perlu bukti primer</span></td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-900">E-09</td>
                            <td class="p-3 font-sans font-medium text-slate-900">±400 kg residu diproses</td>
                            <td class="p-3 font-sans text-slate-600">Log batch, timbang, periode, jenis sampah</td>
                            <td class="p-3 font-sans"><span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Perlu rekonsiliasi</span></td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-900">E-10</td>
                            <td class="p-3 font-sans font-medium text-slate-900">±700 kg total</td>
                            <td class="p-3 font-sans text-slate-600">Definisi, periode, alur material, rekonsiliasi</td>
                            <td class="p-3 font-sans"><span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Ambigu; jangan dipublikasikan sebagai total final</span></td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-900">E-11</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Rp15.607.041</td>
                            <td class="p-3 font-sans text-slate-600">RAB, buku kas, bukti transaksi</td>
                            <td class="p-3 font-sans"><span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Belum tervalidasi secara audit</span></td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-900">E-12</td>
                            <td class="p-3 font-sans font-medium text-slate-900">±300 kunjungan web/bulan</td>
                            <td class="p-3 font-sans text-slate-600">Ekspor analytics dan definisi metrik</td>
                            <td class="p-3 font-sans"><span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Estimasi; perlu verifikasi</span></td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-900">E-13</td>
                            <td class="p-3 font-sans font-medium text-slate-900">83 UMKM, 37 titik GIS, 7 destinasi</td>
                            <td class="p-3 font-sans text-slate-600">Ekspor data/listing bertanggal</td>
                            <td class="p-3 font-sans"><span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Perlu validasi</span></td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-900">E-14</td>
                            <td class="p-3 font-sans font-medium text-slate-900">5 UMKM menggunakan QRIS</td>
                            <td class="p-3 font-sans text-slate-600">Konfirmasi merchant/bukti aktivasi yang disetujui</td>
                            <td class="p-3 font-sans"><span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Perlu verifikasi</span></td>
                        </tr>
                        <tr>
                            <td class="p-3 text-center font-bold text-slate-900">E-15</td>
                            <td class="p-3 font-sans font-medium text-slate-900">Kemitraan/komitmen</td>
                            <td class="p-3 font-sans text-slate-600">Surat, MoU, notulen, kontrak atau persetujuan tertulis</td>
                            <td class="p-3 font-sans"><span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded-full">Bedakan hubungan berjalan dan usulan</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ============================================== -->
        <!-- LAMPIRAN B & LAMPIRAN C                        -->
        <!-- ============================================== -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-200">
            <!-- Lampiran B -->
            <div class="space-y-3">
                <h3 class="text-base font-black text-slate-900">
                    Lampiran B. Dokumen yang Disarankan untuk Paket Audit
                </h3>
                <ul class="list-decimal pl-5 text-xs text-slate-700 space-y-1.5 leading-relaxed">
                    <li>Laporan kegiatan harian/logbook beserta foto dan tanggal.</li>
                    <li>Daftar hadir seluruh kegiatan dan rekap peserta unik.</li>
                    <li>Tiket timbang, log pengangkutan, bukti serah terima TPS3R, dan catatan pengolahan residu.</li>
                    <li>RAB yang disetujui, bukti transfer, invoice, kuitansi, buku kas, dan rekonsiliasi kontribusi.</li>
                    <li>Daftar aset dan berita acara serah terima atau kepemilikan.</li>
                    <li>SOP fasilitas, catatan operasi, checklist keselamatan, pelatihan operator, dan catatan insiden.</li>
                    <li>Daftar anggota Bale Resik, struktur pengurus, jadwal kerja, notulen, dan SK bila ada.</li>
                    <li>Daftar UMKM/destinasi/GIS yang telah diverifikasi dan izin publikasi data.</li>
                    <li>Ekspor analytics website, dokumentasi pembaruan, dan bukti aktivasi QRIS yang tidak membuka data pribadi.</li>
                    <li>Surat, notulen, rekomendasi, atau kesepakatan dengan DLH, pemerintah desa, yayasan, serta pelaku usaha.</li>
                    <li>Baseline/endline atau survei sederhana untuk menilai perubahan perilaku dan penggunaan fasilitas.</li>
                </ul>
            </div>

            <!-- Lampiran C -->
            <div class="space-y-3">
                <h3 class="text-base font-black text-slate-900">
                    Lampiran C. Catatan Istilah Pelaporan
                </h3>
                <div class="overflow-x-auto rounded-2xl border border-slate-200">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-100 text-slate-700 uppercase font-bold text-[10px]">
                            <tr>
                                <th class="p-2.5 w-1/3">Istilah</th>
                                <th class="p-2.5">Penggunaan yang Disarankan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-[11px]">
                            <tr>
                                <td class="p-2.5 font-bold text-slate-900">Dilaporkan</td>
                                <td class="p-2.5 text-slate-600">Angka/kejadian disebutkan oleh sumber program, tetapi belum tentu diperiksa secara independen.</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-bold text-slate-900">Terverifikasi</td>
                                <td class="p-2.5 text-slate-600">Memiliki bukti primer yang ditelaah dan direkonsiliasi sesuai prosedur yang terdokumentasi.</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-bold text-slate-900">Output</td>
                                <td class="p-2.5 text-slate-600">Produk langsung kegiatan, seperti peserta, fasilitas, atau kilogram yang dikumpulkan.</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-bold text-slate-900">Outcome</td>
                                <td class="p-2.5 text-slate-600">Perubahan penggunaan, perilaku, akses, atau kapasitas yang perlu dibuktikan dari waktu ke waktu.</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-bold text-slate-900">Dampak</td>
                                <td class="p-2.5 text-slate-600">Perubahan jangka panjang yang membutuhkan baseline, metode evaluasi, dan bukti yang memadai.</td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-bold text-slate-900">Target / rekomendasi</td>
                                <td class="p-2.5 text-slate-600">Rencana ke depan yang belum boleh disajikan sebagai hasil yang telah dicapai.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ============================================== -->
        <!-- PENUTUP                                       -->
        <!-- ============================================== -->
        <footer class="pt-6 border-t border-slate-200 text-xs text-slate-500 leading-relaxed text-justify space-y-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <strong class="text-slate-900 font-bold uppercase text-[10px] block mb-1">PENUTUP:</strong>
                Sebelum diterbitkan sebagai laporan publik final, lengkapi nomor dokumen, nama penanggung jawab, daftar sumber bukti, tanda persetujuan internal, dan lampiran primer. Hapus atau revisi klaim yang tidak dapat didukung oleh bukti yang tersedia.
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-400 font-mono gap-2 pt-2">
                <span>Collaborative Action Program (CAP) 2026 &bull; Desa Purbayani</span>
                <span>Dokumen Kerja Versi 1.0 &bull; Dihasilkan Otomatis oleh Yota Execution OS</span>
            </div>
        </footer>

    </main>

</body>
</html>
