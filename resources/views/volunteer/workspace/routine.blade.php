<x-volunteer-layout>
    <div class="space-y-6 animate-fade-in" x-data="routineManager()">
        <!-- Header -->
        <div class="bg-white p-6 rounded-3xl border border-gray-150 shadow-sm">
            <h1 class="text-xl font-bold text-slate-900 font-serif">Kegiatan Rutin Harian</h1>
            <p class="text-xs text-slate-400 mt-1">Daftar kegiatan wajib berkala yang dijadwalkan untuk program Anda: <strong>{{ $program->name }}</strong>.</p>
        </div>

        <!-- Routine Activities Cards -->
        <div class="space-y-3">
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jadwal Rutinitas Hari Ini</span>
            
            @foreach($routines as $rt)
                <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wide">⏰ {{ $rt->start_time }} - {{ $rt->end_time }}</span>
                            <h3 class="text-sm font-bold text-slate-800 mt-0.5">{{ $rt->name }}</h3>
                        </div>
                        <div>
                            @if($rt->is_completed_today)
                                <span class="bg-green-100 text-green-950 px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wide">🟢 Selesai</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-950 px-2.5 py-1 rounded-full text-[9px] font-extrabold uppercase tracking-wide">🟡 Belum Dilaporkan</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-1.5 pt-1 text-[10px] border-t border-slate-100 pt-3">
                        <span class="text-gray-450 mr-1 font-medium">Syarat:</span>
                        @if($rt->is_selfie_required)
                            <span class="bg-blue-50 text-blue-800 border border-blue-150 px-2 py-0.5 rounded-lg text-[9px] font-semibold flex items-center gap-1">
                                📸 Selfie Wajib
                            </span>
                        @endif
                        @if($rt->is_notes_required)
                            <span class="bg-amber-50 text-amber-800 border border-amber-150 px-2 py-0.5 rounded-lg text-[9px] font-semibold flex items-center gap-1">
                                📝 Catatan Wajib
                            </span>
                        @endif
                        @if(!$rt->is_selfie_required && !$rt->is_notes_required)
                            <span class="text-slate-400 italic">Tidak ada persyaratan khusus</span>
                        @endif
                    </div>

                    <div class="pt-1">
                        @if($rt->is_completed_today)
                            <button disabled class="w-full bg-slate-100 text-slate-400 text-xs font-bold py-3 rounded-2xl cursor-not-allowed flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                Laporan Hari Ini Selesai
                            </button>
                        @else
                            <button @click="openModal({{ json_encode($rt) }})" class="w-full bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold py-3 rounded-2xl shadow transition active:scale-[0.98]">
                                Laporkan Kegiatan &rarr;
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach

            @if($routines->isEmpty())
                <div class="text-center py-12 bg-white border border-dashed rounded-3xl text-gray-400 text-xs font-medium">
                    Tidak ada kegiatan rutin dijadwalkan untuk program ini.
                </div>
            @endif
        </div>

        <!-- Submit Routine Modal -->
        <div id="routineSubmitModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border text-xs space-y-4 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Laporkan Kegiatan Rutin</h3>
                    <button @click="closeModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>
                
                <div class="p-3.5 bg-slate-50 border rounded-2xl space-y-1">
                    <strong class="block text-slate-800 text-xs" x-text="activeRoutine.name"></strong>
                    <span class="block text-[10px] text-gray-500 font-mono">⏰ <span x-text="activeRoutine.start_time"></span> - <span x-text="activeRoutine.end_time"></span></span>
                </div>

                <form x-bind:action="formUrl" method="POST" enctype="multipart/form-data" @submit="isSubmitting = true" class="space-y-4">
                    @csrf
                    
                    <!-- Selfie/Photo Upload -->
                    <div x-show="activeRoutine.is_selfie_required">
                        <label class="block text-[10px] font-bold text-slate-450 uppercase mb-1">Upload Selfie / Foto Bukti <span class="text-red-500">*</span></label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-200 border-dashed rounded-2xl hover:border-[#4CAF50] transition relative bg-slate-50">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-8 w-8 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-slate-650 text-[10px]">
                                    <label for="photo-file" class="relative cursor-pointer bg-white rounded-md font-bold text-[#4CAF50] hover:text-[#2E7D32] focus-within:outline-none">
                                        <span>Pilih File Gambar</span>
                                        <input id="photo-file" name="photo" type="file" accept="image/*" capture="user" class="sr-only" :required="activeRoutine.is_selfie_required">
                                    </label>
                                </div>
                                <p class="text-[9px] text-slate-400">PNG, JPG, JPEG maks 2MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Input -->
                    <div x-show="activeRoutine.is_notes_required">
                        <label class="block text-[10px] font-bold text-slate-450 uppercase mb-1">Catatan / Keterangan Kegiatan <span class="text-red-500">*</span></label>
                        <textarea name="notes" :required="activeRoutine.is_notes_required" placeholder="Tuliskan aktivitas atau rangkuman singkat..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50] text-xs" rows="3"></textarea>
                    </div>

                    <!-- Additional Details -->
                    <div x-show="!activeRoutine.is_selfie_required && !activeRoutine.is_notes_required" class="text-slate-400 text-xs italic py-2 text-center">
                        Tidak ada persyaratan pengisian laporan wajib. Anda bisa langsung mengirimkan untuk mencatat log absensi.
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t">
                        <button type="button" @click="closeModal()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-2.5 rounded-full transition">Batal</button>
                        <button type="submit" :disabled="isSubmitting" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white font-bold px-6 py-2.5 rounded-full shadow transition flex items-center justify-center gap-2">
                            <template x-if="isSubmitting">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim Laporan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alpine.js / Simple Script state manager -->
    <script>
        function routineManager() {
            return {
                activeRoutine: {},
                formUrl: '',
                isSubmitting: false,
                openModal(routine) {
                    this.activeRoutine = routine;
                    this.formUrl = `/volunteer/workspace/routines/submit/${routine.id}`;
                    this.isSubmitting = false;
                    const formElement = document.querySelector('#routineSubmitModal form');
                    if (formElement) {
                        formElement.action = this.formUrl;
                    }
                    document.getElementById('routineSubmitModal').classList.remove('hidden');
                },
                closeModal() {
                    document.getElementById('routineSubmitModal').classList.add('hidden');
                    this.activeRoutine = {};
                    this.formUrl = '';
                    this.isSubmitting = false;
                    const formElement = document.querySelector('#routineSubmitModal form');
                    if (formElement) {
                        formElement.action = '';
                    }
                }
            }
        }
    </script>
</x-volunteer-layout>
