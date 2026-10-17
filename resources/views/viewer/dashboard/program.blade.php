<x-viewer-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Back and Header -->
        <div>
            <a href="{{ route('viewer.dashboard') }}" class="text-sky-600 hover:text-sky-700 font-extrabold text-xs inline-flex items-center gap-1.5 transition">
                &larr; Kembali ke Dasbor Global
            </a>
            <div class="bg-white p-6 rounded-3xl border border-slate-150 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mt-3">
                <div>
                    <span class="bg-sky-100 text-sky-850 text-[9px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wider">
                        Detail Program Monitor
                    </span>
                    <h1 class="text-xl font-bold text-slate-900 font-serif mt-2">{{ $program->name }}</h1>
                    <p class="text-xs text-slate-400 mt-1 max-w-2xl leading-relaxed">{{ $program->description }}</p>
                </div>
                <div class="text-left shrink-0">
                    <span class="text-xs font-bold text-slate-400 block">Periode Program</span>
                    <span class="text-xs font-extrabold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-full inline-block mt-1">
                        {{ \Carbon\Carbon::parse($program->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($program->end_date)->format('d M Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Metric Grid (Cleaner Cards, No Emojis) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Team assignment Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-150 shadow-sm border-l-4 border-l-slate-700 flex flex-col justify-between h-32">
                <div>
                    <span class="text-[9px] uppercase tracking-widest text-slate-400 font-bold block">Personel Lapangan</span>
                    <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $volunteersCount + $pesertaCount }} Akun</h3>
                </div>
                <p class="text-[10px] text-slate-400">{{ $volunteersCount }} Volunteers, {{ $pesertaCount }} Peserta aktif</p>
            </div>

            <!-- Activities Count Card -->
            @php
                $programActivitiesCount = \App\Models\Kegiatan::whereHas('workPlan', function($q) use ($program) {
                    $q->where('program_id', $program->id);
                })->count();
            @endphp
            <div class="bg-white p-6 rounded-3xl border border-slate-150 shadow-sm border-l-4 border-l-sky-500 flex flex-col justify-between h-32">
                <div>
                    <span class="text-[9px] uppercase tracking-widest text-slate-400 font-bold block">Kegiatan Aktif</span>
                    <h3 class="text-2xl font-extrabold text-slate-800 mt-1">{{ $programActivitiesCount }} Aksi Lapangan</h3>
                </div>
                <p class="text-[10px] text-slate-400">Terdistribusi pada rencana kerja program</p>
            </div>

            <!-- Carbon Impact Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-150 shadow-sm border-l-4 border-l-emerald-500 flex flex-col justify-between h-32 sm:col-span-2 lg:col-span-1">
                <div>
                    <span class="text-[9px] uppercase tracking-widest text-slate-400 font-bold block">Dampak Karbon</span>
                    <h3 class="text-xl font-extrabold text-emerald-600 mt-1">{{ number_format($carbonMetrics['total_offsets'], 2) }} kg CO₂e</h3>
                </div>
                <p class="text-[10px] text-slate-400">Offset terkontrol (Emisi: {{ number_format($carbonMetrics['total_emissions'], 1) }} kg)</p>
            </div>
        </div>

        <!-- Details & Timeline Grid (Responsive layout) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Charter and WBS (Col span 2) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Project Charter Details -->
                <div class="bg-white p-6 rounded-3xl border border-slate-150 shadow-sm space-y-4">
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Project Charter Program</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Visi Program</span>
                            <p class="text-slate-700 mt-1.5 leading-relaxed font-medium">{{ $program->projectCharter?->vision ?? 'Belum didefinisikan.' }}</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Misi Program</span>
                            <p class="text-slate-700 mt-1.5 leading-relaxed font-medium">{{ $program->projectCharter?->mission ?? 'Belum didefinisikan.' }}</p>
                        </div>
                        <div class="md:col-span-2 p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ruang Lingkup</span>
                            <p class="text-slate-700 mt-1.5 leading-relaxed font-medium">{{ $program->projectCharter?->scope ?? 'Belum didefinisikan.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- WBS Table (Program Kerja & Kegiatan) -->
                <div class="bg-white border border-slate-150 rounded-3xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Work Breakdown Structure (WBS)</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">Daftar tahapan program kerja, target output, dan kegiatan lapangan aktif.</p>
                    </div>
                    <div class="p-6 space-y-6">
                        @forelse($program->programKerja as $wp)
                            <div class="border border-slate-150 rounded-2xl p-4 bg-slate-50/50 space-y-4 hover:border-slate-350 transition">
                                <div class="flex flex-col sm:flex-row justify-between items-start gap-2">
                                    <div>
                                        <span class="bg-sky-50 text-sky-700 text-[9px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wider">{{ $wp->tahapan }}</span>
                                        <h4 class="text-xs font-extrabold text-slate-850 mt-2">{{ $wp->name }}</h4>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $wp->description }}</p>
                                    </div>
                                    <span class="text-[9px] font-extrabold uppercase tracking-widest px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 shrink-0">
                                        {{ $wp->status }}
                                    </span>
                                </div>

                                <!-- Activities under this Workplan -->
                                <div class="space-y-2 pt-2 border-t border-slate-100">
                                    <span class="block text-[9px] font-extrabold text-slate-400 uppercase tracking-wider">Kegiatan Lapangan Terkoneksi:</span>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @forelse($wp->kegiatan as $act)
                                            <div class="bg-white p-3 rounded-xl border border-slate-150 text-xs flex flex-col justify-between gap-1 shadow-sm">
                                                <div class="font-bold text-slate-800">{{ $act->name }}</div>
                                                <div class="flex justify-between items-center text-[9px] text-slate-400 mt-1 border-t border-slate-50 pt-1">
                                                    <span>Wilayah: {{ $act->zone?->name ?? 'Global' }}</span>
                                                    <span>{{ \Carbon\Carbon::parse($act->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($act->end_date)->format('d M Y') }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <span class="text-[10px] text-slate-300 italic block pl-1 col-span-2">Belum ada kegiatan di tahapan ini.</span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-400 text-center py-6 text-xs">Belum ada struktur program kerja (WBS) terdaftar.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Side: Personnel and Feeds (Col span 1) -->
            <div class="space-y-8">
                <!-- Assigned Personnel list -->
                <div class="bg-white p-6 rounded-3xl border border-slate-150 shadow-sm space-y-4">
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Daftar Personel</h3>
                    <div class="space-y-3 max-h-60 overflow-y-auto pr-1 text-xs">
                        @forelse($program->assignments as $as)
                            <div class="flex items-center gap-3 p-2 bg-slate-50 rounded-xl border border-slate-150 hover:border-slate-300 transition">
                                <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                    {{ substr($as->user?->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-800 truncate">{{ $as->user?->name }}</div>
                                    <span class="text-[8px] bg-slate-200 text-slate-650 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider mt-1 inline-block">
                                        {{ $as->user?->role }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-slate-400 text-center py-4">Belum ada staf/relawan ditugaskan.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Program Specific Logs -->
                <div class="bg-white p-6 rounded-3xl border border-slate-150 shadow-sm space-y-4">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                        <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Aktivitas Terbaru</h3>
                        <!-- Program Volunteer Filter Dropdown -->
                        <div class="shrink-0">
                            <select id="volunteer-filter" onchange="filterLogbooks()" class="text-[10px] px-2.5 py-1 border rounded-lg bg-slate-50 border-slate-200 focus:outline-none focus:ring-1 focus:ring-sky-500 font-medium">
                                <option value="">Semua Personel</option>
                                @foreach($volunteers as $v)
                                    <option value="user-{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="space-y-4 max-h-[400px] overflow-y-auto pr-1 text-xs">
                        @forelse($recentLogbooks as $log)
                            <div data-user-id="user-{{ $log->user_id }}" class="logbook-card border-l-2 border-sky-400 pl-4 py-1 space-y-2 hover:border-sky-600 transition">
                                <div class="flex justify-between items-center text-[10px] text-slate-400 font-medium">
                                    <span class="font-bold text-slate-700">{{ $log->user?->name }}</span>
                                    <span class="text-slate-350">{{ \Carbon\Carbon::parse($log->date)->format('d M Y') }}</span>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">{{ $log->title }}</div>
                                    <p class="text-[10px] text-slate-450 leading-relaxed mt-0.5">{{ $log->content }}</p>
                                </div>
                                
                                <!-- Logbook Photo (If Present) -->
                                @if($log->photo_path)
                                    <div class="mt-2 overflow-hidden rounded-lg border border-slate-200/50 max-h-32 bg-slate-100">
                                        <img src="{{ $log->photo_path }}" alt="Dokumentasi Lapangan" class="w-full h-full object-cover max-h-32 hover:scale-105 transition duration-300">
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-slate-400 text-center py-4">Belum ada logbook tercatat.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Client-side Filtering Script -->
    <script>
        function filterLogbooks() {
            const selectedUserId = document.getElementById('volunteer-filter').value;
            const cards = document.querySelectorAll('.logbook-card');
            cards.forEach(card => {
                if (selectedUserId === '' || card.getAttribute('data-user-id') === selectedUserId) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }
    </script>
</x-viewer-layout>
