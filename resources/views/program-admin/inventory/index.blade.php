<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex flex-wrap justify-between items-center bg-white p-6 rounded-2xl border border-slate-100 shadow-sm gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Inventory & Asset Management</h1>
                <p class="text-xs text-slate-400 mt-1">Kelola seluruh aset, logistik, infrastruktur, dan alat operasional program secara transparan.</p>
            </div>
            <button onclick="toggleAssetModal(true)" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-5 py-2.5 rounded-full shadow transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-4v-4m-4 8H4m0 0v-4m0 4h4m4-12h2m-2 0H8m-2 0H4v4m0-4h4" />
                </svg>
                <span>Tambah Aset Baru</span>
            </button>
        </div>

        <!-- Summary Metrics Dashboard -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white border p-5 rounded-2xl shadow-sm text-xs space-y-1.5 hover:shadow transition">
                <span class="block text-[9px] font-bold text-slate-400 uppercase">Total Unit Barang</span>
                <strong class="text-2xl font-extrabold text-slate-800">{{ $totalItems }}</strong>
                <span class="block text-[8px] text-slate-450">Semua aset terdaftar</span>
            </div>
            <div class="bg-green-50/50 border border-green-150 p-5 rounded-2xl shadow-sm text-xs space-y-1.5 hover:shadow transition">
                <span class="block text-[9px] font-bold text-green-600 uppercase">Aset Aktif / Layak</span>
                <strong class="text-2xl font-extrabold text-green-950">{{ $activeItems }}</strong>
                <span class="block text-[8px] text-green-700">Siap operasional</span>
            </div>
            <div class="bg-red-50/50 border border-red-150 p-5 rounded-2xl shadow-sm text-xs space-y-1.5 hover:shadow transition">
                <span class="block text-[9px] font-bold text-red-650 uppercase font-semibold">Barang Rusak</span>
                <strong class="text-2xl font-extrabold text-red-950">{{ $damagedItems }}</strong>
                <span class="block text-[8px] text-red-700">Perlu perbaikan/audit</span>
            </div>
            <div class="bg-slate-50 border p-5 rounded-2xl shadow-sm text-xs space-y-1.5 hover:shadow transition">
                <span class="block text-[9px] font-bold text-slate-505 uppercase">Barang Hilang / Rusak Berat</span>
                <strong class="text-2xl font-extrabold text-slate-800">{{ $lostItems }}</strong>
                <span class="block text-[8px] text-slate-500">Tercatat di audit trail</span>
            </div>
            <div class="bg-blue-50/50 border border-blue-150 p-5 rounded-2xl shadow-sm text-xs space-y-1.5 hover:shadow transition col-span-2 lg:col-span-1">
                <span class="block text-[9px] font-bold text-blue-600 uppercase">Barang Dipinjam</span>
                <strong class="text-2xl font-extrabold text-blue-950">{{ $borrowedItems }}</strong>
                <span class="block text-[8px] text-blue-750">Di tangan relawan/mitra</span>
            </div>
        </div>

        <!-- Visual Analytics Dashboard Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Asset Value & Distribution -->
            <div class="bg-white border rounded-2xl p-6 shadow-sm space-y-4">
                <div>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Nilai Total & Kategori</h3>
                    <p class="text-[9px] text-slate-400">Total valuasi seluruh aset investasi</p>
                </div>
                <div class="bg-slate-900 text-[#D9F99D] p-5 rounded-2xl text-center space-y-1">
                    <span class="text-[8px] text-slate-400 uppercase tracking-widest block font-bold">Total Valuasi Aset</span>
                    <strong class="text-xl font-extrabold font-mono">IDR {{ number_format($totalValue, 0, ',', '.') }}</strong>
                </div>
                <!-- Categories progress bars -->
                <div class="space-y-3 pt-2 text-xs">
                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Sebaran Kategori</span>
                    @foreach($categories as $cat => $count)
                        @php $pct = $totalItems > 0 ? ($count / $totalItems) * 100 : 0; @endphp
                        <div class="space-y-1">
                            <div class="flex justify-between font-bold text-slate-700">
                                <span>{{ $cat }}</span>
                                <span>{{ $count }} barang ({{ round($pct) }}%)</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-[#4CAF50] h-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Condition Breakdown -->
            <div class="bg-white border rounded-2xl p-6 shadow-sm space-y-4">
                <div>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Kondisi Fisik Aset</h3>
                    <p class="text-[9px] text-slate-400">Status kesehatan operasional peralatan</p>
                </div>
                <div class="space-y-3.5 text-xs">
                    @foreach($conditions as $cond => $count)
                        @php
                            $pct = $totalItems > 0 ? ($count / $totalItems) * 100 : 0;
                            $barColor = 'bg-[#4CAF50]';
                            if ($cond === 'Rusak Ringan') $barColor = 'bg-amber-500';
                            if ($cond === 'Rusak Berat') $barColor = 'bg-red-500';
                            if ($cond === 'Hilang') $barColor = 'bg-slate-400';
                        @endphp
                        <div class="space-y-1">
                            <div class="flex justify-between font-bold text-slate-700">
                                <span>{{ $cond }}</span>
                                <span>{{ $count }} item ({{ round($pct) }}%)</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="{{ $barColor }} h-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Asset by Location -->
            <div class="bg-white border rounded-2xl p-6 shadow-sm space-y-4">
                <div>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Aset per Lokasi Penempatan</h3>
                    <p class="text-[9px] text-slate-400">Distribusi lokasi penempatan barang</p>
                </div>
                <div class="space-y-3 text-xs">
                    @foreach($locations as $loc => $count)
                        @php $pct = $totalItems > 0 ? ($count / $totalItems) * 100 : 0; @endphp
                        <div class="space-y-1">
                            <div class="flex justify-between font-bold text-slate-700">
                                <span>{{ $loc }}</span>
                                <span>{{ $count }} unit ({{ round($pct) }}%)</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-blue-500 h-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    @if($locations->isEmpty())
                        <p class="text-xs text-slate-400 italic text-center py-4">Belum ada data penempatan aset.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Inventory List Table -->
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-slate-50/50">
                <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Daftar Inventaris Aset Aktif</span>
                <span class="bg-slate-200 text-slate-700 text-[10px] font-bold px-3 py-1 rounded-full">{{ $assets->count() }} Aset</span>
            </div>
            <div class="overflow-x-auto text-xs">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/50 border-b border-gray-100 text-slate-450 uppercase text-[9px] font-extrabold tracking-wider">
                            <th class="py-3 px-6">Asset ID</th>
                            <th class="py-3 px-6">Nama Barang</th>
                            <th class="py-3 px-6">Kategori</th>
                            <th class="py-3 px-6">Penempatan</th>
                            <th class="py-3 px-6 text-center">Jumlah</th>
                            <th class="py-3 px-6 text-center font-semibold">Kondisi</th>
                            <th class="py-3 px-6">PIC</th>
                            <th class="py-3 px-6 text-right">Nilai Total</th>
                            <th class="py-3 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-150">
                        @foreach($assets as $asset)
                            @php
                                $badgeColor = 'bg-green-50 text-green-800 border-green-200';
                                if (in_array($asset->current_condition, ['Rusak Ringan'])) {
                                    $badgeColor = 'bg-amber-50 text-amber-800 border-amber-200';
                                } elseif (in_array($asset->current_condition, ['Rusak Berat'])) {
                                    $badgeColor = 'bg-red-50 text-red-800 border-red-200';
                                }
                            @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-4 px-6 font-mono font-bold text-slate-650">{{ $asset->asset_id }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2.5">
                                        @if($asset->photo_path)
                                            <img src="{{ $asset->photo_path }}" class="w-8 h-8 rounded-lg object-cover border">
                                        @else
                                            <div class="w-8 h-8 bg-slate-50 border rounded-lg flex items-center justify-center text-slate-400">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <strong class="block text-slate-800">{{ $asset->name }}</strong>
                                            <span class="block text-[9px] text-slate-400">Program: {{ $asset->program?->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-medium text-slate-600">{{ $asset->category }}</td>
                                <td class="py-4 px-6 text-slate-600 font-semibold">{{ $asset->location }}</td>
                                <td class="py-4 px-6 text-center font-bold text-slate-700">{{ $asset->quantity }} {{ $asset->unit }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase border {{ $badgeColor }}">
                                        {{ $asset->current_condition }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-600">{{ $asset->pic?->name ?? '-' }}</td>
                                <td class="py-4 px-6 text-right font-mono font-bold text-slate-750">IDR {{ number_format($asset->total_value, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('program-admin.inventory.show', $asset->id) }}" class="bg-slate-100 hover:bg-slate-200 border text-slate-700 px-3 py-1.5 rounded-xl font-bold transition inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                            Detail & Update
                                        </a>
                                        <!-- Public Scan Preview -->
                                        <a href="{{ route('inventory.public', $asset->id) }}" target="_blank" class="bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-800 px-3 py-1.5 rounded-xl font-bold transition inline-flex items-center gap-1" title="Lihat Tampilan Publik">
                                            <svg class="w-3.5 h-3.5 text-blue-750" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Publik
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if($assets->isEmpty())
                            <tr>
                                <td colspan="9" class="text-center py-12 text-gray-400 font-semibold italic">Belum ada barang atau aset program terdaftar.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Asset Modal -->
        <div id="assetModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6" style="backdrop-filter: blur(4px);">
            <div class="bg-white rounded-[2.5rem] p-6 max-w-lg w-full shadow-2xl border overflow-y-auto max-h-[92vh] text-xs">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase">Tambah Barang Aset Program Baru</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">Catat barang dan kelola detail audit inventarisnya.</p>
                    </div>
                    <button onclick="toggleAssetModal(false)" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">&times;</button>
                </div>
                
                <form method="POST" action="{{ route('program-admin.inventory.store') }}" x-data="{ isSubmitting: false }" @submit="isSubmitting = true" class="space-y-4">
                    @csrf
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Barang / Aset</label>
                            <input type="text" name="name" required placeholder="e.g. Mesin Pencacah Sampah" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Program Pemilik</label>
                            <select name="program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                                @foreach($programs as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Kategori</label>
                            <select name="category" required class="w-full mt-1 px-3 py-2.5 border rounded-xl bg-slate-50">
                                <option value="Infrastruktur">Infrastruktur</option>
                                <option value="Mesin & Alat Berat">Mesin & Alat Berat</option>
                                <option value="Teknologi & Elektronik">Teknologi & Elektronik</option>
                                <option value="Operasional Lapangan">Operasional Lapangan</option>
                                <option value="Peralatan Budidaya">Peralatan Budidaya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Sub Kategori</label>
                            <input type="text" name="sub_category" placeholder="e.g. Alat Maggot" class="w-full mt-1 px-3 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Lokasi Penempatan</label>
                            <input type="text" name="location" required placeholder="e.g. Posko Sektor A" class="w-full mt-1 px-3 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Jumlah</label>
                            <input type="number" name="quantity" value="1" required class="w-full mt-1 px-3 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Satuan</label>
                            <input type="text" name="unit" value="pcs" required placeholder="pcs, unit" class="w-full mt-1 px-3 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Kondisi Awal</label>
                            <select name="initial_condition" required class="w-full mt-1 px-3 py-2.5 border rounded-xl bg-slate-50">
                                <option value="Baru">Baru</option>
                                <option value="Bagus">Bagus (Seken)</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal Beli</label>
                            <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="w-full mt-1 px-3 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Harga Satuan (IDR)</label>
                            <input type="number" name="price_per_unit" required placeholder="e.g. 5000000" class="w-full mt-1 px-3 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Sumber Dana</label>
                            <input type="text" name="funding_source" placeholder="e.g. CSR Sponsor A" class="w-full mt-1 px-3 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Supplier</label>
                            <input type="text" name="supplier" placeholder="e.g. PT Mitra Logistik" class="w-full mt-1 px-3 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Penanggung Jawab (PIC)</label>
                            <select name="pic_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                                @foreach($volunteers as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Keterangan Tambahan</label>
                            <input type="text" name="description" placeholder="Catatan khusus..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                    </div>

                    <!-- Universal Photo Uploader Widget -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Unggah Dokumentasi Foto Barang</label>
                        <x-universal-upload id="new_asset_photo" name="photo" />
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t">
                        <button type="button" onclick="toggleAssetModal(false); isSubmitting = false" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-full font-bold transition">Batal</button>
                        <button type="submit" :disabled="isSubmitting" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white px-5 py-2.5 rounded-full font-bold transition flex items-center justify-center gap-2">
                            <template x-if="isSubmitting">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Inventaris'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleAssetModal(show) {
            document.getElementById('assetModal').classList.toggle('hidden', !show);
        }
    </script>
</x-program-admin-layout>
