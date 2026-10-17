<x-volunteer-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-3xl border border-gray-150 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900 font-serif">Aktivitas Mandiri & Tambahan</h1>
                <p class="text-xs text-slate-400 mt-1">Laporkan kegiatan bermanfaat yang dilakukan di luar tugas wajib untuk dihitung sebagai jam kontribusi tambahan.</p>
            </div>
            <button onclick="toggleActivityModal(true)" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full shadow transition">
                + Ajukan Aktivitas
            </button>
        </div>

        <!-- History Table -->
        <div class="bg-white border rounded-3xl shadow-sm overflow-hidden text-xs">
            <div class="p-6 border-b">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Histori Pengajuan Aktivitas</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Aktivitas</th>
                            <th class="p-4">Durasi Kerja</th>
                            <th class="p-4">Foto Lampiran</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Catatan Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($activities as $act)
                            <tr>
                                <td class="p-4 font-bold text-slate-900">{{ $act->date->format('d M Y') }}</td>
                                <td class="p-4">
                                    <strong class="text-slate-800">{{ $act->name }}</strong>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $act->description }}</p>
                                </td>
                                <td class="p-4 font-mono font-bold">{{ $act->hours_spent }} Jam</td>
                                <td class="p-4">
                                    @if($act->photo_path)
                                        <a href="{{ $act->photo_path }}" target="_blank">
                                            <img src="{{ $act->photo_path }}" class="w-10 h-10 rounded object-cover border">
                                        </a>
                                    @else
                                        <span class="text-gray-300">No Photo</span>
                                    @endif
                                </td>
                                <td class="p-4 uppercase font-bold text-[10px]">
                                    @if($act->status === 'pending')
                                        <span class="text-amber-500">🟡 Pending</span>
                                    @elseif($act->status === 'approved')
                                        <span class="text-green-600">🟢 Disetujui</span>
                                    @else
                                        <span class="text-red-500">🔴 Ditolak</span>
                                    @endif
                                </td>
                                <td class="p-4 text-slate-400 italic">{{ $act->remarks ?? '-' }}</td>
                            </tr>
                        @endforeach
                        @if($activities->isEmpty())
                            <tr><td colspan="6" class="p-8 text-center text-gray-400 italic">Belum ada aktivitas tambahan diajukan.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Activity Modal -->
        <div id="activityModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border text-xs overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Ajukan Aktivitas Tambahan</h3>
                    <button onclick="toggleActivityModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                @if($assignments->isNotEmpty())
                    <form method="POST" action="{{ route('volunteer.workspace.additional.store') }}" enctype="multipart/form-data" x-data="{ isSubmitting: false }" @submit="isSubmitting = true" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Pilih Program Kerja</label>
                            <select name="program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                                @foreach($assignments as $assign)
                                    @if($assign->program)
                                        <option value="{{ $assign->program->id }}">{{ $assign->program->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Aktivitas</label>
                            <input type="text" name="name" required placeholder="e.g. Membantu kelompok UMKM kemasan" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Deskripsi Kegiatan</label>
                            <textarea name="description" required placeholder="Jelaskan detail apa yang Anda kerjakan..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]" rows="3"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal Kegiatan</label>
                                <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase">Durasi Kerja (Jam)</label>
                                <input type="number" step="0.5" name="hours_spent" required placeholder="e.g. 2" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Unggah Foto Bukti Kerja (Wajib)</label>
                            <x-universal-upload id="additional_activity_photo" name="photo" :programId="$assignments->first()?->program?->id ?? ''" :required="true" />
                        </div>

                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" onclick="toggleActivityModal(false); isSubmitting = false" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-full transition">Cancel</button>
                            <button type="submit" :disabled="isSubmitting" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white px-4 py-2.5 rounded-full transition flex items-center gap-2">
                                <template x-if="isSubmitting">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </template>
                                <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim Aktivitas'"></span>
                            </button>
                        </div>
                    </form>
                @else
                    <p class="text-red-500 font-bold">Anda belum terhubung ke program manapun.</p>
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleActivityModal(show) {
            document.getElementById('activityModal').classList.toggle('hidden', !show);
        }
    </script>
</x-volunteer-layout>
