<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Validasi Absensi Luar Zona</h1>
                <p class="text-xs text-slate-400 mt-1">Daftar absensi relawan yang dilakukan di luar radius polygon geofence dan membutuhkan persetujuan.</p>
            </div>
        </div>

        <!-- Pending Validations List -->
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Relawan</th>
                            <th class="p-4">Kegiatan</th>
                            <th class="p-4">Waktu Check In</th>
                            <th class="p-4">Foto Selfie</th>
                            <th class="p-4">Alasan Luar Zona</th>
                            <th class="p-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($pendingAttendances as $att)
                            <tr>
                                <td class="p-4 font-semibold">{{ $att->user?->name }}</td>
                                <td class="p-4 font-medium text-slate-650">{{ $att->activity?->name }}</td>
                                <td class="p-4 text-gray-400">{{ $att->check_in_time?->format('d M Y H:i') }}</td>
                                <td class="p-4">
                                    @if($att->check_in_selfie)
                                        <a href="{{ $att->check_in_selfie }}" target="_blank" class="block border rounded-lg p-0.5 bg-slate-50 w-12 h-12 hover:scale-105 transition">
                                            <img src="{{ $att->check_in_selfie }}" alt="Selfie" class="w-full h-full object-cover rounded-md">
                                        </a>
                                    @else
                                        <span class="text-gray-300">No Photo</span>
                                    @endif
                                </td>
                                <td class="p-4 text-red-650 font-medium italic max-w-xs">{{ $att->reason }}</td>
                                <td class="p-4">
                                    <form method="POST" action="{{ route('program-admin.attendance.approve', $att->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-[10px] font-bold px-3 py-1.5 rounded-full transition shadow-sm">
                                            Approve Absensi
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @if($pendingAttendances->isEmpty())
                            <tr><td colspan="6" class="p-8 text-center text-gray-400">Tidak ada absensi luar zona yang memerlukan verifikasi.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-program-admin-layout>
