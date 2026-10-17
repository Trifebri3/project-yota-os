<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900 font-serif">Program Admin Dashboard</h1>
                <p class="text-xs text-slate-400 mt-1">Koordinasikan dan monitor seluruh program CSR, relawan, keuangan, dan data dampak sosial.</p>
            </div>
            <span class="text-xs font-bold text-slate-400">{{ date('d M Y') }}</span>
        </div>

        <!-- Managed Programs Info -->
        <div class="bg-white border rounded-2xl shadow-sm p-6 space-y-4">
            <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Daftar Program Kerja Anda</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($programs as $p)
                    <div class="border rounded-2xl p-4 flex gap-4 items-center bg-slate-50">
                        @if($p->logo_program)
                            <img src="{{ $p->logo_program }}" class="w-12 h-12 rounded-xl object-cover shrink-0">
                        @else
                            <div class="w-12 h-12 rounded-xl bg-lime-100 flex items-center justify-center text-lime-950 font-bold shrink-0">
                                YOTA
                            </div>
                        @endif
                        <div class="space-y-1">
                            <strong class="block text-xs font-extrabold text-slate-800">{{ $p->name }}</strong>
                            <div class="text-[10px] text-gray-500 flex gap-3">
                                <span>PO: <strong>{{ $p->projectOfficer?->name ?? 'Belum Ditunjuk' }}</strong></span>
                                <span>Status: <strong class="uppercase text-[#4CAF50]">{{ $p->status }}</strong></span>
                            </div>
                            <span class="block text-[9px] text-slate-400">Periode: {{ $p->start_date?->format('d M Y') }} s/d {{ $p->end_date?->format('d M Y') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 20-Modules Aggregated Statistics Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white border p-5 rounded-2xl shadow-sm text-center">
                <span class="block text-[9px] font-bold text-gray-400 uppercase">Total Peserta</span>
                <strong class="block text-2xl font-black text-slate-800 mt-2">{{ $stats['total_peserta'] }}</strong>
            </div>
            <div class="bg-white border p-5 rounded-2xl shadow-sm text-center">
                <span class="block text-[9px] font-bold text-gray-400 uppercase">Total Volunteer</span>
                <strong class="block text-2xl font-black text-slate-800 mt-2">{{ $stats['total_volunteer'] }}</strong>
            </div>
            <div class="bg-white border p-5 rounded-2xl shadow-sm text-center">
                <span class="block text-[9px] font-bold text-gray-400 uppercase">Sub Program (WBS)</span>
                <strong class="block text-2xl font-black text-slate-800 mt-2">{{ $stats['total_sub_programs'] }}</strong>
            </div>
            <div class="bg-white border p-5 rounded-2xl shadow-sm text-center">
                <span class="block text-[9px] font-bold text-gray-400 uppercase">Total Aktivitas</span>
                <strong class="block text-2xl font-black text-slate-800 mt-2">{{ $stats['total_activities'] }}</strong>
            </div>
            <div class="bg-white border p-5 rounded-2xl shadow-sm text-center">
                <span class="block text-[9px] font-bold text-gray-400 uppercase">Total Logbook</span>
                <strong class="block text-2xl font-black text-slate-800 mt-2">{{ $stats['total_logbooks'] }}</strong>
            </div>
            <div class="bg-white border p-5 rounded-2xl shadow-sm text-center">
                <span class="block text-[9px] font-bold text-gray-400 uppercase">Total Kehadiran</span>
                <strong class="block text-2xl font-black text-slate-800 mt-2">{{ $stats['total_attendances'] }}</strong>
            </div>
            <div class="bg-white border p-5 rounded-2xl shadow-sm text-center bg-red-50/50">
                <span class="block text-[9px] font-bold text-red-500 uppercase">Emisi Karbon</span>
                <strong class="block text-xl font-black text-red-600 mt-2">{{ number_format($stats['total_emissions'], 1) }} kg</strong>
            </div>
            <div class="bg-white border p-5 rounded-2xl shadow-sm text-center bg-green-50/50">
                <span class="block text-[9px] font-bold text-green-600 uppercase">Reduksi Karbon</span>
                <strong class="block text-xl font-black text-green-600 mt-2">{{ number_format($stats['total_offsets'], 1) }} kg</strong>
            </div>
        </div>

        <!-- Workspace Navigation Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Project Manager Card -->
            <a href="{{ route('program-admin.project-manager.index') }}" class="bg-white border hover:border-[#4CAF50] p-6 rounded-2xl shadow-sm transition flex flex-col justify-between group">
                <div>
                    <div class="w-10 h-10 bg-lime-100 rounded-xl flex items-center justify-center mb-4 text-[#4CAF50] group-hover:bg-[#4CAF50] group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 uppercase">Project Manager</h3>
                    <p class="text-xs text-slate-400 mt-1">Kelola Project Charter, WBS, Milestone, dan Indikator Dampak.</p>
                </div>
            </a>

            <!-- Operations Card -->
            <a href="{{ route('program-admin.operations.index') }}" class="bg-white border hover:border-[#4CAF50] p-6 rounded-2xl shadow-sm transition flex flex-col justify-between group">
                <div>
                    <div class="w-10 h-10 bg-lime-100 rounded-xl flex items-center justify-center mb-4 text-[#4CAF50] group-hover:bg-[#4CAF50] group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 uppercase">Operations</h3>
                    <p class="text-xs text-slate-400 mt-1">Kegiatan lapangan, penugasan relawan, absensi geofence, dan logbook.</p>
                </div>
            </a>

            <!-- Finance Card -->
            <a href="{{ route('program-admin.finance.index') }}" class="bg-white border hover:border-[#4CAF50] p-6 rounded-2xl shadow-sm transition flex flex-col justify-between group">
                <div>
                    <div class="w-10 h-10 bg-lime-100 rounded-xl flex items-center justify-center mb-4 text-[#4CAF50] group-hover:bg-[#4CAF50] group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 uppercase">Finance</h3>
                    <p class="text-xs text-slate-400 mt-1">Alokasi anggaran, persetujuan reimbursement, spj, dan invoice.</p>
                </div>
            </a>

            <!-- Administration Card -->
            <a href="{{ route('program-admin.administration.index') }}" class="bg-white border hover:border-[#4CAF50] p-6 rounded-2xl shadow-sm transition flex flex-col justify-between group">
                <div>
                    <div class="w-10 h-10 bg-lime-100 rounded-xl flex items-center justify-center mb-4 text-[#4CAF50] group-hover:bg-[#4CAF50] group-hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-800 uppercase">Administration</h3>
                    <p class="text-xs text-slate-400 mt-1">Cetak sertifikat relawan, generate LPJ otomatis, dan berita acara.</p>
                </div>
            </a>
        </div>

        <!-- Grafik Progres Program Kerja (WBS Capaian) -->
        <div class="bg-white border rounded-2xl shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-900 uppercase">Progres Capaian Output Target Program Kerja</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                @foreach($programKerjas as $pk)
                    @php
                        $targetOutputs = is_array($pk->target_outputs) ? $pk->target_outputs : json_decode($pk->target_outputs, true);
                    @endphp
                    @if(is_array($targetOutputs) && count($targetOutputs) > 0)
                        @foreach($targetOutputs as $to)
                            @php
                                $targetVal = (float)($to['target'] ?? 1);
                                $achievedVal = (float)($to['achieved'] ?? 0);
                                $percent = $targetVal > 0 ? min(100, ($achievedVal / $targetVal) * 100) : 0;
                            @endphp
                            <div class="space-y-2 border p-4 rounded-xl bg-slate-50">
                                <div class="flex justify-between font-bold text-slate-800">
                                    <span>{{ $pk->name }} - {{ $to['label'] ?? 'Output' }}</span>
                                    <span class="text-[#2E7D32]">{{ $achievedVal }} / {{ $targetVal }}</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-3 overflow-hidden">
                                    <div class="bg-[#4CAF50] h-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                </div>
                                <div class="flex justify-between items-center text-[10px] text-gray-400">
                                    <span>Presentase Realisasi Target</span>
                                    <span class="font-bold text-slate-700">{{ round($percent) }}%</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="space-y-2 border p-4 rounded-xl bg-slate-50 flex items-center justify-between">
                            <span class="font-bold text-slate-700">{{ $pk->name }}</span>
                            <span class="text-gray-400 italic">Belum ada target didefinisikan</span>
                        </div>
                    @endif
                @endforeach
                @if($programKerjas->isEmpty())
                    <p class="col-span-2 text-center py-6 text-gray-400 italic font-medium">Belum ada target program kerja didefinisikan untuk program Anda.</p>
                @endif
            </div>
        </div>

        <!-- Approvals Notifications Panel -->
        <div class="bg-white border rounded-2xl shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-900 uppercase">Daftar Tunggu Persetujuan</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-slate-50 p-4 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="block text-xs font-semibold text-gray-400">Verifikasi Tugas</span>
                        <strong class="text-lg font-bold text-slate-850">{{ $stats['pending_tasks_review'] }} Laporan</strong>
                    </div>
                    <a href="{{ route('program-admin.tasks.approval') }}" class="text-[#4CAF50] text-xs font-bold hover:underline">Detail &rarr;</a>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="block text-xs font-semibold text-gray-400">Persetujuan Logbook</span>
                        <strong class="text-lg font-bold text-slate-850">{{ $stats['pending_logbooks'] }} Laporan</strong>
                    </div>
                    <a href="{{ route('program-admin.logbooks.approval') }}" class="text-[#4CAF50] text-xs font-bold hover:underline">Detail &rarr;</a>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="block text-xs font-semibold text-gray-400">Verifikasi Absensi Luar Zona</span>
                        <strong class="text-lg font-bold text-slate-850">{{ $stats['pending_tasks_review'] }} Absensi</strong>
                    </div>
                    <a href="{{ route('program-admin.attendance.validation') }}" class="text-[#4CAF50] text-xs font-bold hover:underline">Detail &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</x-program-admin-layout>
