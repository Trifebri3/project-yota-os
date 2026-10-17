<x-program-admin-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Project Manager Workspace</h1>
                <p class="text-xs text-slate-400 mt-1">Kelola perancangan program kerja, anggaran program, dan milestones dampak sosial.</p>
            </div>
        </div>

        <!-- Programs List -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($programs as $program)
                <div class="bg-white border rounded-2xl p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="flex justify-between items-start">
                            <h3 class="text-lg font-bold text-slate-800">{{ $program->name }}</h3>
                            <span class="bg-slate-100 text-slate-700 text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">
                                {{ $program->status }}
                            </span>
                        </div>
                        <p class="text-slate-400 text-xs mt-2 line-clamp-3">{{ $program->description }}</p>
                        
                        <div class="mt-4 bg-gray-50 p-3 rounded-xl text-xs space-y-1">
                            <div class="flex justify-between text-gray-500">
                                <span>Pagu Anggaran:</span>
                                <strong class="text-slate-800">IDR {{ number_format($program->projectCharter?->budget ?? 0, 0, ',', '.') }}</strong>
                            </div>
                            <div class="flex justify-between text-gray-500">
                                <span>Durasi:</span>
                                <strong class="text-slate-800">{{ $program->start_date?->format('d M Y') ?? '-' }} s/d {{ $program->end_date?->format('d M Y') ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="border-t pt-4 mt-6 flex gap-3">
                        <a href="{{ route('program-admin.charter.show', $program->id) }}" class="flex-1 text-center bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold py-2.5 rounded-xl transition">
                            Project Charter
                        </a>
                        <a href="{{ route('program-admin.workplans.show', $program->id) }}" class="flex-1 text-center bg-[#D9F99D] hover:bg-lime-300 text-lime-950 text-xs font-bold py-2.5 rounded-xl transition">
                            Struktur WBS
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-program-admin-layout>
