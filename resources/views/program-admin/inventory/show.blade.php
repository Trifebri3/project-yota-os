<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in text-xs">
        <!-- Back Link & Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border shadow-sm">
            <div>
                <a href="{{ route('program-admin.inventory.index') }}" class="text-[#4CAF50] hover:underline font-bold font-sans">&larr; Kembali ke Daftar Inventaris</a>
                <h1 class="text-lg font-extrabold text-slate-900 mt-2">Detail Aset: {{ $asset->name }} ({{ $asset->asset_id }})</h1>
                <p class="text-slate-400 mt-0.5 text-[10px]">Perbarui status dan pantau riwayat perubahan serta audit fisik barang.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="bg-slate-100 hover:bg-slate-200 border text-slate-700 text-xs font-bold px-4 py-2.5 rounded-full transition flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Barcode QR</span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Side: QR Code printout & Asset specs -->
            <div class="space-y-6">
                <!-- QR Asset Card -->
                <div class="bg-white border rounded-3xl p-6 shadow-sm text-center space-y-4">
                    <span class="block text-[10px] font-bold text-slate-450 uppercase tracking-widest">QR CODE IDENTITAS FISIK ASET</span>
                    
                    <div class="p-4 bg-white border-2 border-slate-100 rounded-2xl inline-block shadow-sm">
                        <!-- Points to public entry route which checks permissions and redirects -->
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('inventory.scan', $asset->id)) }}" alt="QR Asset" class="w-36 h-36 mx-auto">
                    </div>
                    
                    <div class="text-[10px] text-slate-400">
                        <p class="font-bold text-slate-700 font-mono text-xs">{{ $asset->asset_id }}</p>
                        <p class="mt-1">Pindai kode QR fisik ini menggunakan handphone Anda untuk membuka dasbor manajemen ini atau transparansi publik.</p>
                    </div>
                    
                    <!-- Print Layout wrapper (hidden by default, only visible on print media) -->
                    <div class="hidden print:block p-8 text-center bg-white border text-black font-sans">
                        <h2 class="text-lg font-bold">YOTA OS ASSET TAG</h2>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode(route('inventory.scan', $asset->id)) }}" class="w-48 h-48 mx-auto my-4">
                        <p class="text-xl font-mono font-bold">{{ $asset->asset_id }}</p>
                        <p class="text-sm font-semibold mt-1">{{ $asset->name }}</p>
                        <p class="text-xs text-gray-500">Program: {{ $asset->program?->name }}</p>
                    </div>
                </div>

                <!-- Asset Specifications Card -->
                <div class="bg-white border rounded-3xl p-6 shadow-sm space-y-3.5">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Spesifikasi Pembelian & Dana</span>
                    
                    <div class="space-y-2.5">
                        <div class="flex justify-between border-b pb-1.5"><span class="text-slate-400">Kondisi Awal:</span><strong class="font-bold text-slate-800">{{ $asset->initial_condition }}</strong></div>
                        <div class="flex justify-between border-b pb-1.5"><span class="text-slate-400">Harga Satuan:</span><strong class="font-bold text-slate-800 font-mono">IDR {{ number_format($asset->price_per_unit, 0, ',', '.') }}</strong></div>
                        <div class="flex justify-between border-b pb-1.5"><span class="text-slate-400">Nilai Aset Total:</span><strong class="font-bold text-slate-800 font-mono">IDR {{ number_format($asset->total_value, 0, ',', '.') }}</strong></div>
                        <div class="flex justify-between border-b pb-1.5"><span class="text-slate-400">Tanggal Beli:</span><strong class="font-bold text-slate-800">{{ $asset->purchase_date?->format('d M Y') ?? '-' }}</strong></div>
                        <div class="flex justify-between border-b pb-1.5"><span class="text-slate-400">Sumber Pendanaan:</span><strong class="font-bold text-slate-800">{{ $asset->funding_source ?? '-' }}</strong></div>
                        <div class="flex justify-between border-b pb-1.5"><span class="text-slate-400">Supplier:</span><strong class="font-bold text-slate-800">{{ $asset->supplier ?? '-' }}</strong></div>
                        <div class="flex justify-between pb-1.5"><span class="text-slate-400">Status Aktif:</span><strong class="font-bold uppercase {{ $asset->status_active ? 'text-green-600' : 'text-red-500' }}">{{ $asset->status_active ? 'AKTIF' : 'NON-AKTIF' }}</strong></div>
                    </div>
                </div>
            </div>

            <!-- Middle: Mode 1 Action Update Panel -->
            <div class="bg-white border rounded-3xl p-6 shadow-sm space-y-4">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dasbor Perbaruan Aset (Admin Update)</span>
                
                <form method="POST" action="{{ route('program-admin.inventory.update', $asset->id) }}" x-data="{ isSubmitting: false }" @submit="isSubmitting = true" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-[9px] font-bold text-slate-450 uppercase mb-1">Kondisi Aset Saat Ini</label>
                        <select name="current_condition" required class="w-full px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                            <option value="Baru" {{ $asset->current_condition === 'Baru' ? 'selected' : '' }}>Baru</option>
                            <option value="Bagus" {{ $asset->current_condition === 'Bagus' ? 'selected' : '' }}>Bagus (Layak Pakai)</option>
                            <option value="Rusak Ringan" {{ $asset->current_condition === 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ $asset->current_condition === 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat (Afkir)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[9px] font-bold text-slate-450 uppercase mb-1">Lokasi Penempatan Terbaru</label>
                        <input type="text" name="location" required value="{{ $asset->location }}" class="w-full px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-450 uppercase mb-1">Jumlah Unit Tersedia</label>
                            <input type="number" name="quantity" required value="{{ $asset->quantity }}" class="w-full px-4 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-450 uppercase mb-1">Penanggung Jawab (PIC)</label>
                            <select name="pic_id" required class="w-full px-4 py-2.5 border rounded-xl bg-slate-50">
                                @foreach($volunteers as $v)
                                    <option value="{{ $v->id }}" {{ $asset->pic_id === $v->id ? 'selected' : '' }}>{{ $v->name }} ({{ $v->role }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-[9px] font-bold text-slate-400 uppercase">Keterangan / Deskripsi</label>
                        <textarea name="description" class="w-full px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50]" rows="2" placeholder="Catatan tambahan...">{{ $asset->description }}</textarea>
                    </div>

                    <!-- Asset Status Toggles -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-2">
                        <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wide">Status Kepemilikan & Kondisi</span>
                        <div class="grid grid-cols-2 gap-3 text-[10px]">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="status_active" value="1" {{ $asset->status_active ? 'checked' : '' }} class="rounded text-[#4CAF50] focus:ring-[#4CAF50] border-gray-300">
                                <span class="font-bold text-slate-750">Aset Aktif</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="status_damaged" value="1" {{ $asset->status_damaged ? 'checked' : '' }} class="rounded text-[#4CAF50] focus:ring-[#4CAF50] border-gray-300">
                                <span class="font-bold text-slate-750">Status Rusak</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="status_lost" value="1" {{ $asset->status_lost ? 'checked' : '' }} class="rounded text-[#4CAF50] focus:ring-[#4CAF50] border-gray-300">
                                <span class="font-bold text-slate-750">Status Hilang</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="status_borrowed" value="1" {{ $asset->status_borrowed ? 'checked' : '' }} class="rounded text-[#4CAF50] focus:ring-[#4CAF50] border-gray-300">
                                <span class="font-bold text-slate-750">Sedang Dipinjam</span>
                            </label>
                        </div>
                    </div>

                    <!-- Universal Upload widget to add updated documentation photo -->
                    <div>
                        <label class="block text-[9px] font-bold text-slate-450 uppercase mb-1">Unggah Foto Kondisi Terkini</label>
                        <x-universal-upload id="update_asset_photo" name="photo" :programId="$asset->program_id" />
                    </div>

                    <button type="submit" :disabled="isSubmitting" class="w-full bg-[#4CAF50] hover:bg-[#2E7D32] text-white py-3 rounded-xl font-bold flex items-center justify-center gap-2 shadow transition">
                        <template x-if="isSubmitting">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="isSubmitting ? 'Memperbarui...' : 'Simpan Perubahan Aset'"></span>
                    </button>
                </form>
            </div>

            <!-- Right Side: Permanent Audit Trail Timeline -->
            <div class="bg-white border rounded-3xl p-6 shadow-sm space-y-4">
                <div>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Audit Trail (Jejak Rekaman Aset)</h3>
                    <p class="text-[9px] text-slate-400">Rekaman log modifikasi permanen & tidak dapat dihapus</p>
                </div>

                <div class="relative pl-5 border-l-2 border-slate-100 space-y-5 py-2">
                    @foreach($asset->logs as $log)
                        <div class="relative space-y-1">
                            <!-- Bullet marker -->
                            <div class="absolute -left-[26px] top-1 w-2.5 h-2.5 rounded-full bg-slate-400 border border-white"></div>
                            
                            <div class="flex justify-between items-center text-[10px] text-slate-400">
                                <span>{{ $log->created_at->format('d M Y - H:i') }}</span>
                                <span class="bg-slate-100 px-2 py-0.5 rounded font-extrabold uppercase text-[8px]">{{ $log->action }}</span>
                            </div>
                            <div class="text-[10px]">
                                <strong class="text-slate-800">{{ $log->user?->name ?? 'System' }}</strong>
                                <span class="text-slate-500">melakukan perubahan status aset di lokasi:</span>
                                <strong class="text-slate-700 block mt-0.5">{{ $log->location ?? $asset->location }}</strong>
                            </div>
                            
                            @if(!empty($log->old_values) && !empty($log->new_values))
                                <div class="bg-slate-50 p-2 border rounded-xl text-[9px] font-mono text-slate-600 mt-2 space-y-1">
                                    @foreach($log->new_values as $key => $val)
                                        @if(isset($log->old_values[$key]) && $log->old_values[$key] !== $val && !in_array($key, ['updated_at', 'photo_path']))
                                            <div class="truncate">
                                                <span class="text-blue-500 font-bold">{{ $key }}</span>: 
                                                <span class="text-red-500 line-through">{{ is_array($log->old_values[$key]) ? json_encode($log->old_values[$key]) : $log->old_values[$key] }}</span> 
                                                &rarr; 
                                                <span class="text-green-600 font-bold">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-program-admin-layout>
