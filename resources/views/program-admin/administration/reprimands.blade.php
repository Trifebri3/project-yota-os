<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900 font-serif">Teguran & Pembinaan Peserta</h1>
                <p class="text-xs text-slate-400 mt-1">Dokumentasikan tindakan disiplin (Teguran Ringan, Sedang, atau Berat) terhadap relawan bermasalah.</p>
            </div>
            <button onclick="toggleReprimandModal(true)" class="bg-red-600 hover:bg-red-750 text-white text-xs font-bold px-4 py-2.5 rounded-full shadow transition">
                + Kirim Surat Teguran
            </button>
        </div>

        <!-- Reprimands list -->
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden text-xs">
            <div class="p-6 border-b">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Histori Teguran Resmi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Peserta Terkait</th>
                            <th class="p-4">Tingkat Teguran</th>
                            <th class="p-4">Alasan Teguran</th>
                            <th class="p-4">Bukti & Aksi</th>
                            <th class="p-4">Program</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($reprimands as $rep)
                            <tr>
                                <td class="p-4 font-bold">{{ $rep->date->format('d M Y') }}</td>
                                <td class="p-4 font-semibold text-slate-900">{{ $rep->user?->name }}</td>
                                <td class="p-4 uppercase font-bold text-[10px]">
                                    @if($rep->level === 'light')
                                        <span class="bg-amber-50 text-amber-800 border border-amber-150 px-2 py-0.5 rounded">Teguran Ringan</span>
                                    @elseif($rep->level === 'medium')
                                        <span class="bg-orange-50 text-orange-850 border border-orange-150 px-2 py-0.5 rounded">Teguran Sedang</span>
                                    @else
                                        <span class="bg-red-50 text-red-650 border border-red-150 px-2 py-0.5 rounded">Teguran Berat</span>
                                    @endif
                                </td>
                                <td class="p-4">{{ $rep->reason }}</td>
                                <td class="p-4 flex items-center gap-3">
                                    @if($rep->proof_path)
                                        <a href="{{ $rep->proof_path }}" target="_blank" class="text-red-550 font-bold hover:underline">Lihat Lampiran</a>
                                    @else
                                        <span class="text-gray-300">Tidak ada</span>
                                    @endif
                                    <form method="POST" action="{{ route('program-admin.reprimands.delete', $rep->id) }}" onsubmit="return confirm('Hapus surat teguran ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 font-bold hover:underline">Hapus</button>
                                    </form>
                                </td>
                                <td class="p-4 text-gray-500">{{ $rep->program?->name }}</td>
                            </tr>
                        @endforeach
                        @if($reprimands->isEmpty())
                            <tr><td colspan="6" class="p-8 text-center text-gray-400 italic">Belum ada tindakan teguran tercatat.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Reprimand Modal -->
        <div id="reprimandModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border text-xs">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Buat Surat Teguran Resmi</h3>
                    <button onclick="toggleReprimandModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form method="POST" action="{{ route('program-admin.reprimands.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Program Terkait</label>
                        <select name="program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-red-500">
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Pilih Relawan Terkait</label>
                        <select name="user_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-red-500">
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tingkat Pelanggaran</label>
                            <select name="level" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                                <option value="light">Teguran Ringan</option>
                                <option value="medium">Teguran Sedang</option>
                                <option value="heavy">Teguran Berat</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Alasan & Bukti Pelanggaran</label>
                        <textarea name="reason" required placeholder="Tuliskan uraian pelanggaran..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-red-500" rows="3"></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Lampiran File Bukti (Foto/PDF)</label>
                        <input type="file" name="proof" class="w-full mt-1">
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="toggleReprimandModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" class="bg-red-600 hover:bg-red-750 text-white px-4 py-2.5 rounded-full transition">Kirim Teguran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleReprimandModal(show) {
            document.getElementById('reprimandModal').classList.toggle('hidden', !show);
        }
    </script>
</x-program-admin-layout>
