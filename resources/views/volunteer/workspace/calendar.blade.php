<x-volunteer-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div>
            <h1 class="text-xl font-bold text-slate-900 font-serif">Kalender & Timeline Program</h1>
            <p class="text-xs text-slate-400 mt-1">Pantau milestone program, jadwal sub-program, dan meeting agenda yang ditugaskan kepada Anda.</p>
        </div>

        <!-- Big Timelines (Super Admin Fixed Phases) -->
        <div class="bg-white border rounded-3xl p-6 shadow-sm">
            <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest mb-4">Milestone Utama Program</h3>
            <div class="space-y-4">
                @foreach($program->timelines as $phase)
                    <div class="flex items-center justify-between p-4 bg-slate-50 border rounded-2xl">
                        <div>
                            <strong class="text-xs text-slate-800">{{ $phase->name }}</strong>
                            <span class="block text-[10px] text-gray-400 mt-0.5 font-mono">
                                {{ $phase->start_date->format('d M Y') }} - {{ $phase->end_date->format('d M Y') }}
                            </span>
                        </div>
                        <div>
                            @if($phase->is_locked)
                                <span class="bg-red-50 text-red-700 border border-red-150 px-2.5 py-1 rounded-lg text-[10px] font-bold">🔒 Terkunci (Fixed)</span>
                            @else
                                <span class="bg-green-50 text-green-700 border border-green-150 px-2.5 py-1 rounded-lg text-[10px] font-bold">🔓 Fleksibel</span>
                            @endif
                        </div>
                    </div>
                @endforeach
                @if($program->timelines->isEmpty())
                    <p class="text-xs text-gray-400 italic">Belum ada fase timeline didefinisikan.</p>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left: Sub-Programs (WBS) -->
            <div class="bg-white border rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Struktur Rencana Kerja (WBS)</h3>
                <div class="space-y-4">
                    @foreach($program->programKerja as $work)
                        <div class="p-4 border border-dashed rounded-2xl bg-slate-50 space-y-2">
                            <span class="bg-purple-50 text-purple-800 border border-purple-150 px-2 py-0.5 rounded text-[9px] font-bold uppercase">Sub-Program</span>
                            <strong class="block text-xs text-slate-800">{{ $work->name }}</strong>
                            <p class="text-[10px] text-gray-400">{{ $work->description }}</p>
                            <span class="block text-[10px] font-semibold text-slate-600 font-mono mt-1">
                                Periode: {{ $work->start_date->format('d M Y') }} - {{ $work->end_date->format('d M Y') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right: Meeting Agenda -->
            <div class="bg-white border rounded-3xl p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Agenda Meeting</h3>
                <div class="space-y-4">
                    @foreach($agendas as $agenda)
                        <div class="p-4 bg-lime-50/50 border border-lime-150 rounded-2xl space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-[9px] font-bold uppercase">Meeting</span>
                                <span class="text-[10px] font-mono text-slate-500 font-bold">{{ $agenda->date->format('d M Y') }}</span>
                            </div>
                            <strong class="block text-xs text-slate-800">{{ $agenda->title }}</strong>
                            <p class="text-[10px] text-gray-400">{{ $agenda->description }}</p>
                            <div class="flex gap-4 pt-1 text-[10px] font-bold text-slate-650">
                                <span>⏰ {{ $agenda->start_time }} - {{ $agenda->end_time }}</span>
                                @if($agenda->meeting_link)
                                    <a href="{{ $agenda->meeting_link }}" target="_blank" class="text-blue-650 hover:underline">🌐 Join Meet &rarr;</a>
                                @else
                                    <span>📍 Offline: {{ $agenda->location ?? 'Posko Utama' }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    @if($agendas->isEmpty())
                        <p class="text-xs text-slate-400 italic text-center py-6">Belum ada agenda rapat terjadwal.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-volunteer-layout>
