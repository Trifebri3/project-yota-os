<x-volunteer-layout>
    <div class="space-y-6 animate-fade-in">
        <!-- Header -->
        <div>
            <h1 class="text-lg font-bold text-slate-900">Pusat Absensi Geofence</h1>
            <p class="text-xs text-slate-400 mt-1">Pilih kegiatan hari ini untuk melakukan Check-In atau Check-Out absensi lokasi.</p>
        </div>

        <!-- Today's check-in status banner -->
        @if($todayAttendance)
            <div class="bg-lime-50 text-lime-900 border border-lime-200 p-4 rounded-3xl text-xs space-y-2">
                <span class="block font-bold text-[10px] text-lime-800 uppercase tracking-widest">Status Absen Aktif</span>
                <div class="flex justify-between items-center">
                    <span>Check-In Jam: <strong>{{ $todayAttendance->check_in_time?->format('H:i') }}</strong></span>
                    @if(!$todayAttendance->check_out_time)
                        <a href="{{ route('volunteer.attendance.check-out', $todayAttendance->id) }}" class="bg-red-500 hover:bg-red-600 text-white text-[10px] font-bold px-4 py-2 rounded-full shadow transition">Check Out Sekarang</a>
                    @else
                        <span class="text-green-700 font-extrabold uppercase text-[10px]">Telah Selesai</span>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Absensi Harian Umum (General)</h3>
                    <p class="text-xs text-gray-400 mt-1">Lakukan absensi masuk harian umum untuk mencatat kehadiran harian Anda.</p>
                </div>
                <a href="{{ route('volunteer.attendance.check-in-daily') }}" class="block text-center bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-3 rounded-2xl shadow transition">
                    Check In Harian Umum &rarr;
                </a>
            </div>
        @endif

        <!-- Activities List -->
        <div class="space-y-3">
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Daftar Kegiatan Lapangan</span>
            @foreach($activities as $act)
                <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">{{ $act->name }}</h3>
                        <p class="text-xs text-gray-400 mt-1">Geofence Zone: {{ $act->zone?->name ?? 'Global (Tanpa Geofence)' }}</p>
                    </div>

                    @if(!$todayAttendance)
                        <a href="{{ route('volunteer.attendance.check-in', $act->id) }}" class="block text-center bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold py-3 rounded-2xl shadow transition">
                            Check In Di Sini &rarr;
                        </a>
                    @elseif($todayAttendance->activity_id !== $act->id)
                        <button disabled class="w-full bg-slate-100 text-slate-400 text-xs font-bold py-3 rounded-2xl cursor-not-allowed">
                            Anda Sedang Aktif di Kegiatan Lain
                        </button>
                    @endif
                </div>
            @endforeach

            @if($activities->isEmpty())
                <div class="text-center py-12 bg-white border border-dashed rounded-3xl text-gray-400 text-xs font-medium">
                    Tidak ada kegiatan lapangan aktif hari ini.
                </div>
            @endif
        </div>

        <div class="text-center pt-4">
            <a href="{{ route('volunteer.attendance.history') }}" class="text-xs font-bold text-[#4CAF50] hover:underline">Lihat Riwayat Absensi &rarr;</a>
        </div>
    </div>
</x-volunteer-layout>
