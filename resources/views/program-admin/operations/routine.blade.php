<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Kegiatan Rutin Harian</h1>
                <p class="text-xs text-slate-400 mt-1">Jadwalkan kegiatan wajib berkala (e.g. Briefing Pagi, Evaluasi Sore) untuk seluruh peserta.</p>
            </div>
            <button onclick="toggleRoutineModal(true)" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full shadow transition">
                + Tambah Kegiatan Rutin
            </button>
        </div>

        <!-- Routine Activities List -->
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Daftar Kegiatan Rutin</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Nama Kegiatan</th>
                            <th class="p-4">Jam Pelaksanaan</th>
                            <th class="p-4">Syarat Laporan</th>
                            <th class="p-4">Program</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($routines as $rt)
                            <tr>
                                <td class="p-4 font-bold text-slate-900">{{ $rt->name }}</td>
                                <td class="p-4 font-mono">{{ $rt->start_time }} - {{ $rt->end_time }}</td>
                                <td class="p-4 space-x-2">
                                    @if($rt->is_selfie_required)
                                        <span class="bg-blue-50 text-blue-800 border border-blue-150 px-2 py-0.5 rounded text-[10px] font-semibold">📸 Selfie Wajib</span>
                                    @endif
                                    @if($rt->is_notes_required)
                                        <span class="bg-amber-50 text-amber-800 border border-amber-150 px-2 py-0.5 rounded text-[10px] font-semibold">📝 Catatan Wajib</span>
                                    @endif
                                    @if(!$rt->is_selfie_required && !$rt->is_notes_required)
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="p-4 text-gray-500">{{ $rt->program?->name }}</td>
                            </tr>
                        @endforeach
                        @if($routines->isEmpty())
                            <tr><td colspan="4" class="p-8 text-center text-gray-400 italic">Belum ada kegiatan rutin dijadwalkan.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Routine Modal -->
        <div id="routineModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border text-xs">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Jadwalkan Kegiatan Rutin</h3>
                    <button onclick="toggleRoutineModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form method="POST" action="{{ route('program-admin.routines.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Program</label>
                        <select name="program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Kegiatan Rutin</label>
                        <input type="text" name="name" required placeholder="e.g. Briefing Pagi Tim Mangrove" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Jam Mulai</label>
                            <input type="time" name="start_time" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Jam Selesai</label>
                            <input type="time" name="end_time" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-xl space-y-2 border">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Pengaturan & Persyaratan</label>
                        <label class="flex items-center gap-2 cursor-pointer py-1">
                            <input type="checkbox" name="is_selfie_required" value="1" class="rounded text-[#4CAF50] focus:ring-[#4CAF50]">
                            <span>Wajib Upload Foto / Selfie</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer py-1">
                            <input type="checkbox" name="is_notes_required" value="1" class="rounded text-[#4CAF50] focus:ring-[#4CAF50]">
                            <span>Wajib Isi Catatan Kegiatan</span>
                        </label>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="toggleRoutineModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white px-4 py-2.5 rounded-full transition">Jadwalkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleRoutineModal(show) {
            document.getElementById('routineModal').classList.toggle('hidden', !show);
        }
    </script>
</x-program-admin-layout>
