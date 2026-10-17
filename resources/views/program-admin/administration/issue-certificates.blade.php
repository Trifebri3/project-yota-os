<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Piagam & Sertifikat Peserta</h1>
                <p class="text-xs text-slate-400 mt-1">Terbitkan dan kirimkan piagam penghargaan, sertifikat kompetensi, atau surat keterangan kerja relawan.</p>
            </div>
            <button onclick="toggleDocModal(true)" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full shadow transition">
                + Terbitkan Dokumen
            </button>
        </div>

        <!-- Documents list -->
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden text-xs">
            <div class="p-6 border-b">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Daftar Dokumen Diterbitkan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Relawan Penerima</th>
                            <th class="p-4">Nama Dokumen</th>
                            <th class="p-4">Tipe Dokumen</th>
                            <th class="p-4">Program Acuan</th>
                            <th class="p-4">Tanggal Terbit</th>
                            <th class="p-4">File</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($documents as $doc)
                            <tr>
                                <td class="p-4 font-bold text-slate-900">{{ $doc->user?->name }}</td>
                                <td class="p-4">{{ $doc->name }}</td>
                                <td class="p-4 uppercase font-semibold text-slate-500">{{ $doc->type }}</td>
                                <td class="p-4 text-slate-400">{{ $doc->program?->name }}</td>
                                <td class="p-4 text-slate-400">{{ $doc->created_at->format('d M Y') }}</td>
                                <td class="p-4 flex items-center gap-3">
                                    <a href="{{ $doc->file_path }}" target="_blank" class="text-[#4CAF50] font-bold hover:underline">Buka</a>
                                    <form method="POST" action="{{ route('program-admin.participant-documents.delete', $doc->id) }}" onsubmit="return confirm('Hapus dokumen/sertifikat ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 font-bold hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @if($documents->isEmpty())
                            <tr><td colspan="6" class="p-8 text-center text-gray-400 italic">Belum ada piagam / dokumen diterbitkan.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Issue Document Modal -->
        <div id="docModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border text-xs">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Terbitkan Dokumen Baru</h3>
                    <button onclick="toggleDocModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form method="POST" action="{{ route('program-admin.participant-documents.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Program Acuan</label>
                        <select name="program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Pilih Relawan Penerima</label>
                        <select name="user_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Piagam / Dokumen</label>
                        <input type="text" name="name" required placeholder="e.g. Sertifikat Best Volunteer Purbayani" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Jenis Dokumen</label>
                            <select name="type" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                                <option value="certificate">Sertifikat Resmi</option>
                                <option value="piagam">Piagam Penghargaan</option>
                                <option value="letter">Surat Keterangan</option>
                                <option value="rekomendasi">Surat Rekomendasi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">File Dokumen</label>
                            <input type="file" name="file" required class="w-full mt-1">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="toggleDocModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white px-4 py-2.5 rounded-full transition">Terbitkan Dokumen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleDocModal(show) {
            document.getElementById('docModal').classList.toggle('hidden', !show);
        }
    </script>
</x-program-admin-layout>
