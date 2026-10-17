<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <a href="{{ route('program-admin.project-manager.index') }}" class="text-xs font-bold text-[#4CAF50] hover:underline">&larr; Kembali ke Dashboard</a>
                <h1 class="text-xl font-bold text-slate-900 mt-2">WBS & Program Kerja: {{ $program->name }}</h1>
                <p class="text-xs text-slate-400 mt-1">Definisikan struktur Program Kerja, Target Output, dan Kegiatan Lapangan secara modular.</p>
            </div>
            <button onclick="toggleWbsModal(true)" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full shadow transition">
                + Tambah Program Kerja
            </button>
        </div>

        <!-- Validation Error Alert -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-xs">
                @foreach($errors->all() as $error)
                    <p class="font-bold">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Work Plans & Activities Tree -->
        <div class="space-y-6">
            @foreach($program->programKerja as $wp)
                <div class="bg-white border rounded-2xl shadow-sm p-6 space-y-4">
                    <div class="flex justify-between items-center border-b pb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="bg-purple-50 text-purple-800 border border-purple-150 px-2 py-0.5 rounded text-[9px] font-extrabold uppercase">
                                    {{ $wp->tahapan ?? 'Umum' }}
                                </span>
                                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide">{{ $wp->name }}</h3>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">{{ $wp->description }}</p>
                            <span class="block text-[10px] text-slate-500 mt-1">
                                Rentang Waktu WBS: <strong>{{ $wp->start_date?->format('d M Y') ?? '-' }} s/d {{ $wp->end_date?->format('d M Y') ?? '-' }}</strong>
                            </span>
                            @if($wp->timeline)
                                <span class="inline-block mt-1 text-[9px] bg-red-100 text-red-800 font-bold px-2 py-0.5 rounded">Mengacu Timeline: {{ $wp->timeline->name }}</span>
                            @endif
                        </div>
                        <div class="text-right space-y-1">
                            <span class="block text-xs font-bold text-slate-700">IDR {{ number_format($wp->budget, 0, ',', '.') }}</span>
                            <span class="text-[9px] bg-lime-100 text-lime-900 px-2 py-0.5 rounded-full font-bold uppercase">{{ $wp->status }}</span>
                            <button onclick="openTargetModal('{{ $wp->id }}')" class="inline-flex items-center gap-1.5 text-[10px] text-[#4CAF50] font-bold hover:underline mt-2">
                                <svg class="w-3.5 h-3.5 text-[#4CAF50]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Kelola Target Output
                            </button>
                        </div>
                    </div>

                    <!-- QR Attendance & GPS Geofence details card -->
                    <div class="border border-slate-100 p-4 rounded-2xl flex flex-wrap justify-between items-center gap-4 bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            @if($wp->qr_code)
                                <div class="bg-white p-1.5 border rounded-xl shadow-sm shrink-0">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($wp->qr_code) }}" alt="QR Code" class="w-14 h-14">
                                </div>
                                <div class="space-y-0.5">
                                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wide">Absensi QR Sub Program</span>
                                    <strong class="block text-xs font-mono text-slate-800">{{ $wp->qr_code }}</strong>
                                    <a href="/volunteer/attendance?scan_sub={{ $wp->qr_code }}" class="inline-block text-[9px] font-extrabold text-[#4CAF50] hover:underline">
                                        Deep Link Absensi relawan &rarr;
                                    </a>
                                </div>
                            @else
                                <div class="flex flex-col gap-1.5">
                                    <span class="text-gray-450 italic text-[10px]">QR Code belum di-generate.</span>
                                    <form method="POST" action="{{ route('program-admin.workplans.generate-qr', $wp->id) }}">
                                        @csrf
                                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-[9px] font-bold px-3 py-1.5 rounded-full transition inline-flex items-center gap-1 shadow-sm">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-4v-4m-4 8H4m0 0v-4m0 4h4m4-12h2m-2 0H8m-2 0H4v4m0-4h4" />
                                            </svg>
                                            Generate QR & Token
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        <div class="text-right text-[10px] text-slate-500 space-y-0.5">
                            <div>Token Kehadiran: <span class="font-mono font-bold text-slate-700 bg-white border px-1.5 py-0.5 rounded">{{ $wp->attendance_token ?? '-' }}</span></div>
                            <div>Radius Validasi: <span class="font-bold text-slate-750">{{ $wp->radius_meters ?? 50 }} meter</span></div>
                            <div>Koordinat Lokasi: <span class="font-mono font-bold text-slate-750">{{ $wp->latitude ?? '-' }}, {{ $wp->longitude ?? '-' }}</span></div>
                        </div>
                    </div>

                    <!-- Target Outputs and Outcomes Progress -->
                    <div class="bg-slate-50 p-4 rounded-2xl border text-xs space-y-3">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Progress Capaian (Output & Outcome)</span>
                        @if(!empty($wp->target_outputs))
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($wp->target_outputs as $out)
                                    @php
                                        $pct = $out['target'] > 0 ? min(100, round(($out['achieved'] / $out['target']) * 100)) : 0;
                                        $sectorColor = 'bg-slate-100 text-slate-700';
                                        $sector = $out['sector'] ?? 'Sosial';
                                        if ($sector === 'Lingkungan') {
                                            $sectorColor = 'bg-emerald-50 text-emerald-700 border border-emerald-150';
                                        } elseif ($sector === 'Ekonomi') {
                                            $sectorColor = 'bg-amber-50 text-amber-700 border border-amber-150';
                                        } elseif ($sector === 'Sosial') {
                                            $sectorColor = 'bg-blue-50 text-blue-700 border border-blue-150';
                                        }
                                    @endphp
                                    <div class="space-y-2 bg-white p-4 rounded-2xl border shadow-sm hover:shadow transition">
                                        <div class="flex justify-between items-start gap-2">
                                            <div>
                                                <span class="block font-bold text-slate-800 text-xs">{{ $out['label'] }}</span>
                                                <span class="text-[9px] font-mono text-slate-400">Satuan: {{ $out['unit'] ?? 'Unit' }}</span>
                                            </div>
                                            <span class="text-[8px] font-extrabold px-2 py-0.5 rounded-full uppercase shrink-0 {{ $sectorColor }}">
                                                {{ $sector }}
                                            </span>
                                        </div>
                                        
                                        <div class="space-y-1">
                                            <div class="flex justify-between text-[10px] text-slate-650 font-bold">
                                                <span>Realisasi</span>
                                                <span>{{ number_format($out['achieved'] ?? 0) }} / {{ number_format($out['target'] ?? 0) }} {{ $out['unit'] ?? '' }} ({{ $pct }}%)</span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-[#4CAF50] h-full rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-400 italic">Belum ada target output dikonfigurasi. Klik 'Kelola Target Output' untuk menambahkan.</p>
                        @endif
                    </div>

                    <!-- Activities associated with this Workplan -->
                    <div class="pl-4 border-l-2 border-slate-100 space-y-3">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Daftar Kegiatan Lapangan:</span>
                        @foreach($wp->kegiatan as $keg)
                            <div class="flex justify-between items-center bg-slate-50 px-4 py-3 rounded-xl text-xs">
                                <div>
                                    <strong class="text-slate-800">{{ $keg->name }}</strong>
                                    <span class="block text-[10px] text-gray-400 mt-0.5">Geofence: {{ $keg->zone?->name ?? 'Tidak ada geofence' }}</span>
                                    <span class="block text-[9px] text-gray-400">Jadwal: {{ $keg->start_date?->format('d M Y') }} s/d {{ $keg->end_date?->format('d M Y') }}</span>
                                </div>
                                <span class="bg-white border text-gray-600 text-[9px] font-bold px-2 py-1 rounded-full uppercase">{{ $keg->status }}</span>
                            </div>
                        @endforeach

                        @if($wp->kegiatan->isEmpty())
                            <p class="text-xs text-gray-400 italic">Belum ada kegiatan didaftarkan untuk program kerja ini. Buat kegiatan baru melalui menu Operations.</p>
                        @endif
                    </div>
                </div>
            @endforeach

            @if($program->programKerja->isEmpty())
                <div class="text-center py-12 bg-white border border-dashed rounded-2xl text-gray-400 font-medium">
                    Belum ada program kerja didaftarkan.
                </div>
            @endif
        </div>

        <!-- Target Outputs Manager Modal -->
        <div id="targetModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border text-xs overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Kelola Target Output Sub-Program</h3>
                    <button onclick="toggleTargetModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form id="target-form" method="POST" action="" x-data="{ isSubmitting: false }" @submit="isSubmitting = true" class="space-y-4">
                    @csrf
                    <div id="target-inputs-wrapper" class="space-y-3">
                        <!-- Dynamic inputs injected by javascript -->
                    </div>
                    <button type="button" onclick="addTargetRow()" class="text-blue-500 font-bold hover:underline">+ Tambah Parameter Baru</button>

                    <div class="pt-4 flex justify-end gap-3 border-t">
                        <button type="button" onclick="toggleTargetModal(false); isSubmitting = false" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" :disabled="isSubmitting" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white px-4 py-2.5 rounded-full transition flex items-center gap-2">
                            <template x-if="isSubmitting">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Target'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add WBS Modal -->
        <div id="wbsModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-gray-150 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Tambah Program Kerja (WBS)</h3>
                    <button onclick="toggleWbsModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form method="POST" action="{{ route('program-admin.workplans.store', $program->id) }}" x-data="{ isSubmitting: false }" @submit="isSubmitting = true" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Program Kerja</label>
                        <input type="text" name="name" required placeholder="e.g. Sosialisasi Pengolahan Limbah Bakau" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Tahapan Alur Program Kerja</label>
                        <select name="tahapan" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                            <option value="Design Thinking">1. Design Thinking</option>
                            <option value="Pra-Pelaksanaan">2. Pra-Pelaksanaan</option>
                            <option value="Pelaksanaan">3. Pelaksanaan</option>
                            <option value="Monitoring & Evaluasi">4. Monitoring & Evaluasi</option>
                            <option value="LPJ & Dampak">5. LPJ & Dampak (Impact)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Mengacu Ke Timeline Utama</label>
                        <select name="program_timeline_id" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                            <option value="">-- Tanpa Mengacu Timeline --</option>
                            @foreach($program->timelines as $tl)
                                <option value="{{ $tl->id }}">{{ $tl->name }} ({{ $tl->start_date->format('d M Y') }} s/d {{ $tl->end_date->format('d M Y') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal Mulai</label>
                            <input type="date" name="start_date" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal Selesai</label>
                            <input type="date" name="end_date" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Deskripsi singkat</label>
                        <textarea name="description" placeholder="Deskripsi..." class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]" rows="3"></textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Alokasi Anggaran (IDR)</label>
                        <input type="number" name="budget" required placeholder="e.g. 15000000" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Latitude</label>
                            <input type="number" step="any" name="latitude" placeholder="e.g. -6.2088" class="w-full mt-1 px-3 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Longitude</label>
                            <input type="number" step="any" name="longitude" placeholder="e.g. 106.8456" class="w-full mt-1 px-3 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Radius (m)</label>
                            <input type="number" name="radius_meters" value="50" required class="w-full mt-1 px-3 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t">
                        <button type="button" onclick="toggleWbsModal(false); isSubmitting = false" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" :disabled="isSubmitting" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full transition flex items-center justify-center gap-2">
                            <template x-if="isSubmitting">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Program Kerja'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.workplans = @json($program->programKerja);

        function toggleWbsModal(show) {
            document.getElementById('wbsModal').classList.toggle('hidden', !show);
        }

        function toggleTargetModal(show) {
            document.getElementById('targetModal').classList.toggle('hidden', !show);
        }

        function openTargetModal(wpId) {
            const wp = window.workplans.find(w => w.id === wpId);
            if (!wp) return;

            document.getElementById('target-form').action = `/program-admin/workplans/target/update/${wpId}`;
            const wrapper = document.getElementById('target-inputs-wrapper');
            wrapper.innerHTML = '';

            const targets = wp.target_outputs;
            if (targets && targets.length > 0) {
                targets.forEach(t => {
                    addTargetRow(t.label, t.target, t.achieved, t.unit || '', t.sector || 'Sosial');
                });
            } else {
                // Add one blank row
                addTargetRow();
            }

            toggleTargetModal(true);
        }

        function addTargetRow(label = '', target = 0, achieved = 0, unit = '', sector = 'Sosial') {
            const wrapper = document.getElementById('target-inputs-wrapper');
            const div = document.createElement('div');
            const rowId = 'row_' + Math.random().toString(36).substr(2, 9);
            div.id = rowId;
            div.className = 'bg-slate-50 p-4 rounded-2xl border space-y-3.5 relative animate-fade-in text-xs';
            
            div.innerHTML = `
                <button type="button" onclick="document.getElementById('${rowId}').remove()" class="absolute top-2.5 right-2.5 text-slate-400 hover:text-red-500 font-bold text-base transition">&times;</button>
                
                <div>
                    <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Indikator SROI / Parameter Output</label>
                    <input type="text" name="labels[]" value="${label}" required placeholder="e.g. Jumlah penanaman bibit bakau" class="w-full px-3 py-2 border rounded-xl text-xs bg-white focus:border-[#4CAF50] focus:ring-0">
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Target</label>
                        <input type="number" name="targets[]" value="${target}" required class="w-full px-3 py-2 border rounded-xl text-xs bg-white focus:border-[#4CAF50] focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Realisasi (Actual)</label>
                        <input type="number" name="achieveds[]" value="${achieved}" required class="w-full px-3 py-2 border rounded-xl text-xs bg-white focus:border-[#4CAF50] focus:ring-0">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Satuan Unit</label>
                        <input type="text" name="units[]" value="${unit}" placeholder="e.g. Batang / Orang / Ton" class="w-full px-3 py-2 border rounded-xl text-xs bg-white focus:border-[#4CAF50] focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kategori Dampak SROI</label>
                        <select name="sectors[]" class="w-full px-3 py-2 border rounded-xl text-xs bg-white focus:border-[#4CAF50] focus:ring-0">
                            <option value="Sosial" ${sector === 'Sosial' ? 'selected' : ''}>Sosial (Social Value)</option>
                            <option value="Ekonomi" ${sector === 'Ekonomi' ? 'selected' : ''}>Ekonomi (Economic Value)</option>
                            <option value="Lingkungan" ${sector === 'Lingkungan' ? 'selected' : ''}>Lingkungan (Environmental Value)</option>
                        </select>
                    </div>
                </div>
            `;
            wrapper.appendChild(div);
        }
    </script>
</x-program-admin-layout>
