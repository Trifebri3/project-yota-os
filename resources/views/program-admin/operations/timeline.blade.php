<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Timeline & Calendar Agendas</h1>
                <p class="text-xs text-slate-400 mt-1">Kelola agenda kegiatan operasional harian yang akan otomatis tampil di Kalender Program & Dashboard Peserta.</p>
            </div>
            <button onclick="toggleAgendaModal(true)" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full shadow transition">
                + Tambah Agenda Baru
            </button>
        </div>

        @php
            $programs = App\Models\Program::with('timelines')->get();
            $agendas = App\Models\Agenda::with('program')->orderBy('date', 'asc')->get();
        @endphp

        <!-- Agendas List -->
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Daftar Agenda Terjadwal</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Nama Agenda</th>
                            <th class="p-4">Lokasi & PJ</th>
                            <th class="p-4">Link Meeting</th>
                            <th class="p-4">Program</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($agendas as $ag)
                            <tr>
                                <td class="p-4 font-bold text-slate-900">{{ $ag->date->format('d M Y') }}</td>
                                <td class="p-4">
                                    <strong class="text-slate-800">{{ $ag->name }}</strong>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $ag->description }}</p>
                                </td>
                                <td class="p-4">
                                    <span class="block font-semibold">{{ $ag->location ?? '-' }}</span>
                                    <span class="block text-[10px] text-gray-400">PJ: {{ $ag->penanggung_jawab ?? '-' }}</span>
                                </td>
                                <td class="p-4">
                                    @if($ag->meeting_link)
                                        <a href="{{ $ag->meeting_link }}" target="_blank" class="text-blue-500 hover:underline font-bold">Gabung Meeting &rarr;</a>
                                    @else
                                        <span class="text-gray-300">Offline / Tidak ada</span>
                                    @endif
                                </td>
                                <td class="p-4 text-gray-500">{{ $ag->program?->name }}</td>
                            </tr>
                        @endforeach
                        @if($agendas->isEmpty())
                            <tr><td colspan="5" class="p-8 text-center text-gray-400 italic">Belum ada agenda kegiatan dijadwalkan.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Agenda Modal -->
        <div id="agendaModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border text-xs overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Tambah Agenda Timeline Baru</h3>
                    <button onclick="toggleAgendaModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form method="POST" action="{{ route('program-admin.agendas.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Hubungkan Ke Program</label>
                        <select name="program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Agenda Kegiatan</label>
                            <input type="text" name="name" required placeholder="e.g. Survey Pesisir" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal Kegiatan</label>
                            <input type="date" name="date" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Deskripsi Agenda</label>
                        <textarea name="description" placeholder="Penjelasan agenda..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]" rows="3"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Lokasi Kegiatan</label>
                            <input type="text" name="location" placeholder="e.g. Balai Desa Purbayani" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Penanggung Jawab (PJ)</label>
                            <input type="text" name="penanggung_jawab" placeholder="Nama koordinator..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Meeting Link (Online Meeting / Zoom)</label>
                        <input type="url" name="meeting_link" placeholder="https://zoom.us/j/..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Lampiran Acara (Foto/Poster)</label>
                            <input type="file" name="attachment" accept="image/*" class="w-full mt-1">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Dokumen Pendukung (PDF)</label>
                            <input type="file" name="document" accept="application/pdf" class="w-full mt-1">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="toggleAgendaModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white px-4 py-2.5 rounded-full transition">Simpan Agenda</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleAgendaModal(show) {
            document.getElementById('agendaModal').classList.toggle('hidden', !show);
        }
    </script>
</x-program-admin-layout>
