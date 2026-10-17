<x-volunteer-layout>
    <div class="space-y-6 animate-fade-in">
        <!-- Header -->
        <div>
            <a href="{{ route('volunteer.attendance.index') }}" class="text-xs font-bold text-[#4CAF50] hover:underline">&larr; Kembali</a>
            <h1 class="text-lg font-bold text-slate-900 mt-2">Riwayat Absensi</h1>
            <p class="text-xs text-slate-400 mt-1">Daftar lengkap kehadiran dan validasi lokasi Anda.</p>
        </div>

        <!-- History List -->
        <div class="space-y-3">
            @foreach($attendances as $att)
                <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-3">
                    <div class="flex justify-between items-start border-b pb-2">
                        <div>
                            <strong class="text-xs text-slate-800">{{ $att->activity?->name }}</strong>
                            <span class="block text-[9px] text-gray-400 mt-0.5">{{ $att->created_at->format('d M Y') }}</span>
                        </div>
                        <span class="text-[9px] font-bold px-2 py-0.5 rounded uppercase {{ $att->check_in_status === 'inside_zone' ? 'bg-green-100 text-green-950' : 'bg-red-100 text-red-950' }}">
                            {{ $att->check_in_status }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-[10px] text-gray-500">
                        <div>
                            <span class="block font-semibold">Check-In:</span>
                            <span class="text-slate-800 font-bold">{{ $att->check_in_time?->format('H:i') ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block font-semibold">Check-Out:</span>
                            <span class="text-slate-800 font-bold">{{ $att->check_out_time?->format('H:i') ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($attendances->isEmpty())
                <div class="text-center py-12 bg-white border border-dashed rounded-3xl text-gray-400 text-xs font-medium">
                    Belum ada riwayat absensi terekam.
                </div>
            @endif
        </div>
    </div>
</x-volunteer-layout>
