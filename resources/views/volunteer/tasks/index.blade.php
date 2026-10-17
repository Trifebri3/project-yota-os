<x-volunteer-layout>
    <div class="space-y-6 animate-fade-in" x-data="{ openOverdue: false, openFuture: false, openApproved: false, openPending: false }">
        <!-- Header -->
        <div>
            <span class="text-[9px] font-extrabold uppercase tracking-widest text-[#4CAF50]">Penugasan Relawan</span>
            <h1 class="text-lg font-extrabold text-slate-900 leading-snug">Tugas Lapangan WBS</h1>
            <p class="text-[10px] text-slate-400 mt-0.5">Selesaikan tugas terstruktur dan laporkan progres proyek langsung dari lokasi kegiatan.</p>
        </div>

        <!-- SECTION 1: HARI INI (TODAY'S TASKS) - SHOW DIRECTLY -->
        <div class="space-y-3">
            <div class="flex items-center justify-between border-b pb-2 border-slate-100">
                <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                    Tugas Hari Ini (Due Today)
                </span>
                <span class="bg-red-50 text-red-700 text-[9px] font-extrabold px-2.5 py-0.5 rounded-full border border-red-150">
                    {{ $todaysTasks->count() }} Tugas
                </span>
            </div>

            <div class="space-y-3">
                @foreach($todaysTasks as $task)
                    <x-task-card :task="$task" />
                @endforeach

                @if($todaysTasks->isEmpty())
                    <div class="text-center py-6 bg-white border border-dashed rounded-3xl text-gray-400 text-[10px] font-semibold">
                         Tidak ada tenggat tugas untuk hari ini.
                    </div>
                @endif
            </div>
        </div>

        <!-- SECTION 2: BELUM DIISI / TERTUNDDA (UNFILLED / PENDING REVIEW) - ACCORDION -->
        <div class="bg-white border rounded-3xl p-4 shadow-sm space-y-2">
            <button 
                type="button" 
                @click="openPending = !openPending" 
                class="w-full flex items-center justify-between font-bold text-xs text-slate-850"
            >
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Belum Diisi / Pending Review</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-amber-50 text-amber-800 text-[9px] px-2.5 py-0.5 rounded-full font-bold border border-amber-150">
                        {{ $pendingTasks->count() }} Tugas
                    </span>
                    <span class="text-slate-400 transform transition-transform" :class="openPending ? 'rotate-180' : ''">&darr;</span>
                </div>
            </button>

            <div x-show="openPending" x-collapse class="pt-3 space-y-3 border-t border-slate-100 mt-2">
                @foreach($pendingTasks as $task)
                    <x-task-card :task="$task" />
                @endforeach

                @if($pendingTasks->isEmpty())
                    <p class="text-center py-4 text-slate-400 text-[10px] italic">Tidak ada tugas di kategori ini.</p>
                @endif
            </div>
        </div>

        <!-- SECTION 3: SUDAH TERLEWAT (OVERDUE/PAST) - ACCORDION -->
        <div class="bg-white border rounded-3xl p-4 shadow-sm space-y-2">
            <button 
                type="button" 
                @click="openOverdue = !openOverdue" 
                class="w-full flex items-center justify-between font-bold text-xs text-slate-850"
            >
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Tugas Sudah Terlewat (Overdue)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-red-50 text-red-800 text-[9px] px-2.5 py-0.5 rounded-full font-bold border border-red-150">
                        {{ $overdueTasks->count() }} Tugas
                    </span>
                    <span class="text-slate-400 transform transition-transform" :class="openOverdue ? 'rotate-180' : ''">&darr;</span>
                </div>
            </button>

            <div x-show="openOverdue" x-collapse class="pt-3 space-y-3 border-t border-slate-100 mt-2">
                @foreach($overdueTasks as $task)
                    <x-task-card :task="$task" />
                @endforeach

                @if($overdueTasks->isEmpty())
                    <p class="text-center py-4 text-slate-400 text-[10px] italic">Tidak ada tugas yang terlewat.</p>
                @endif
            </div>
        </div>

        <!-- SECTION 4: AKAN DATANG (FUTURE) - ACCORDION -->
        <div class="bg-white border rounded-3xl p-4 shadow-sm space-y-2">
            <button 
                type="button" 
                @click="openFuture = !openFuture" 
                class="w-full flex items-center justify-between font-bold text-xs text-slate-850"
            >
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Tugas Akan Datang (Upcoming)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-blue-50 text-blue-800 text-[9px] px-2.5 py-0.5 rounded-full font-bold border border-blue-150">
                        {{ $futureTasks->count() }} Tugas
                    </span>
                    <span class="text-slate-400 transform transition-transform" :class="openFuture ? 'rotate-180' : ''">&darr;</span>
                </div>
            </button>

            <div x-show="openFuture" x-collapse class="pt-3 space-y-3 border-t border-slate-100 mt-2">
                @foreach($futureTasks as $task)
                    <x-task-card :task="$task" />
                @endforeach

                @if($futureTasks->isEmpty())
                    <p class="text-center py-4 text-slate-400 text-[10px] italic">Tidak ada tugas akan datang.</p>
                @endif
            </div>
        </div>

        <!-- SECTION 5: SUDAH DISETUJUI (APPROVED) - ACCORDION -->
        <div class="bg-white border rounded-3xl p-4 shadow-sm space-y-2">
            <button 
                type="button" 
                @click="openApproved = !openApproved" 
                class="w-full flex items-center justify-between font-bold text-xs text-slate-850"
            >
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Tugas Selesai & Disetujui</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-green-50 text-green-800 text-[9px] px-2.5 py-0.5 rounded-full font-bold border border-green-150">
                        {{ $approvedTasks->count() }} Tugas
                    </span>
                    <span class="text-slate-400 transform transition-transform" :class="openApproved ? 'rotate-180' : ''">&darr;</span>
                </div>
            </button>

            <div x-show="openApproved" x-collapse class="pt-3 space-y-3 border-t border-slate-100 mt-2">
                @foreach($approvedTasks as $task)
                    <x-task-card :task="$task" />
                @endforeach

                @if($approvedTasks->isEmpty())
                    <p class="text-center py-4 text-slate-400 text-[10px] italic">Belum ada tugas yang diselesaikan.</p>
                @endif
            </div>
        </div>

    </div>
</x-volunteer-layout>
