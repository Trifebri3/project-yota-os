<x-volunteer-layout>
    <div class="space-y-6 animate-fade-in" x-data="taskSubmitter()">
        <!-- Header -->
        <div>
            <a href="{{ route('volunteer.tasks.index') }}" class="text-xs font-bold text-[#4CAF50] hover:underline">&larr; Kembali ke Tugas</a>
            <h1 class="text-lg font-bold text-slate-900 mt-2">{{ $task->title }}</h1>
            <p class="text-xs text-slate-400 mt-1">Kegiatan: {{ $task->activity?->name }}</p>
        </div>

        <!-- Task Details -->
        <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4">
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Detail Instruksi</span>
            <p class="text-xs text-slate-500 leading-relaxed">{{ $task->description }}</p>
            
            <div class="grid grid-cols-2 gap-4 text-[10px] text-gray-500 pt-2 border-t">
                <div>
                    <span class="block font-semibold">Batas Waktu:</span>
                    <strong class="text-slate-700">{{ $task->due_date?->format('d M Y H:i') ?? '-' }}</strong>
                </div>
                <div>
                    <span class="block font-semibold">Tipe Tugas:</span>
                    <strong class="text-slate-700 uppercase">{{ $task->type }}</strong>
                </div>
            </div>
        </div>

        @if($submission)
            <!-- Submission status badge -->
            <div class="bg-slate-50 p-5 rounded-3xl border border-dashed text-center space-y-2">
                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status Laporan Anda</span>
                <strong class="block text-sm font-extrabold uppercase text-yellow-600">{{ $submission->status }}</strong>
                <p class="text-xs text-slate-400">Menunggu verifikasi oleh Program Admin.</p>
            </div>
        @else
            <!-- Submission reporting form -->
            <form method="POST" action="{{ route('volunteer.tasks.submit', $task->id) }}" enctype="multipart/form-data" @submit="isSubmitting = true" class="space-y-6">
                @csrf
                
                <!-- GPS coordinates tracker hidden inputs -->
                <input type="hidden" name="lat" x-model="lat">
                <input type="hidden" name="lng" x-model="lng">

                <!-- Task Checklist items if any -->
                @if(!empty($task->checklists))
                    <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-3">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Checklist Penunjang Tugas</span>
                        <div class="space-y-2.5">
                            @foreach($task->checklists as $index => $item)
                                <label class="inline-flex items-center text-xs text-slate-700 w-full bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <input type="checkbox" name="checklists[{{ $index }}][checked]" value="true" class="rounded text-[#4CAF50] focus:ring-[#4CAF50] border-gray-300">
                                    <span class="ml-2.5 font-medium">{{ $item['label'] ?? '' }}</span>
                                    <input type="hidden" name="checklists[{{ $index }}][label]" value="{{ $item['label'] ?? '' }}">
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Dynamic Form Config Engine fields -->
                @if($task->customForm && !empty($task->customForm->fields))
                    <div class="bg-white border rounded-3xl p-5 shadow-sm">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-4">Laporan Dampak (Form Pengisian)</span>
                        
                        {!! app(\App\Services\FormEngineService::class)->renderHtml($task->customForm->fields) !!}
                    </div>
                @endif

                <!-- Validation selfie camera input -->
                <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Validasi Foto (Selfie)</span>
                    <div class="relative rounded-2xl overflow-hidden aspect-video bg-black flex items-center justify-center">
                        <video x-ref="video" autoplay playsinline class="w-full h-full object-cover" x-show="!selfieCaptured"></video>
                        <canvas x-ref="canvas" class="hidden"></canvas>
                        <img :src="selfieData" class="w-full h-full object-cover" x-show="selfieCaptured">
                    </div>
                    <input type="hidden" name="selfie_base64" x-model="selfieData">
                    <button type="button" @click="captureSelfie" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 py-3 rounded-2xl text-xs font-bold transition">
                        <span x-text="selfieCaptured ? 'Ulangi Selfie' : 'Ambil Selfie Validasi'"></span>
                    </button>
                </div>

                <!-- Signature input using signature-pad Blade component -->
                <div class="space-y-2">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Persetujuan & Tanda Tangan</span>
                    <x-signature-pad />
                </div>

                <!-- Submit Action Button -->
                <button type="submit" :disabled="isSubmitting" class="w-full bg-[#4CAF50] hover:bg-[#2E7D32] text-white py-4 rounded-2xl text-xs font-extrabold shadow-lg transition active:scale-95 text-center flex items-center justify-center gap-2">
                    <template x-if="isSubmitting">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="isSubmitting ? 'Mengirim Laporan...' : 'Kirim Laporan Output Proyek'"></span>
                </button>
            </form>
        @endif
    </div>

    <script>
        function taskSubmitter() {
            return {
                lat: null,
                lng: null,
                selfieCaptured: false,
                selfieData: '',
                videoStream: null,
                isSubmitting: false,

                init() {
                    this.getCoordinates();
                    this.startCamera();
                },

                getCoordinates() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(position => {
                            this.lat = position.coords.latitude;
                            this.lng = position.coords.longitude;
                        });
                    }
                },

                startCamera() {
                    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                        .then(stream => {
                            this.videoStream = stream;
                            this.$refs.video.srcObject = stream;
                        })
                        .catch(err => {
                            console.log('Camera failed:', err);
                        });
                },

                captureSelfie() {
                    if (this.selfieCaptured) {
                        this.selfieCaptured = false;
                        this.selfieData = '';
                        this.startCamera();
                    } else {
                        const video = this.$refs.video;
                        const canvas = this.$refs.canvas;
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        
                        this.selfieData = canvas.toDataURL('image/png');
                        this.selfieCaptured = true;

                        if (this.videoStream) {
                            this.videoStream.getTracks().forEach(track => track.stop());
                        }
                    }
                }
            }
        }
    </script>
</x-volunteer-layout>
