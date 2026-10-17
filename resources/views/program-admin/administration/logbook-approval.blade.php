<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Verifikasi Logbook Relawan</h1>
                <p class="text-xs text-slate-400 mt-1">Daftar laporan logbook harian relawan yang memerlukan persetujuan jam kerja lapangan.</p>
            </div>
        </div>

        <!-- Logbooks Table -->
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Relawan</th>
                            <th class="p-4">Program Kerja</th>
                            <th class="p-4">Aktivitas</th>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Durasi Kerja</th>
                            <th class="p-4">Bukti Foto</th>
                            <th class="p-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($logbooks as $lb)
                            <tr>
                                <td class="p-4 font-semibold">{{ $lb->user?->name }}</td>
                                <td class="p-4 text-gray-500">{{ $lb->program?->name }}</td>
                                <td class="p-4 font-medium max-w-xs">
                                    <strong class="text-slate-800">{{ $lb->title }}</strong>
                                    <p class="text-gray-400 font-normal mt-1 leading-relaxed">{{ $lb->content }}</p>
                                </td>
                                <td class="p-4 text-gray-400">{{ $lb->date?->format('d M Y') }}</td>
                                <td class="p-4 font-bold text-slate-750">{{ $lb->hours_spent }} jam</td>
                                <td class="p-4">
                                    @if($lb->photo_path)
                                        <a href="{{ $lb->photo_path ? (str_starts_with($lb->photo_path, 'http') ? $lb->photo_path : (str_starts_with($lb->photo_path, '/storage/') ? $lb->photo_path : (str_starts_with($lb->photo_path, 'storage/') ? '/' . $lb->photo_path : '/storage/' . ltrim($lb->photo_path, '/')))) : '' }}" target="_blank">
                                            <img src="{{ $lb->photo_path ? (str_starts_with($lb->photo_path, 'http') ? $lb->photo_path : (str_starts_with($lb->photo_path, '/storage/') ? $lb->photo_path : (str_starts_with($lb->photo_path, 'storage/') ? '/' . $lb->photo_path : '/storage/' . ltrim($lb->photo_path, '/')))) : '' }}" class="w-12 h-12 rounded-lg object-cover border">
                                        </a>
                                    @else
                                        <span class="text-gray-300 italic">Tanpa Foto</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <form method="POST" action="{{ route('program-admin.logbooks.approve', $lb->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-[10px] font-bold px-3 py-1.5 rounded-full transition shadow-sm">
                                            Setujui
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @if($logbooks->isEmpty())
                            <tr><td colspan="7" class="p-8 text-center text-gray-400">Belum ada logbook baru yang memerlukan verifikasi.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-program-admin-layout>
