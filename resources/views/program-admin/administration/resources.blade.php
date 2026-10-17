<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Resource Center & SOP</h1>
                <p class="text-xs text-slate-400 mt-1">Unggah buku panduan, modul, template laporan, dan surat tugas untuk dibagikan secara Umum atau Khusus ke Relawan.</p>
            </div>
            <button onclick="toggleResourceModal(true)" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full shadow transition">
                + Unggah Resource
            </button>
        </div>

        <!-- Resources list -->
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden text-xs">
            <div class="p-6 border-b">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Daftar Panduan & Surat Didistribusikan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Nama Dokumen</th>
                            <th class="p-4">Tipe & Format</th>
                            <th class="p-4">Metode Distribusi</th>
                            <th class="p-4">Ditetapkan Ke</th>
                            <th class="p-4">Download</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($resources as $res)
                            <tr>
                                <td class="p-4 font-bold text-slate-900">{{ $res->name }}</td>
                                <td class="p-4 uppercase font-semibold text-slate-500">{{ $res->type }}</td>
                                <td class="p-4 uppercase text-[10px] font-bold">
                                    @if($res->distribution === 'general')
                                        <span class="text-green-600">🟢 Umum (Semua)</span>
                                    @else
                                        <span class="text-blue-500">🔵 Khusus (Individu)</span>
                                    @endif
                                </td>
                                <td class="p-4 text-slate-500">{{ $res->assignedUser?->name ?? 'Semua Peserta' }}</td>
                                <td class="p-4 flex items-center gap-3">
                                    <a href="{{ $res->file_path }}" download class="text-[#4CAF50] font-bold hover:underline">Download</a>
                                    <form method="POST" action="{{ route('program-admin.resources.delete', $res->id) }}" onsubmit="return confirm('Hapus resource ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 font-bold hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @if($resources->isEmpty())
                            <tr><td colspan="5" class="p-8 text-center text-gray-400 italic">Belum ada resource diunggah.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Resource Modal -->
        <div id="resourceModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border text-xs">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Unggah Resource Baru</h3>
                    <button onclick="toggleResourceModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form method="POST" action="{{ route('program-admin.resources.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Hubungkan Ke Program</label>
                        <select name="program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Resource / Dokumen</label>
                        <input type="text" name="name" required placeholder="e.g. Buku Panduan Mangrove v1" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Jenis Dokumen</label>
                            <select name="type" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                                <option value="guide">Buku Panduan</option>
                                <option value="SOP">SOP Operasional</option>
                                <option value="template">Template Laporan</option>
                                <option value="letter">Surat Tugas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">File Pendukung</label>
                            <input type="file" name="file" required class="w-full mt-1">
                        </div>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-xl space-y-3 border" x-data="{ dist: 'general' }">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Metode Distribusi</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1 cursor-pointer">
                                <input type="radio" name="distribution" value="general" x-model="dist" checked class="text-[#4CAF50] focus:ring-[#4CAF50]">
                                <span>Umum (Semua)</span>
                            </label>
                            <label class="flex items-center gap-1 cursor-pointer">
                                <input type="radio" name="distribution" value="specific" x-model="dist" class="text-[#4CAF50] focus:ring-[#4CAF50]">
                                <span>Khusus (Individu)</span>
                            </label>
                        </div>

                        <div x-show="dist === 'specific'" class="mt-2 animate-fade-in">
                            <label class="block text-[9px] font-bold text-slate-400 uppercase">Pilih Relawan Penerima</label>
                            <select name="assigned_user_id" class="w-full mt-1 px-3 py-2 border rounded-xl bg-white">
                                <option value="">-- Pilih Relawan --</option>
                                @foreach($users as $usr)
                                    <option value="{{ $usr->id }}">{{ $usr->name }} ({{ $usr->role }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="toggleResourceModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white px-4 py-2.5 rounded-full transition">Bagikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleResourceModal(show) {
            document.getElementById('resourceModal').classList.toggle('hidden', !show);
        }
    </script>
</x-program-admin-layout>
