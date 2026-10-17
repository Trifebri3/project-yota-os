<x-volunteer-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-3xl border border-gray-150 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900 font-serif">Pengaduan & Aspirasi Lapangan</h1>
                <p class="text-xs text-slate-400 mt-1">Laporkan kendala fasilitas, masalah kesehatan/keamanan, atau kirimkan masukan operasional kepada Admin Program.</p>
            </div>
            <button onclick="toggleComplaintModal(true)" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full shadow transition">
                + Kirim Aduan / Ide
            </button>
        </div>

        <!-- History Table -->
        <div class="bg-white border rounded-3xl shadow-sm overflow-hidden text-xs">
            <div class="p-6 border-b">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Histori Aduan & Tanggapan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Kategori & Judul</th>
                            <th class="p-4">Uraian Masalah</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Tanggapan Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($complaints as $comp)
                            <tr>
                                <td class="p-4 font-semibold">{{ $comp->created_at->format('d M Y H:i') }}</td>
                                <td class="p-4">
                                    <span class="bg-red-50 text-red-800 border border-red-150 px-2 py-0.5 rounded text-[9px] font-bold uppercase">
                                        {{ $comp->category }}
                                    </span>
                                    <strong class="block text-slate-800 mt-1">{{ $comp->title }}</strong>
                                </td>
                                <td class="p-4 max-w-xs">{{ $comp->description }}</td>
                                <td class="p-4 uppercase font-bold text-[10px]">
                                    @if($comp->status === 'pending')
                                        <span class="text-amber-500">🟡 Pending</span>
                                    @elseif($comp->status === 'processing')
                                        <span class="text-blue-500">🔵 Diproses</span>
                                    @elseif($comp->status === 'resolved')
                                        <span class="text-green-600">🟢 Selesai</span>
                                    @else
                                        <span class="text-gray-400">🔴 Closed</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($comp->response)
                                        <div class="bg-green-50 text-green-800 border border-green-150 p-2.5 rounded-lg text-[10px]">
                                            <strong>Solusi Admin:</strong> {{ $comp->response }}
                                        </div>
                                    @else
                                        <span class="text-gray-300 italic">Belum ditanggapi</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @if($complaints->isEmpty())
                            <tr><td colspan="5" class="p-8 text-center text-gray-400 italic">Belum ada pengaduan dikirim.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Complaint Modal -->
        <div id="complaintModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border text-xs">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Kirim Pengaduan / Aspirasi</h3>
                    <button onclick="toggleComplaintModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                @if($program)
                    <form method="POST" action="{{ route('volunteer.workspace.complaints.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="program_id" value="{{ $program->id }}">

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Kategori Laporan</label>
                            <select name="category" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                                <option value="facilities">Fasilitas & Konsumsi</option>
                                <option value="security">Keamanan & Konflik Lapangan</option>
                                <option value="health">Kesehatan & Kecelakaan Kerja</option>
                                <option value="administration">Administrasi & Modul</option>
                                <option value="aspiration">Aspirasi / Ide Program</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Subjek / Judul Laporan</label>
                            <input type="text" name="title" required placeholder="e.g. Masalah air bersih di posko utama" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Uraian Detail Masalah</label>
                            <textarea name="description" required placeholder="Jelaskan secara rinci kendala lapangan..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]" rows="4"></textarea>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Foto Bukti Pendukung (Opsional)</label>
                            <x-universal-upload id="complaint_photo" name="photo" :programId="$program?->id ?? ''" />
                        </div>

                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" onclick="toggleComplaintModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-full transition">Cancel</button>
                            <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white px-4 py-2.5 rounded-full transition">Kirim Laporan</button>
                        </div>
                    </form>
                @else
                    <p class="text-red-500 font-bold">Anda belum ditugaskan ke program kerja.</p>
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleComplaintModal(show) {
            document.getElementById('complaintModal').classList.toggle('hidden', !show);
        }
    </script>
</x-volunteer-layout>
