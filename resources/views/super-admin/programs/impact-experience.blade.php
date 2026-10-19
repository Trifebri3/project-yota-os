<x-app-layout>
    <div class="space-y-8 animate-fade-in pb-12">
        <!-- Top Breadcrumb & Action Header -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 font-bold mb-1">
                    <a href="{{ route('super-admin.programs.index') }}" class="hover:text-emerald-700 transition">&larr; Daftar Program</a>
                    <span>/</span>
                    <span class="text-slate-700">Yota Adiwidya Center</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Kelola Identitas Publik, KPI Grafis & Before-After
                </h1>
                <p class="text-xs text-slate-500 mt-1">
                    Program: <strong class="text-slate-800">{{ $program->name }}</strong>
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('public.projects.evidence-report', $program->id) }}" target="_blank" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-1.5">
                    <span>📑 Laporan & Tinjauan Bukti (v1.0) &rarr;</span>
                </a>
                <a href="{{ route('public.projects.show', $program->id) }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-1.5">
                    <span>Lihat Halaman Publik (Nuansa Putih) &rarr;</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-xs space-y-1">
                <strong>Terjadi kesalahan input:</strong>
                <ul class="list-disc pl-4">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- ============================================== -->
        <!-- SECTION 1: IDENTITAS PROGRAM & UPLOAD MEDIA   -->
        <!-- ============================================== -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <span class="bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase px-2.5 py-1 rounded-md">
                    Identitas & Banner Publik
                </span>
                <h2 class="text-lg font-black text-slate-900 mt-2">
                    Foto Banner, Cover, Logo & Ringkasan Program
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Perubahan data dan file foto di bawah akan langsung tampil pada tampilan public experience Yota Adiwidya Center.
                </p>
            </div>

            <form action="{{ route('super-admin.programs.impact.identity', $program->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Media Upload Previews Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Banner Program Upload -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Foto Banner Program</label>
                        <div class="aspect-video w-full rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 relative group shadow-sm">
                            <img src="{{ $program->banner_program ?? '/images/projects/mangrove.jpg' }}" alt="Banner" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center p-3 text-center text-white text-xs font-bold">
                                Ganti Banner Utama
                            </div>
                        </div>
                        <input type="file" name="banner_program" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                    </div>

                    <!-- Cover Image Upload -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Foto Cover / Tapak</label>
                        <div class="aspect-video w-full rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 relative group shadow-sm">
                            <img src="{{ $program->cover_image ?? '/images/projects/mangrove.jpg' }}" alt="Cover" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center p-3 text-center text-white text-xs font-bold">
                                Ganti Cover Tapak
                            </div>
                        </div>
                        <input type="file" name="cover_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                    </div>

                    <!-- Logo Program Upload -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Logo Program / Inisiatif</label>
                        <div class="aspect-video w-full rounded-2xl overflow-hidden bg-slate-50 border border-slate-200 flex items-center justify-center p-4 relative group shadow-sm">
                            @if($program->logo_program)
                                <img src="{{ $program->logo_program }}" alt="Logo" class="max-h-20 object-contain">
                            @else
                                <div class="w-16 h-16 rounded-2xl bg-emerald-100 text-emerald-800 font-black text-2xl flex items-center justify-center">
                                    {{ substr($program->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <input type="file" name="logo_program" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                    </div>
                </div>

                <!-- Text Fields Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Nama Program</label>
                        <input type="text" name="name" value="{{ old('name', $program->name) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white font-medium text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kategori Program</label>
                        <input type="text" name="kategori_program" value="{{ old('kategori_program', $program->kategori_program) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white font-medium text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Mitra Kerjasama</label>
                        <input type="text" name="mitra" value="{{ old('mitra', $program->mitra) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white font-medium text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Sponsor / Donatur</label>
                        <input type="text" name="sponsor" value="{{ old('sponsor', $program->sponsor) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white font-medium text-slate-900 focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Deskripsi Singkat Program</label>
                        <textarea name="description" rows="3" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white font-medium text-slate-900 focus:ring-2 focus:ring-emerald-500">{{ old('description', $program->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Visi Program (Project Charter)</label>
                        <textarea name="vision" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white font-medium text-slate-900">{{ old('vision', $program->projectCharter?->vision) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Scope / Wilayah Tapak</label>
                        <textarea name="scope" rows="2" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white font-medium text-slate-900">{{ old('scope', $program->projectCharter?->scope) }}</textarea>
                    </div>
                </div>

                <!-- Stats Macro Inputs -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Reduksi Karbon (Kg CO₂e)</label>
                        <input type="number" step="0.1" name="carbon_offset" value="{{ $program->stats['carbon_offset'] ?? 0 }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-emerald-700">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Penerima Manfaat (Jiwa)</label>
                        <input type="number" name="beneficiaries_count" value="{{ $program->stats['beneficiaries_count'] ?? 0 }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Relawan Lapangan (Orang)</label>
                        <input type="number" name="volunteers_count" value="{{ $program->stats['volunteers_count'] ?? 0 }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-900">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-sm transition">
                        Simpan Identitas & Dokumen Media
                    </button>
                </div>
            </form>
        </div>

        <!-- ============================================== -->
        <!-- SECTION 2: MANAJEMEN INDIKATOR KPI & GRAFIS   -->
        <!-- ============================================== -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div>
                    <span class="bg-amber-50 text-amber-700 text-[10px] font-black uppercase px-2.5 py-1 rounded-md">
                        Indikator & Grafis KPI
                    </span>
                    <h2 class="text-lg font-black text-slate-900 mt-2">
                        Daftar KPI Ketercapaian & Unggah Gambar Grafis
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Setiap KPI dapat memiliki target, realisasi, persentase grafis donut, serta unggahan gambar/infografis pendukung.
                    </p>
                </div>

                <button onclick="document.getElementById('addKpiForm').classList.toggle('hidden')" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm">
                    + Tambah Indikator KPI
                </button>
            </div>

            <!-- Form Tambah KPI (Collapsible) -->
            <form id="addKpiForm" action="{{ route('super-admin.programs.kpi.store', $program->id) }}" method="POST" enctype="multipart/form-data" class="hidden bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-4 text-xs">
                @csrf
                <strong class="block text-sm font-black text-slate-900">Form Tambah KPI Baru</strong>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Kode KPI</label>
                        <input type="text" name="code" placeholder="e.g. KPI-ENV-01" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Nama Indikator</label>
                        <input type="text" name="name" required placeholder="e.g. Bibit Mangrove Tertanam" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Kategori</label>
                        <input type="text" name="category" placeholder="e.g. Lingkungan / Sampah" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Target Angka</label>
                        <input type="number" step="0.01" name="target" required placeholder="2500" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Realisasi Aktual</label>
                        <input type="number" step="0.01" name="actual" required placeholder="2500" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-emerald-700">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Satuan</label>
                        <input type="text" name="unit" required placeholder="Pohon / Kg / Jiwa" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Definisi & Pengukuran</label>
                        <input type="text" name="definition" placeholder="Metode sensus fisik..." class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Verifikator</label>
                        <input type="text" name="verified_by" placeholder="e.g. DLH Jabar" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Upload Gambar Grafis / Infografis Pendukung (Opsional)</label>
                        <input type="file" name="graphic_image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-800 cursor-pointer">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('addKpiForm').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-700 font-bold text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm">Simpan KPI</button>
                </div>
            </form>

            <!-- Table of Existing KPIs -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 uppercase text-[10px] font-black border-y border-slate-200">
                            <th class="p-3">Kode & Nama</th>
                            <th class="p-3">Grafis / Gambar</th>
                            <th class="p-3">Target vs Realisasi</th>
                            <th class="p-3">Capaian</th>
                            <th class="p-3">Verifikator</th>
                            <th class="p-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($program->kpiIndicators as $kpi)
                            @php
                                $pct = $kpi->target > 0 ? min(100, round(($kpi->actual / $kpi->target) * 100)) : 100;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-3">
                                    <span class="font-mono text-[10px] bg-slate-100 text-slate-700 px-1.5 py-0.5 rounded font-bold">{{ $kpi->code ?? 'KPI' }}</span>
                                    <strong class="block text-slate-900 mt-1">{{ $kpi->name }}</strong>
                                    <span class="text-[10px] text-slate-400">{{ $kpi->definition }}</span>
                                </td>
                                <td class="p-3">
                                    @if($kpi->graphic_image)
                                        <div class="w-14 h-10 rounded-lg overflow-hidden border border-slate-200">
                                            <img src="{{ $kpi->graphic_image }}" alt="Grafis" class="w-full h-full object-cover">
                                        </div>
                                    @else
                                        <span class="text-[10px] text-slate-400 italic">SVG Donut Chart</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <strong class="text-slate-900 text-sm">{{ number_format($kpi->actual) }}</strong>
                                    <span class="text-slate-500 text-[10px]">/ {{ number_format($kpi->target) }} {{ $kpi->unit }}</span>
                                </td>
                                <td class="p-3">
                                    <span class="bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        {{ $pct }}%
                                    </span>
                                </td>
                                <td class="p-3 text-slate-600 font-medium">
                                    {{ $kpi->verified_by ?? 'DLH Jabar' }}
                                </td>
                                <td class="p-3 text-right">
                                    <form action="{{ route('super-admin.programs.kpi.delete', $kpi->id) }}" method="POST" onsubmit="return confirm('Hapus indikator KPI ini?');" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs p-1">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-slate-400 text-xs">Belum ada indikator KPI terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- SECTION 3: MANAJEMEN BEFORE & AFTER (BANYAK)   -->
        <!-- ============================================== -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                <div>
                    <span class="bg-sky-50 text-sky-700 text-[10px] font-black uppercase px-2.5 py-1 rounded-md">
                        Transformasi Nyata
                    </span>
                    <h2 class="text-lg font-black text-slate-900 mt-2">
                        Manajemen Komparasi Before & After (Bisa Banyak Rekord)
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Unggah foto Sebelum dan Sesudah intervensi pada titik-titik tapak aksi yang berbeda.
                    </p>
                </div>

                <button onclick="document.getElementById('addBaForm').classList.toggle('hidden')" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm">
                    + Tambah Rekord Before & After
                </button>
            </div>

            <!-- Form Tambah Before & After (Collapsible) -->
            <form id="addBaForm" action="{{ route('super-admin.programs.before-after.store', $program->id) }}" method="POST" enctype="multipart/form-data" class="hidden bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-4 text-xs">
                @csrf
                <strong class="block text-sm font-black text-slate-900">Form Tambah Titik Before & After Baru</strong>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Judul Titik Aksi</label>
                        <input type="text" name="title" required placeholder="e.g. Muara Sungai & Pantai Purbayani" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Metrik Dampak / Capaian</label>
                        <input type="text" name="metric_impact" placeholder="e.g. Penurunan 80% sampah muara" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs text-emerald-700 font-bold">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Lokasi / Titik Koordinat</label>
                        <input type="text" name="location" placeholder="e.g. Pantai Rancabuaya (-6.0890, 106.7200)" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Tanggal Sebelum</label>
                            <input type="date" name="before_date" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Tanggal Sesudah</label>
                            <input type="date" name="after_date" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                        </div>
                    </div>

                    <!-- Before Image Upload -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Upload Foto SEBELUM (Kondisi Awal)</label>
                        <input type="file" name="before_image" required accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-200 file:text-slate-800 cursor-pointer">
                        <textarea name="before_description" rows="2" placeholder="Keterangan kondisi awal sebelum aksi..." class="w-full mt-2 px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs"></textarea>
                    </div>

                    <!-- After Image Upload -->
                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Upload Foto SESUDAH (Hasil Transformasi)</label>
                        <input type="file" name="after_image" required accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-800 cursor-pointer">
                        <textarea name="after_description" rows="2" placeholder="Keterangan hasil perubahan sesudah aksi..." class="w-full mt-2 px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('addBaForm').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-700 font-bold text-xs">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm">Simpan Rekord Before & After</button>
                </div>
            </form>

            <!-- Grid of Existing Before & After Records -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($program->beforeAfterRecords as $bar)
                    <div class="hud-card-white rounded-2xl overflow-hidden flex flex-col justify-between border border-slate-200">
                        <div class="space-y-3 p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                    {{ $bar->metric_impact }}
                                </span>
                                <form action="{{ route('super-admin.programs.before-after.delete', $bar->id) }}" method="POST" onsubmit="return confirm('Hapus rekord Before & After ini?');" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs">Hapus</button>
                                </form>
                            </div>

                            <h4 class="text-sm font-black text-slate-900 leading-snug">{{ $bar->title }}</h4>
                            <p class="text-[10px] text-slate-400">{{ $bar->location }}</p>

                            <!-- Mini Dual Preview -->
                            <div class="grid grid-cols-2 gap-2 pt-1">
                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold text-slate-400 block uppercase">Sebelum:</span>
                                    <div class="aspect-video rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
                                        <img src="{{ $bar->before_image }}" alt="Before" class="w-full h-full object-cover">
                                    </div>
                                    <p class="text-[10px] text-slate-500 line-clamp-2">{{ $bar->before_description }}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[9px] font-bold text-emerald-600 block uppercase">Sesudah:</span>
                                    <div class="aspect-video rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
                                        <img src="{{ $bar->after_image }}" alt="After" class="w-full h-full object-cover">
                                    </div>
                                    <p class="text-[10px] text-slate-500 line-clamp-2">{{ $bar->after_description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-slate-400 text-xs">Belum ada rekord Before & After. Klik tombol Tambah di atas.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
