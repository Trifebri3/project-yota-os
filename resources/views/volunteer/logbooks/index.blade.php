<x-volunteer-layout>
    <div class="space-y-6 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-lg font-bold text-slate-900">Logbook Harian</h1>
                <p class="text-xs text-slate-400 mt-1">Catat jam kerja sosial dan rincian kontribusi harian Anda.</p>
            </div>
            <button onclick="toggleLogbookForm(true)" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full shadow transition">
                + Isi Logbook
            </button>
        </div>

        <!-- Add Logbook Form (Collapsible/Overlay modal) -->
        <div id="logbookModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl border border-gray-100 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xs font-bold text-slate-900 uppercase">Isi Logbook Hari Ini</h3>
                    <button onclick="toggleLogbookForm(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form method="POST" action="{{ route('volunteer.logbooks.store') }}" enctype="multipart/form-data" x-data="{ isSubmitting: false }" @submit="isSubmitting = true" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase">Pilih Program Kerja</label>
                        <select name="program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                            @foreach($programs as $prog)
                                <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase">Nama Aktivitas</label>
                        <input type="text" name="activity_name" required placeholder="e.g. Membantu persiapan bibit mangrove" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase">Deskripsi Kegiatan</label>
                        <textarea name="description" required placeholder="Jelaskan kontribusi Anda secara singkat..." class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]" rows="3"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase">Tanggal</label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase">Durasi Kerja (Jam)</label>
                            <input type="number" name="hours_spent" step="0.5" required placeholder="e.g. 3.5" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase mb-1">Unggah Foto Bukti (Opsional)</label>
                        <x-universal-upload id="logbook_photo" name="photo" :programId="$programs->first()?->id ?? ''" />
                    </div>
                    <div class="pt-4 flex justify-end gap-3 border-t">
                        <button type="button" onclick="toggleLogbookForm(false); isSubmitting = false" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" :disabled="isSubmitting" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full transition flex items-center gap-2">
                            <template x-if="isSubmitting">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim Logbook'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Logbooks List -->
        <div class="space-y-3">
            @foreach($logbooks as $lb)
                @php
                    $isHighlighted = str_contains($lb->title, '[Absen Scan Admin]');
                    $points = null;
                    if ($isHighlighted) {
                        preg_match('/Poin Pencapaian: (\d+) poin/', $lb->content, $matches);
                        $points = isset($matches[1]) ? $matches[1] : null;
                    }
                @endphp
                <div class="{{ $isHighlighted ? 'bg-gradient-to-r from-amber-50 to-orange-50/70 border-amber-300 ring-2 ring-amber-100/50' : 'bg-white border' }} rounded-3xl p-5 shadow-sm space-y-3 transition duration-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase">{{ $lb->date?->format('d M Y') }}</span>
                            <h3 class="text-sm font-bold text-slate-800 mt-0.5">{{ $lb->title }}</h3>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            @if($points)
                                <span class="text-[9px] font-bold px-2.5 py-0.5 bg-amber-100 text-amber-800 rounded-full border border-amber-200 animate-pulse flex items-center gap-1">
                                    <span>🌟</span>
                                    <span>{{ $points }} Poin</span>
                                </span>
                            @endif
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full uppercase {{ $lb->status === 'approved' ? 'bg-green-100 text-green-950' : 'bg-yellow-100 text-yellow-950' }}">
                                {{ $lb->status }}
                            </span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400">{{ $lb->content }}</p>
                    
                    @if($lb->photo_path)
                        <div class="pt-1">
                            <a href="{{ $lb->photo_path ? (str_starts_with($lb->photo_path, 'http') ? $lb->photo_path : (str_starts_with($lb->photo_path, '/storage/') ? $lb->photo_path : (str_starts_with($lb->photo_path, 'storage/') ? '/' . $lb->photo_path : '/storage/' . ltrim($lb->photo_path, '/')))) : '' }}" target="_blank">
                                <img src="{{ $lb->photo_path ? (str_starts_with($lb->photo_path, 'http') ? $lb->photo_path : (str_starts_with($lb->photo_path, '/storage/') ? $lb->photo_path : (str_starts_with($lb->photo_path, 'storage/') ? '/' . $lb->photo_path : '/storage/' . ltrim($lb->photo_path, '/')))) : '' }}" class="w-16 h-16 rounded-xl object-cover border">
                            </a>
                        </div>
                    @endif

                    <div class="flex justify-between items-center text-[10px] text-gray-500 pt-2 border-t {{ $isHighlighted ? 'border-amber-200' : 'border-slate-100' }}">
                        <span>Program: <strong>{{ $lb->program?->name }}</strong></span>
                        <span class="font-bold text-slate-700">Durasi: {{ $lb->hours_spent }} Jam</span>
                    </div>
                </div>
            @endforeach

            @if($logbooks->isEmpty())
                <div class="text-center py-12 bg-white border border-dashed rounded-3xl text-gray-400 text-xs font-medium">
                    Belum ada logbook diisi. Klik tombol di atas untuk mengisi logbook.
                </div>
            @endif
        </div>

    </div>

    <script>
        function toggleLogbookForm(show) {
            document.getElementById('logbookModal').classList.toggle('hidden', !show);
        }
    </script>
</x-volunteer-layout>
