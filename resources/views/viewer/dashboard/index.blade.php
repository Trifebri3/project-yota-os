<x-viewer-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header Section -->
        <div class="bg-white p-6 rounded-3xl border border-slate-150 shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900 font-serif">Dasbor Pemantauan Global</h1>
                <p class="text-xs text-slate-400 mt-1">Pantau perkembangan program, realisasi kegiatan lapangan, dan logbook aktivitas peserta secara real-time.</p>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100 text-[10px] font-extrabold uppercase shrink-0">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-ping"></span>
                <span>Monitoring Aktif</span>
            </div>
        </div>

        <!-- Global Metrics Cards Grid (Responsive & Clean 3 Columns) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Program Card -->
            <div class="bg-white border border-slate-150 p-6 rounded-3xl shadow-sm border-l-4 border-l-slate-700 flex flex-col justify-between h-36">
                <div>
                    <span class="text-[9px] uppercase tracking-widest text-slate-400 font-extrabold block">Total Program</span>
                    <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $totalPrograms }}</h3>
                </div>
                <p class="text-[11px] text-slate-400">Jumlah program terdaftar saat ini</p>
            </div>

            <!-- Activities Card -->
            <div class="bg-white border border-slate-150 p-6 rounded-3xl shadow-sm border-l-4 border-l-sky-500 flex flex-col justify-between h-36">
                <div>
                    <span class="text-[9px] uppercase tracking-widest text-slate-400 font-extrabold block">Kegiatan Lapangan</span>
                    <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $totalActivities }}</h3>
                </div>
                <p class="text-[11px] text-slate-400">Total rencana kerja lapangan aktif</p>
            </div>

            <!-- Participants Card -->
            <div class="bg-white border border-slate-150 p-6 rounded-3xl shadow-sm border-l-4 border-l-emerald-500 flex flex-col justify-between h-36 sm:col-span-2 lg:col-span-1">
                <div>
                    <span class="text-[9px] uppercase tracking-widest text-slate-400 font-extrabold block">Partisipan Aktif</span>
                    <h3 class="text-3xl font-extrabold text-slate-800 mt-2">{{ $totalVolunteers + $totalPeserta }}</h3>
                </div>
                <p class="text-[11px] text-slate-400">Terdiri dari {{ $totalVolunteers }} Volunteer, {{ $totalPeserta }} Peserta</p>
            </div>
        </div>

        <!-- Active Programs Progress Tracker -->
        <div class="bg-white border border-slate-150 rounded-3xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Pemantauan Program Kerja</h2>
                <p class="text-[11px] text-slate-400 mt-0.5">Daftar semua program beserta status realisasi kegiatan lapangan.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs min-w-[600px]">
                    <thead>
                        <tr class="bg-slate-50 text-slate-450 font-bold uppercase border-b border-slate-100">
                            <th class="p-4 pl-6">Nama Program</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4">Total Rencana Kerja</th>
                            <th class="p-4">Periode</th>
                            <th class="p-4 pr-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-650">
                        @forelse($programs as $p)
                            @php
                                $progActivitiesCount = \App\Models\Kegiatan::whereHas('workPlan', function($q) use ($p) {
                                    $q->where('program_id', $p->id);
                                })->count();
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4 pl-6">
                                    <div class="font-extrabold text-slate-800 text-sm">{{ $p->name }}</div>
                                    <span class="text-[10px] text-slate-400 block mt-0.5 max-w-md truncate">{{ $p->description }}</span>
                                </td>
                                <td class="p-4">
                                    <span class="bg-sky-50 text-sky-700 text-[9px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wider">
                                        {{ $p->kategori_program ?? 'Umum' }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="font-semibold text-slate-700 bg-slate-100 px-2 py-1 rounded-lg inline-block">{{ $progActivitiesCount }} Kegiatan</div>
                                </td>
                                <td class="p-4 text-slate-400 font-medium">
                                    {{ \Carbon\Carbon::parse($p->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($p->end_date)->format('d M Y') }}
                                </td>
                                <td class="p-4 pr-6 text-right">
                                    <a href="{{ route('viewer.programs.show', $p->id) }}" class="inline-block bg-sky-50 hover:bg-sky-100 text-sky-750 font-extrabold px-4 py-2 rounded-xl transition text-[10px] uppercase">
                                        Monitor
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-400">Belum ada program terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Live Feeds: Logbooks Only -->
        <div class="bg-white border border-slate-150 rounded-3xl shadow-sm p-6 space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Aktifitas Logbook Peserta Lapangan</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Laporan aktivitas harian relawan dan peserta yang diajukan di lapangan.</p>
                </div>
                <!-- Volunteer Filter Dropdown -->
                <div class="shrink-0">
                    <select id="volunteer-filter" onchange="filterLogbooks()" class="text-xs px-4 py-2 border rounded-xl bg-slate-50 border-slate-200 focus:outline-none focus:ring-1 focus:ring-sky-500 font-medium">
                        <option value="">Semua Volunteer / Peserta</option>
                        @foreach($volunteers as $v)
                            <option value="user-{{ $v->id }}">{{ $v->name }} ({{ ucfirst($v->role) }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($recentLogbooks as $log)
                    <div data-user-id="user-{{ $log->user_id }}" class="logbook-card p-4 rounded-2xl bg-slate-50 border border-slate-150 text-xs space-y-3 flex flex-col justify-between hover:border-slate-300 transition">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-[10px] uppercase">
                                        {{ substr($log->user?->name, 0, 1) }}
                                    </div>
                                    <div class="font-bold text-slate-800">{{ $log->user?->name }}</div>
                                </div>
                                <span class="text-[9px] bg-sky-100 text-sky-700 px-2 py-0.5 rounded-full font-bold uppercase tracking-wide">
                                    {{ $log->status }}
                                </span>
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-xs">{{ $log->title }}</div>
                                <p class="text-[10px] text-slate-400 mt-1 leading-relaxed">{{ $log->content }}</p>
                            </div>
                            
                            <!-- Logbook Photo (If Present) -->
                            @if($log->photo_path)
                                <div class="mt-3 overflow-hidden rounded-xl border border-slate-200/50 max-h-48 bg-slate-100">
                                    <img src="{{ $log->photo_path }}" alt="Dokumentasi Lapangan" class="w-full h-full object-cover max-h-48 hover:scale-105 transition duration-300">
                                </div>
                            @endif
                        </div>
                        <div class="flex justify-between items-center text-[10px] text-slate-400 pt-3 border-t border-slate-150 mt-2 font-medium">
                            <span class="truncate max-w-[150px]">Program: {{ $log->program?->name }}</span>
                            <span class="text-slate-350">{{ $log->created_at ? $log->created_at->diffForHumans() : '-' }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-400 text-center py-6 col-span-2">Belum ada logbook lapangan yang dikirimkan.</p>
                @endforelse
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
