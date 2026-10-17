<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900 font-serif">Daily Situation Reports</h1>
                <p class="text-xs text-slate-400 mt-1">Buat, arsipkan, dan pantau log harian situasi lapangan, manajemen risiko, kendala desa, dan rekomendasi program.</p>
            </div>
            <button onclick="toggleReportModal(true)" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full shadow transition">
                + Buat Laporan Harian
            </button>
        </div>

        <!-- Reports list -->
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden text-xs">
            <div class="p-6 border-b">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Histori Laporan Lapangan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Masalah / Kendala</th>
                            <th class="p-4">Risiko & Hambatan</th>
                            <th class="p-4">Rekomendasi Tindakan</th>
                            <th class="p-4">Program</th>
                            <th class="p-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($reports as $rep)
                            <tr>
                                <td class="p-4 font-bold text-slate-900">{{ $rep->date->format('d M Y') }}</td>
                                <td class="p-4">{{ $rep->issues ?? 'Aman / Tidak ada' }}</td>
                                <td class="p-4">
                                    <span class="block"><strong>Risiko:</strong> {{ $rep->risks ?? '-' }}</span>
                                    <span class="block text-gray-400 mt-0.5"><strong>Hambatan:</strong> {{ $rep->roadblocks ?? '-' }}</span>
                                </td>
                                <td class="p-4 text-slate-800 font-medium">{{ $rep->recommendations ?? '-' }}</td>
                                <td class="p-4 text-gray-500">{{ $rep->program?->name }}</td>
                                <td class="p-4">
                                    <form method="POST" action="{{ route('program-admin.daily-reports.delete', $rep->id) }}" onsubmit="return confirm('Hapus laporan harian ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 font-bold hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @if($reports->isEmpty())
                            <tr><td colspan="6" class="p-8 text-center text-gray-400 italic">Belum ada laporan harian lapangan diarsipkan.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Report Modal -->
        <div id="reportModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border text-xs overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Buat Laporan Harian Situasi</h3>
                    <button onclick="toggleReportModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form method="POST" action="{{ route('program-admin.daily-reports.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Pilih Program</label>
                        <select name="program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal Laporan</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Permasalahan / Kendala Lapangan</label>
                        <textarea name="issues" placeholder="Uraian kendala desa, peserta..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]" rows="2"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Analisis Risiko</label>
                            <textarea name="risks" placeholder="Risiko cuaca, sosial..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50" rows="2"></textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Hambatan Operasional</label>
                            <textarea name="roadblocks" placeholder="Logistik, perizinan..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50" rows="2"></textarea>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Rekomendasi & Tindakan Korektif</label>
                        <textarea name="recommendations" placeholder="Langkah mitigasi lanjut..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]" rows="3"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="toggleReportModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white px-4 py-2.5 rounded-full transition">Simpan Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleReportModal(show) {
            document.getElementById('reportModal').classList.toggle('hidden', !show);
        }
    </script>
</x-program-admin-layout>
