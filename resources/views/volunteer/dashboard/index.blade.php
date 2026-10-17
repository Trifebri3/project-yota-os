<x-volunteer-layout>
    <div class="space-y-6 animate-fade-in" x-data="pwaInstallPrompt()">
        
        <!-- Welcome & Program Info Card -->
        <div class="bg-[#1e293b] text-white p-6 rounded-3xl shadow-sm relative overflow-hidden">
            <div class="space-y-3 z-10 relative">
                <span class="text-[9px] font-extrabold uppercase tracking-widest text-[#D9F99D]">Personal Execution Workspace</span>
                
                @if($program)
                    <div class="flex items-center gap-3">
                        @if($program->logo_program)
                            <img src="{{ $program->logo_program }}" class="w-10 h-10 rounded-xl object-cover border border-white/20 bg-white">
                        @endif
                        <div>
                            <h2 class="text-base font-extrabold text-white">{{ $program->name }}</h2>
                            <p class="text-[10px] text-gray-400">Periode: {{ $program->start_date->format('d M') }} - {{ $program->end_date->format('d M Y') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-3 border-t border-white/10 text-[10px]">
                        <div>
                            <span class="block text-gray-400">Zona Penugasan:</span>
                            <strong class="text-white">{{ $zoneName }}</strong>
                        </div>
                        <div>
                            <span class="block text-gray-400">Project Officer:</span>
                            <strong class="text-white">{{ $program->projectOfficer?->name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="block text-gray-400">Supervisor Program:</span>
                            <strong class="text-white">{{ $program->supervisor?->name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="block text-gray-400">Status Relawan:</span>
                            <strong class="text-[#D9F99D] uppercase">Aktif Lapangan</strong>
                        </div>
                    </div>
                @else
                    <div class="py-4 text-center">
                        <p class="text-xs text-gray-300 italic">Anda belum ditugaskan ke program apa pun oleh Super Admin.</p>
                    </div>
                @endif
            </div>
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full"></div>
        </div>

        <!-- Daily Carbon Log Warning Banner -->
        @if(!$filledToday)
            <div class="bg-red-50 border border-red-200 p-4 rounded-3xl text-xs text-red-950 flex items-start gap-2.5 shadow-sm animate-pulse">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center text-red-650 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="flex-1">
                    <strong class="block text-[10px] uppercase tracking-wider font-extrabold text-red-750">Laporan Karbon Wajib Harian</strong>
                    <p class="text-[10px] text-red-600 mt-0.5">Anda belum mengisi formulir kalkulasi jejak karbon untuk hari ini.</p>
                    <a href="{{ route('volunteer.carbon.fill') }}" class="inline-block mt-2 bg-red-600 hover:bg-red-700 text-white text-[10px] font-extrabold px-4 py-1.5 rounded-full transition shadow-sm">
                        Isi Laporan Sekarang &rarr;
                    </a>
                </div>
            </div>
        @endif

        <!-- Quick Access Workspace Modules Links Grid -->
        <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-3">
            <span class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider">Akses Cepat Workspace</span>
            <div class="grid grid-cols-4 gap-2 text-center text-[10px]">
                <a href="{{ route('volunteer.workspace.calendar') }}" class="p-3 bg-slate-50 hover:bg-slate-100 rounded-2xl border transition flex flex-col items-center justify-center">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="block font-semibold mt-2 text-slate-700">Calendar</span>
                </a>
                <a href="{{ route('volunteer.workspace.resources.index') }}" class="p-3 bg-slate-50 hover:bg-slate-100 rounded-2xl border transition flex flex-col items-center justify-center">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    <span class="block font-semibold mt-2 text-slate-700">SOP Center</span>
                </a>
                <a href="{{ route('volunteer.workspace.documents.index') }}" class="p-3 bg-slate-50 hover:bg-slate-100 rounded-2xl border transition flex flex-col items-center justify-center">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                    <span class="block font-semibold mt-2 text-slate-700">Sertifikat</span>
                </a>
                <a href="{{ route('volunteer.workspace.todos.index') }}" class="p-3 bg-slate-50 hover:bg-slate-100 rounded-2xl border transition flex flex-col items-center justify-center">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                    <span class="block font-semibold mt-2 text-slate-700">To-Do List</span>
                </a>
                <a href="{{ route('volunteer.workspace.complaints.index') }}" class="p-3 bg-slate-50 hover:bg-slate-100 rounded-2xl border transition flex flex-col items-center justify-center">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span class="block font-semibold mt-2 text-slate-700">Aduan</span>
                </a>
                <a href="{{ route('volunteer.workspace.reprimands.index') }}" class="p-3 bg-slate-50 hover:bg-slate-100 rounded-2xl border transition flex flex-col items-center justify-center">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span class="block font-semibold mt-2 text-slate-700">Teguran</span>
                </a>
                <a href="{{ route('volunteer.workspace.facilities.index') }}" class="p-3 bg-slate-50 hover:bg-slate-100 rounded-2xl border transition flex flex-col items-center justify-center">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span class="block font-semibold mt-2 text-slate-700">Atribut</span>
                </a>
                <a href="{{ route('volunteer.workspace.routines.index') }}" class="p-3 bg-slate-50 hover:bg-slate-100 rounded-2xl border transition flex flex-col items-center justify-center">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="block font-semibold mt-2 text-slate-700">Kegiatan Rutin</span>
                </a>
                <a href="{{ route('volunteer.workspace.additional.index') }}" class="p-3 bg-slate-50 hover:bg-slate-100 rounded-2xl border transition col-span-2 flex flex-col items-center justify-center">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span class="block font-semibold mt-2 text-slate-700">Aktivitas Tambahan</span>
                </a>
                <a href="{{ route('volunteer.workspace.logbooks.print') }}" target="_blank" class="p-3 bg-slate-50 hover:bg-slate-100 rounded-2xl border transition col-span-2 flex flex-col items-center justify-center">
                    <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span class="block font-semibold mt-2 text-slate-700">Cetak Logbook PDF</span>
                </a>
            </div>
        </div>

        <!-- Ringkasan Personal -->
        <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4">
            <span class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider">Ringkasan Personal & Karbon</span>
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="bg-slate-50 p-3 rounded-2xl border">
                    <span class="block text-[9px] text-gray-400 uppercase">Jumlah Kehadiran</span>
                    <strong class="text-sm font-extrabold text-slate-800">{{ $totalAttendanceCount }} Kali</strong>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border">
                    <span class="block text-[9px] text-gray-400 uppercase">Tugas Selesai</span>
                    <strong class="text-sm font-extrabold text-slate-800">{{ $completedTasksCount }} Tugas</strong>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border">
                    <span class="block text-[9px] text-gray-400 uppercase">Tugas Berjalan</span>
                    <strong class="text-sm font-extrabold text-slate-800">{{ $activeTasksCount }} Tugas</strong>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border">
                    <span class="block text-[9px] text-gray-400 uppercase">Total Logbook</span>
                    <strong class="text-sm font-extrabold text-slate-800">{{ $totalLogbooks }} Jurnal</strong>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border">
                    <span class="block text-[9px] text-gray-400 uppercase">Jam Kontribusi</span>
                    <strong class="text-sm font-extrabold text-slate-800">{{ number_format($totalHours, 1) }} Jam</strong>
                </div>
                <div class="bg-slate-50 p-3 rounded-2xl border">
                    <span class="block text-[9px] text-gray-400 uppercase">Total Aktivitas</span>
                    <strong class="text-sm font-extrabold text-slate-800">{{ $totalActivities }} Kegiatan</strong>
                </div>
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 p-3.5 rounded-2xl text-amber-950 col-span-2 flex items-center justify-between shadow-sm">
                    <div>
                        <span class="block text-[9px] text-amber-600 uppercase font-bold tracking-wider">🏆 Total Poin Achievement</span>
                        <strong class="text-base font-black text-amber-900 mt-1 block">{{ $totalAchievementPoints }} Poin</strong>
                    </div>
                    <span class="text-2xl animate-pulse">🥇</span>
                </div>
                <div class="bg-red-50 p-3 rounded-2xl border border-red-150 text-red-950">
                    <span class="block text-[9px] text-red-500 uppercase font-bold">Emisi Karbon</span>
                    <strong class="text-sm font-extrabold">{{ number_format($totalEmissions, 2) }} kg</strong>
                </div>
                <div class="bg-green-50 p-3 rounded-2xl border border-green-150 text-green-950">
                    <span class="block text-[9px] text-green-600 uppercase font-bold">Reduksi Karbon</span>
                    <strong class="text-sm font-extrabold">{{ number_format($totalOffsets, 2) }} kg</strong>
                </div>
            </div>
        </div>

        <!-- Agenda Hari Ini -->
        <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-3">
            <span class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider">Agenda & Deadline Hari Ini</span>
            
            <!-- Attendance Reminder -->
            @if(!$attendance)
                <div class="bg-amber-50 border border-amber-200 p-3.5 rounded-2xl flex items-center justify-between text-xs text-amber-950">
                    <div>
                        <strong class="block font-bold">⚠️ Pengingat Absensi</strong>
                        <span class="text-[10px] text-amber-700">Anda belum absen masuk hari ini.</span>
                    </div>
                    <a href="{{ route('volunteer.attendance.index') }}" class="bg-amber-600 hover:bg-amber-700 text-white text-[10px] font-bold px-3 py-1.5 rounded-full transition">Absen Masuk</a>
                </div>
            @endif

            <!-- Todays Agendas -->
            @foreach($todaysAgendas as $agenda)
                <div class="p-3.5 bg-blue-50/50 border border-blue-150 rounded-2xl text-xs space-y-1">
                    <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded text-[8px] font-bold uppercase">Meeting Rapat</span>
                    <strong class="block text-slate-800">{{ $agenda->title }}</strong>
                    <span class="block text-[10px] text-gray-500 font-mono">⏰ {{ $agenda->start_time }} - {{ $agenda->end_time }}</span>
                </div>
            @endforeach

            <!-- Todays Tasks Deadline -->
            @foreach($todaysTasks as $t)
                <div class="p-3.5 bg-red-50/50 border border-red-150 rounded-2xl text-xs space-y-1">
                    <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded text-[8px] font-bold uppercase">Deadline Tugas</span>
                    <strong class="block text-slate-800">{{ $t->title }}</strong>
                </div>
            @endforeach

            @if($todaysAgendas->isEmpty() && $todaysTasks->isEmpty() && $attendance)
                <p class="text-xs text-gray-400 italic text-center py-4">Tidak ada agenda atau tenggat waktu tersisa untuk hari ini.</p>
            @endif
        </div>

        <!-- Kegiatan Rutin Hari Ini -->
        <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-3">
            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Kegiatan Rutin Hari Ini</span>
            @foreach($routines as $rt)
                <div class="p-3.5 bg-slate-50 border rounded-2xl flex items-center justify-between text-xs">
                    <div class="space-y-1">
                        <strong class="block text-slate-850">{{ $rt->name }}</strong>
                        <span class="block text-[10px] text-gray-500 font-mono">⏰ {{ $rt->start_time }} - {{ $rt->end_time }}</span>
                        <div class="flex items-center gap-1.5 pt-1">
                            @if($rt->is_selfie_required)
                                <span class="bg-blue-50 text-blue-800 text-[8px] font-bold px-1.5 py-0.5 rounded border border-blue-100">📸 Selfie</span>
                            @endif
                            @if($rt->is_notes_required)
                                <span class="bg-amber-50 text-amber-800 text-[8px] font-bold px-1.5 py-0.5 rounded border border-amber-100">📝 Catatan</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        @if($rt->is_completed_today)
                            <span class="bg-green-100 text-green-950 px-3 py-1.5 rounded-full text-[9px] font-extrabold uppercase tracking-wide">Selesai</span>
                        @else
                            <a href="{{ route('volunteer.workspace.routines.index') }}" class="inline-block bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-[9px] font-extrabold px-3 py-1.5 rounded-full transition">Laporkan</a>
                        @endif
                    </div>
                </div>
            @endforeach
            @if($routines->isEmpty())
                <p class="text-xs text-gray-400 italic text-center py-4">Tidak ada kegiatan rutin wajib untuk program Anda.</p>
            @endif
        </div>

        <!-- Progress Progres Program Kerja (V2 Enhanced) -->
        <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4">
            <div>
                <span class="block text-[9px] font-extrabold text-[#4CAF50] uppercase tracking-wider">Capaian Sub-Program</span>
                <h3 class="text-sm font-extrabold text-slate-800">Progres Capaian Program Kerja</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Pantau realisasi target output utama pada setiap sub-program.</p>
            </div>
            
            <div class="space-y-4 text-xs">
                @foreach($programKerjas as $pk)
                    @php
                        $targetOutputs = is_array($pk->target_outputs) ? $pk->target_outputs : json_decode($pk->target_outputs, true);
                    @endphp
                    @if(is_array($targetOutputs))
                        <div class="border border-slate-100 rounded-2xl p-4 bg-slate-50/50 space-y-3.5 shadow-sm">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <strong class="font-extrabold text-slate-800 uppercase text-[10px] tracking-wide">{{ $pk->name }}</strong>
                                <span class="bg-slate-200/70 text-slate-700 font-bold px-2 py-0.5 rounded text-[8px] uppercase tracking-wider">WBS Stage</span>
                            </div>
                            
                            <div class="space-y-4">
                                @foreach($targetOutputs as $to)
                                    @php
                                        $targetVal = (float)($to['target'] ?? 1);
                                        $achievedVal = (float)($to['achieved'] ?? 0);
                                        $percent = $targetVal > 0 ? min(100, ($achievedVal / $targetVal) * 100) : 0;
                                        
                                        // Status badge
                                        $statusLabel = "Belum Dimulai";
                                        $statusClass = "bg-slate-100 text-slate-655 border-slate-200";
                                        if ($percent >= 100) {
                                            $statusLabel = "Tercapai 100%";
                                            $statusClass = "bg-green-50 text-green-800 border-green-200";
                                        } elseif ($percent > 0) {
                                            $statusLabel = "Sedang Berjalan";
                                            $statusClass = "bg-blue-50 text-blue-800 border-blue-150";
                                        }

                                        // Distinct gradient colors
                                        $gradients = [
                                            'from-emerald-400 to-emerald-600', // Emerald Green
                                            'from-sky-400 to-indigo-650',       // Sky Blue to Indigo
                                            'from-amber-400 to-orange-500',     // Amber to Orange
                                            'from-purple-400 to-pink-600',      // Purple to Pink
                                            'from-teal-400 to-cyan-500',        // Teal to Cyan
                                        ];
                                        $gradientClass = $gradients[$loop->index % count($gradients)];
                                    @endphp
                                    <div class="space-y-2 bg-white p-3.5 border border-slate-100 rounded-xl shadow-sm hover:shadow transition duration-200">
                                        <div class="flex justify-between items-start gap-2">
                                            <div>
                                                <span class="block font-extrabold text-slate-750 text-xs">{{ $to['label'] ?? 'Output' }}</span>
                                                <span class="text-[9px] text-slate-400 font-semibold mt-0.5 block">Realisasi: {{ $achievedVal }} dari {{ $targetVal }} {{ $to['unit'] ?? 'unit' }}</span>
                                            </div>
                                            <span class="text-[8px] font-extrabold px-2.5 py-0.5 rounded-full uppercase border shrink-0 {{ $statusClass }}">
                                                {{ $statusLabel }}
                                            </span>
                                        </div>
                                        
                                        <div class="space-y-1">
                                            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden border shadow-inner">
                                                <div class="bg-gradient-to-r {{ $gradientClass }} h-full rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                            </div>
                                            <div class="flex justify-between items-center text-[9px] text-slate-400 font-bold mt-1">
                                                <span>Persentase Target</span>
                                                <span>{{ round($percent) }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="border border-slate-100 rounded-2xl p-4 bg-slate-50/50 flex justify-between items-center">
                            <span class="font-bold text-slate-650">{{ $pk->name }}</span>
                            <span class="text-slate-405 italic">Belum ada target WBS</span>
                        </div>
                    @endif
                @endforeach

                @if($programKerjas->isEmpty())
                    <p class="text-xs text-gray-400 italic text-center py-4">Belum ada target program kerja didefinisikan.</p>
                @endif
            </div>
        </div>

        <!-- Progress Kontribusi Saya -->
        <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4">
            <span class="block text-[9px] font-bold text-gray-400 uppercase tracking-wider">Progress Dampak Kontribusi Saya</span>
            
            <div class="space-y-3 text-xs">
                <div>
                    <div class="flex justify-between font-bold mb-1">
                        <span>Penanaman Pohon Mangrove</span>
                        <span class="text-[#4CAF50]">{{ $myTrees }} Pohon</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-[#4CAF50] h-full" style="width: {{ min(100, ($myTrees / 100) * 100) }}%"></div>
                    </div>
                    <span class="text-[9px] text-gray-400 mt-0.5 block">Target Personal Kontribusi: 100 Pohon</span>
                </div>

                <div>
                    <div class="flex justify-between font-bold mb-1">
                        <span>Pengolahan Sampah Organik</span>
                        <span class="text-purple-700">{{ $myWaste }} Kg</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-purple-600 h-full" style="width: {{ min(100, ($myWaste / 50) * 100) }}%"></div>
                    </div>
                    <span class="text-[9px] text-gray-400 mt-0.5 block">Target Personal Kontribusi: 50 Kg</span>
                </div>
            </div>
        </div>

    </div>

    <!-- PWA Installation Script handler -->
    <script>
        function pwaInstallPrompt() {
            return {
                deferredPrompt: null,
                init() {
                    window.addEventListener('beforeinstallprompt', (e) => {
                        e.preventDefault();
                        this.deferredPrompt = e;
                    });
                },
                installApp() {
                    if (this.deferredPrompt) {
                        this.deferredPrompt.prompt();
                        this.deferredPrompt.userChoice.then((choiceResult) => {
                            if (choiceResult.outcome === 'accepted') {
                                this.deferredPrompt = null;
                            }
                        });
                    }
                }
            }
        }
    </script>
</x-volunteer-layout>
