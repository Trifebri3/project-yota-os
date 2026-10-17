<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Verifikasi Aktivitas Tambahan</h1>
                <p class="text-xs text-slate-400 mt-1">Review dan setujui log aktivitas mandiri yang diajukan oleh peserta di luar penugasan resmi.</p>
            </div>
        </div>

        <!-- Additional Logs list -->
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden text-xs">
            <div class="p-6 border-b">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Aktivitas Tambahan Menunggu Review</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Tanggal & Peserta</th>
                            <th class="p-4">Aktivitas</th>
                            <th class="p-4">Durasi</th>
                            <th class="p-4">Foto Bukti</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($activities as $act)
                            <tr>
                                <td class="p-4">
                                    <strong class="text-slate-800">{{ $act->user?->name }}</strong>
                                    <span class="block text-[10px] text-gray-400 mt-0.5">{{ $act->date->format('d M Y') }}</span>
                                </td>
                                <td class="p-4">
                                    <strong class="text-slate-800">{{ $act->name }}</strong>
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $act->description }}</p>
                                </td>
                                <td class="p-4 font-mono font-bold">{{ $act->hours_spent }} Jam</td>
                                <td class="p-4">
                                    @if($act->photo_path)
                                        <a href="{{ $act->photo_path }}" target="_blank">
                                            <img src="{{ $act->photo_path }}" class="w-10 h-10 rounded object-cover border hover:scale-105 transition">
                                        </a>
                                    @else
                                        <span class="text-gray-300">Tanpa Foto</span>
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
                                <td class="p-4">
                                    @if($act->status === 'pending')
                                        <div class="flex gap-2">
                                            <form method="POST" action="{{ route('program-admin.additional-activity.approve', $act->id) }}">
                                                @csrf
                                                <button type="submit" class="bg-green-50 hover:bg-green-150 text-green-700 font-bold px-2.5 py-1.5 rounded-lg">Setujui</button>
                                            </form>
                                            <button onclick="openRejectModal('{{ $act->id }}')" class="bg-red-50 hover:bg-red-150 text-red-650 font-bold px-2.5 py-1.5 rounded-lg">Tolak</button>
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">No Action</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @if($activities->isEmpty())
                            <tr><td colspan="6" class="p-8 text-center text-gray-400 italic">Belum ada pengajuan aktivitas tambahan.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reject Modal -->
        <div id="rejectModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl border text-xs">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Tolak Aktivitas Tambahan</h3>
                    <button onclick="toggleRejectModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form id="reject-form" method="POST" action="" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Alasan Penolakan</label>
                        <textarea name="remarks" required placeholder="Tulis alasan log ditolak..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]" rows="3"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="toggleRejectModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" class="bg-red-600 hover:bg-red-750 text-white px-4 py-2.5 rounded-full transition">Tolak Log</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleRejectModal(show) {
            document.getElementById('rejectModal').classList.toggle('hidden', !show);
        }

        function openRejectModal(actId) {
            document.getElementById('reject-form').action = `/program-admin/additional-activity/reject/${actId}`;
            toggleRejectModal(true);
        }
    </script>
</x-program-admin-layout>
