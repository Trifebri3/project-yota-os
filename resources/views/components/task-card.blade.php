@props(['task'])

<div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm space-y-4 hover:shadow-md hover:border-slate-200 transition duration-300 relative group overflow-hidden">
    <!-- Top info row -->
    <div class="flex justify-between items-start gap-4">
        <div class="space-y-1">
            <span class="text-[9px] font-extrabold text-[#4CAF50] uppercase tracking-wider flex items-center gap-1 leading-none">
                <svg class="w-3.5 h-3.5 text-[#4CAF50] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ $task->activity?->workPlan?->program?->name ?? 'General Program' }}
            </span>
            <div class="flex items-center gap-1.5 mt-1">
                @if($task->activity?->workPlan?->tahapan)
                    <span class="bg-slate-100 text-slate-700 text-[8px] font-bold px-1.5 py-0.5 rounded uppercase">
                        {{ $task->activity->workPlan->tahapan }}
                    </span>
                @endif
                <h3 class="text-xs font-extrabold text-slate-800 tracking-wide group-hover:text-[#4CAF50] transition">
                    {{ $task->title }}
                </h3>
            </div>
        </div>
        
        @php
            $statusColor = 'bg-amber-50 text-amber-800 border-amber-100';
            if ($task->status === 'completed') {
                $statusColor = 'bg-green-50 text-green-800 border-green-100';
            } elseif ($task->status === 'in_progress') {
                $statusColor = 'bg-blue-50 text-blue-800 border-blue-100';
            }
        @endphp
        <span class="text-[8px] font-extrabold px-2.5 py-0.5 rounded-full uppercase border shrink-0 {{ $statusColor }}">
            {{ $task->status }}
        </span>
    </div>

    <!-- Description -->
    <p class="text-slate-400 text-[11px] leading-relaxed line-clamp-2">{{ $task->description }}</p>

    <!-- Bottom metadata and links -->
    <div class="flex justify-between items-center text-[10px] text-gray-500 pt-3 border-t border-slate-100">
        <div class="flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-slate-450 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>Batas Waktu:</span>
            <span class="font-bold text-slate-650">{{ $task->due_date?->format('d M Y, H:i') ?? '-' }}</span>
        </div>
        
        <a href="{{ route('volunteer.tasks.show', $task->id) }}" class="text-[#4CAF50] font-extrabold hover:underline flex items-center gap-0.5 transition active:translate-x-1">
            Detail Tugas <span class="text-xs">&rarr;</span>
        </a>
    </div>
</div>
