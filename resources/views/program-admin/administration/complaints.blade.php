<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900 font-serif">Pengaduan Relawan & Peserta</h1>
                <p class="text-xs text-slate-400 mt-1">Review kendala lapangan, aduan fasilitas, konflik relawan, masalah kesehatan, dan keamanan.</p>
            </div>
        </div>

        <!-- Complaints list -->
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden text-xs">
            <div class="p-6 border-b">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Laporan Pengaduan Masuk</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Tanggal & Pengirim</th>
                            <th class="p-4">Kategori & Judul</th>
                            <th class="p-4">Uraian Masalah</th>
                            <th class="p-4">Foto Bukti</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($complaints as $comp)
                            <tr>
                                <td class="p-4">
                                    <strong class="text-slate-800">{{ $comp->user?->name }}</strong>
                                    <span class="block text-[10px] text-gray-400 mt-0.5">{{ $comp->created_at->format('d M Y H:i') }}</span>
                                </td>
                                <td class="p-4">
                                    <span class="bg-red-50 text-red-800 border border-red-150 px-2 py-0.5 rounded text-[10px] font-bold uppercase">
                                        {{ $comp->category }}
                                    </span>
                                    <strong class="block text-slate-800 mt-1">{{ $comp->title }}</strong>
                                </td>
                                <td class="p-4">
                                    <p class="max-w-xs">{{ $comp->description }}</p>
                                    @if($comp->response)
                                        <p class="mt-2 text-green-700 bg-green-50 p-2 rounded border border-green-150 text-[10px]">
                                            <strong>Admin Response:</strong> {{ $comp->response }}
                                        </p>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($comp->photo_path)
                                        <a href="{{ $comp->photo_path }}" target="_blank">
                                            <img src="{{ $comp->photo_path }}" class="w-10 h-10 rounded object-cover border">
                                        </a>
                                    @else
                                        <span class="text-gray-300">No Photo</span>
                                    @endif
                                </td>
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
                                    <button onclick="openRespondModal('{{ $comp->id }}', '{{ $comp->status }}', '{{ $comp->response }}')" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white font-bold px-3 py-1.5 rounded-xl transition">
                                        Tanggapi
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        @if($complaints->isEmpty())
                            <tr><td colspan="6" class="p-8 text-center text-gray-400 italic">Tidak ada laporan pengaduan masuk.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Respond Modal -->
        <div id="respondModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl border text-xs">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Tanggapi Pengaduan</h3>
                    <button onclick="toggleRespondModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form id="respond-form" method="POST" action="" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Status Pengaduan</label>
                        <select id="comp-status" name="status" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                            <option value="processing">Sedang Diproses (Processing)</option>
                            <option value="resolved">Selesai Ditangani (Resolved)</option>
                            <option value="closed">Tutup Tiket (Closed)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggapan / Solusi Resmi</label>
                        <textarea id="comp-response" name="response" required placeholder="Tuliskan tindakan/tanggapan..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50" rows="4"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="toggleRespondModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white px-4 py-2.5 rounded-full transition">Kirim Solusi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleRespondModal(show) {
            document.getElementById('respondModal').classList.toggle('hidden', !show);
        }

        function openRespondModal(compId, status, responseText) {
            document.getElementById('respond-form').action = `/program-admin/complaints/respond/${compId}`;
            document.getElementById('comp-status').value = status;
            document.getElementById('comp-response').value = responseText || '';
            toggleRespondModal(true);
        }
    </script>
</x-program-admin-layout>
