<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in" x-data="{ tab: 'inventory' }">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Manajemen Fasilitas & Atribut</h1>
                <p class="text-xs text-slate-400 mt-1">Monitoring inventaris kaos, topi, modul, ID Card, dan status penyerahan kepada masing-masing peserta.</p>
            </div>
            <button onclick="toggleFacilityModal(true)" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full shadow transition">
                + Tambah Item Atribut
            </button>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex border-b border-gray-250 text-xs">
            <button @click="tab = 'inventory'" :class="tab === 'inventory' ? 'border-[#4CAF50] text-[#4CAF50] font-bold border-b-2' : 'text-gray-400'" class="px-6 py-2">
                Daftar Inventaris
            </button>
            <button @click="tab = 'allocation'" :class="tab === 'allocation' ? 'border-[#4CAF50] text-[#4CAF50] font-bold border-b-2' : 'text-gray-400'" class="px-6 py-2">
                Status Penyerahan
            </button>
        </div>

        <!-- Tab Content: Inventory -->
        <div x-show="tab === 'inventory'" class="bg-white border rounded-2xl shadow-sm overflow-hidden text-xs">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                        <th class="p-4">Nama Fasilitas / Atribut</th>
                        <th class="p-4">Deskripsi / Spesifikasi</th>
                        <th class="p-4">Program</th>
                        <th class="p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-slate-700">
                    @foreach($facilities as $fac)
                        <tr>
                            <td class="p-4 font-bold text-slate-900">{{ $fac->name }}</td>
                            <td class="p-4 text-gray-400">{{ $fac->description ?? 'Tidak ada spesifikasi khusus' }}</td>
                            <td class="p-4 text-gray-500">{{ $fac->program?->name }}</td>
                            <td class="p-4">
                                <form method="POST" action="{{ route('program-admin.facilities.delete', $fac->id) }}" onsubmit="return confirm('Hapus fasilitas/atribut ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 font-bold hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if($facilities->isEmpty())
                        <tr><td colspan="4" class="p-8 text-center text-gray-400 italic">Belum ada fasilitas terdaftar.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Tab Content: Allocation -->
        <div x-show="tab === 'allocation'" class="space-y-6">
            <!-- Allocate Facility status Form -->
            <form method="POST" action="{{ route('program-admin.facilities.allocate') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end text-xs bg-slate-50 border p-5 rounded-2xl">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Pilih Penerima</label>
                    <select name="user_id" required class="w-full mt-1 px-3 py-2 border rounded-xl bg-white focus:ring-1 focus:ring-[#4CAF50]">
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Pilih Fasilitas</label>
                    <select name="facility_id" required class="w-full mt-1 px-3 py-2 border rounded-xl bg-white focus:ring-1 focus:ring-[#4CAF50]">
                        @foreach($facilities as $f)
                            <option value="{{ $f->id }}">{{ $f->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Status Penyerahan</label>
                    <select name="status" required class="w-full mt-1 px-3 py-2 border rounded-xl bg-white focus:ring-1 focus:ring-[#4CAF50]">
                        <option value="pending">Belum Diberikan</option>
                        <option value="given">Sudah Diberikan</option>
                    </select>
                </div>
                <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white py-2 rounded-xl font-bold transition">Update Status</button>
            </form>

            <!-- Allocation Table -->
            <div class="bg-white border rounded-2xl shadow-sm overflow-hidden text-xs">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Peserta / Relawan</th>
                            <th class="p-4">Atribut</th>
                            <th class="p-4">Status Penyerahan</th>
                            <th class="p-4">Tanggal Update</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($allocations as $al)
                            <tr>
                                <td class="p-4 font-bold text-slate-900">{{ $al->user?->name }}</td>
                                <td class="p-4 text-gray-500">{{ $al->facility?->name }}</td>
                                <td class="p-4 uppercase font-bold text-[10px]">
                                    @if($al->status === 'given')
                                        <span class="text-green-600">🟢 Sudah Diberikan</span>
                                    @else
                                        <span class="text-red-500">🔴 Belum Diberikan</span>
                                    @endif
                                </td>
                                <td class="p-4 text-gray-400">{{ $al->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endforeach
                        @if($allocations->isEmpty())
                            <tr><td colspan="4" class="p-8 text-center text-gray-400 italic">Belum ada data distribusi dicatat.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Facility Modal -->
        <div id="facilityModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl border text-xs">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Daftarkan Fasilitas Baru</h3>
                    <button onclick="toggleFacilityModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form method="POST" action="{{ route('program-admin.facilities.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Program Kerja</label>
                        <select name="program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Atribut / Fasilitas</label>
                        <input type="text" name="name" required placeholder="e.g. Kaos Polo Hijau Yota" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Deskripsi singkat</label>
                        <textarea name="description" placeholder="Ukuran, bahan, dll..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]" rows="3"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="toggleFacilityModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white px-4 py-2.5 rounded-full transition">Simpan Atribut</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleFacilityModal(show) {
            document.getElementById('facilityModal').classList.toggle('hidden', !show);
        }
    </script>
</x-program-admin-layout>
