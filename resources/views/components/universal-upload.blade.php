@props([
    'name' => 'photo',
    'id' => 'universal-uploader',
    'required' => false,
    'programId' => '',
    'subProgramId' => '',
    'activityId' => ''
])

<div x-data="universalUploader('{{ $id }}', '{{ $name }}', '{{ $programId }}', '{{ $subProgramId }}', '{{ $activityId }}')" 
     class="space-y-3 font-sans w-full">
    
    <!-- Outer Upload Zone -->
    <div 
        @click="openUploadOptions"
        @dragover.prevent="dragover = true"
        @dragleave.prevent="dragover = false"
        @drop.prevent="handleDrop($event)"
        :class="{ 'border-[#4CAF50] bg-lime-50/30': dragover, 'border-gray-200 bg-slate-50 hover:bg-slate-100': !dragover }"
        class="border-2 border-dashed rounded-3xl p-6 flex flex-col items-center justify-center cursor-pointer transition relative group overflow-hidden"
    >
        <!-- Background light gradient effect -->
        <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-slate-50/10 to-[#4CAF50]/5 opacity-0 group-hover:opacity-100 transition duration-300 pointer-events-none"></div>

        <template x-if="!previewUrl">
            <div class="text-center space-y-2 pointer-events-none relative z-10">
                <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm text-slate-500 border border-slate-100 group-hover:scale-105 transition mx-auto">
                    <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="text-xs">
                    <span class="font-extrabold text-slate-700 block">Ambil Foto atau Unggah Berkas</span>
                    <span class="text-slate-400 block text-[10px] mt-0.5">Ketuk di sini atau seret & lepas berkas foto</span>
                </div>
                <span class="inline-block bg-slate-200 text-slate-650 font-bold px-2 py-0.5 rounded-full text-[9px] uppercase tracking-wider">Mendukung Kamera & Galeri</span>
            </div>
        </template>

        <template x-if="previewUrl">
            <div class="w-full relative z-10 space-y-3">
                <div class="relative rounded-2xl overflow-hidden aspect-video bg-black flex items-center justify-center border shadow-sm">
                    <img :src="previewUrl" class="w-full h-full object-cover">
                    <!-- Overlay clear button -->
                    <button 
                        type="button" 
                        @click.stop="clearPhoto" 
                        class="absolute top-2.5 right-2.5 w-8 h-8 bg-black/60 hover:bg-red-650 text-white rounded-full flex items-center justify-center backdrop-blur-sm transition active:scale-90"
                        title="Hapus Foto"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex items-center justify-between text-[10px] text-gray-505 font-mono px-1">
                    <span x-text="fileName" class="truncate max-w-[180px] font-semibold text-slate-700"></span>
                    <span class="bg-green-50 text-green-800 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">Berhasil Dimuat</span>
                </div>
            </div>
        </template>
    </div>

    <!-- Real hidden fields containing final values -->
    <input type="hidden" x-bind:name="fieldName" x-bind:id="fieldId" x-model="photoBase64" x-bind:required="required">
    <input type="hidden" x-bind:name="fieldName + '_lat'" x-model="latitude">
    <input type="hidden" x-bind:name="fieldName + '_lng'" x-model="longitude">
    <input type="hidden" x-bind:name="fieldName + '_device'" x-model="deviceInfo">

    <!-- Hidden Native Inputs triggered by bottom sheet options -->
    <!-- Camera environment (Back Camera) -->
    <input type="file" x-ref="inputCameraEnv" accept="image/*" capture="environment" class="hidden" @change="handleFileSelected">
    <!-- Camera user (Selfie/Front Camera) -->
    <input type="file" x-ref="inputCameraUser" accept="image/*" capture="user" class="hidden" @change="handleFileSelected">
    <!-- Gallery / General picker -->
    <input type="file" x-ref="inputGallery" accept="image/*" class="hidden" @change="handleFileSelected">

    <!-- Mobile Action Sheet Bottom Modal -->
    <div 
        x-show="showOptions" 
        class="fixed inset-0 z-50 flex items-end justify-center bg-black/40 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="showOptions = false"
    >
        <div 
            x-show="showOptions"
            class="w-full max-w-md bg-white rounded-t-[2.5rem] shadow-2xl p-6 space-y-4 transform transition-transform"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            @click.stop
        >
            <!-- Header bar/drag line indicator -->
            <div class="w-12 h-1 bg-slate-200 rounded-full mx-auto mb-2"></div>
            
            <div class="text-center">
                <h3 class="text-sm font-extrabold text-slate-800">Unggah Dokumentasi Foto</h3>
                <p class="text-[10px] text-slate-400 mt-1">Pilih metode pengambilan foto dokumentasi kegiatan Anda</p>
            </div>

            <div class="space-y-2 pt-2">
                <!-- Option 1: Camera Back -->
                <button 
                    type="button" 
                    @click="triggerInput('camera-env')"
                    class="w-full py-4 px-5 bg-slate-50 hover:bg-slate-100 rounded-2xl flex items-center justify-between text-xs text-slate-700 font-bold transition shadow-sm border border-slate-100"
                >
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-550 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Ambil Foto (Kamera Belakang)</span>
                    </div>
                    <span class="text-slate-400">&rarr;</span>
                </button>
 
                <!-- Option 2: Camera Selfie -->
                <button 
                    type="button" 
                    @click="triggerInput('camera-user')"
                    class="w-full py-4 px-5 bg-slate-50 hover:bg-slate-100 rounded-2xl flex items-center justify-between text-xs text-slate-700 font-bold transition shadow-sm border border-slate-100"
                >
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-550 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Ambil Foto Selfie (Kamera Depan)</span>
                    </div>
                    <span class="text-slate-400">&rarr;</span>
                </button>
 
                <!-- Option 3: Gallery -->
                <button 
                    type="button" 
                    @click="triggerInput('gallery')"
                    class="w-full py-4 px-5 bg-slate-50 hover:bg-slate-100 rounded-2xl flex items-center justify-between text-xs text-slate-700 font-bold transition shadow-sm border border-slate-100"
                >
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-slate-550 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Pilih dari Galeri (Gallery)</span>
                    </div>
                    <span class="text-slate-400">&rarr;</span>
                </button>

                <!-- Option 4: Cancel -->
                <button 
                    type="button" 
                    @click="showOptions = false"
                    class="w-full py-3.5 mt-2 bg-red-50 hover:bg-red-150 text-red-750 rounded-2xl text-xs font-extrabold transition text-center"
                >
                    Batalkan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    if (typeof window.universalUploader === 'undefined') {
        window.universalUploader = function(id, name, programId, subProgramId, activityId) {
            return {
                fieldId: id,
                fieldName: name,
                programId: programId,
                subProgramId: subProgramId,
                activityId: activityId,
                showOptions: false,
                dragover: false,
                previewUrl: '',
                photoBase64: '',
                fileName: '',
                latitude: '',
                longitude: '',
                deviceInfo: '',

                init() {
                    this.deviceInfo = navigator.userAgent;
                    this.requestLocation();
                },

                requestLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            pos => {
                                this.latitude = pos.coords.latitude;
                                this.longitude = pos.coords.longitude;
                            },
                            err => {
                                console.log('Location access denied or failed for metadata:', err);
                            },
                            { enableHighAccuracy: true, timeout: 5000 }
                        );
                    }
                },

                openUploadOptions() {
                    this.requestLocation();
                    // On desktop, directly trigger general file picker to skip bottom sheet
                    if (!/Mobi|Android|iPhone/i.test(navigator.userAgent)) {
                        this.$refs.inputGallery.click();
                    } else {
                        this.showOptions = true;
                    }
                },

                triggerInput(type) {
                    this.showOptions = false;
                    if (type === 'camera-env') {
                        this.$refs.inputCameraEnv.click();
                    } else if (type === 'camera-user') {
                        this.$refs.inputCameraUser.click();
                    } else {
                        this.$refs.inputGallery.click();
                    }
                },

                handleFileSelected(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.processFile(file);
                    }
                },

                handleDrop(e) {
                    this.dragover = false;
                    const file = e.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        this.processFile(file);
                    }
                },

                processFile(file) {
                    this.fileName = file.name;
                    this.previewUrl = URL.createObjectURL(file);
                    
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        this.photoBase64 = event.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                clearPhoto() {
                    this.previewUrl = '';
                    this.photoBase64 = '';
                    this.fileName = '';
                    // Reset inputs
                    this.$refs.inputCameraEnv.value = '';
                    this.$refs.inputCameraUser.value = '';
                    this.$refs.inputGallery.value = '';
                }
            };
        };
    }
</script>
