<x-volunteer-layout>
    <div class="space-y-6 animate-fade-in" x-data="checkInEngine()">
        
        <!-- Header -->
        <div>
            <a href="{{ route('volunteer.attendance.index') }}" class="text-xs font-bold text-[#4CAF50] hover:underline">&larr; Kembali</a>
            <h1 class="text-lg font-bold text-slate-900 mt-2">Absen Masuk Harian (General)</h1>
            <p class="text-xs text-slate-400 mt-1">Lakukan check-in harian umum posko/wilayah kerja Anda.</p>
        </div>

        <!-- Camera Stream Box -->
        <div class="bg-white border rounded-3xl p-4 shadow-sm space-y-4">
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Validasi Wajah (Selfie Camera)</span>
            
            <div class="relative rounded-2xl overflow-hidden aspect-video bg-black flex items-center justify-center">
                <video x-ref="video" autoplay playsinline class="w-full h-full object-cover" x-show="!photoCaptured"></video>
                <canvas x-ref="canvas" class="hidden"></canvas>
                <img :src="photoData" class="w-full h-full object-cover" x-show="photoCaptured">
                
                <!-- Status layer -->
                <div class="absolute bottom-3 left-3 bg-black/60 backdrop-blur-md px-3 py-1 rounded-full text-[10px] text-white">
                    <span x-text="cameraStatus">Kamera Aktif</span>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" @click="capturePhoto" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-3 rounded-2xl text-xs font-bold transition active:scale-95 shadow-sm">
                    <span x-text="photoCaptured ? 'Ambil Ulang Foto' : 'Ambil Foto Selfie'"></span>
                </button>
            </div>
        </div>

        <!-- GPS Coordinates Box -->
        <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4">
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Validasi Lokasi (GPS Geofence)</span>
            
            <div class="flex items-center gap-3 bg-slate-50 border p-3.5 rounded-2xl">
                <div class="w-8 h-8 bg-lime-100 rounded-full flex items-center justify-center text-green-600 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <strong class="block text-xs text-slate-800" x-text="gpsText">Mengambil koordinat...</strong>
                    <span class="block text-[10px] text-gray-400">Akurasi GPS Browser</span>
                </div>
            </div>

            <!-- Outside geofence validation reason warning -->
            <div x-show="outsideGeofence" class="space-y-2 border border-red-200 bg-red-50 p-4 rounded-2xl" x-transition>
                <span class="block text-[10px] font-bold text-red-750 uppercase">Peringatan: Anda Diluar Area Geofence Penugasan!</span>
                <p class="text-[10px] text-red-600">Anda wajib menuliskan alasan rasional melakukan check-in di luar radius area kerja pada catatan di bawah:</p>
            </div>

            <!-- Notes field -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-slate-400 uppercase">Catatan Aktivitas / Keterangan</label>
                <textarea x-model="reason" placeholder="Tuliskan catatan check-in Anda..." class="w-full mt-1 px-4 py-3 border rounded-2xl text-xs bg-slate-50 focus:border-[#4CAF50]" rows="3"></textarea>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="button" @click="submitCheckIn" class="w-full bg-[#4CAF50] hover:bg-[#2E7D32] text-white py-4 rounded-2xl text-xs font-extrabold shadow-lg transition active:scale-95 flex justify-center items-center gap-2">
            <span x-show="!submitting">Kirim Absensi Harian</span>
            <span x-show="submitting" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
        </button>

    </div>

    <script>
        function checkInEngine() {
            return {
                videoStream: null,
                photoCaptured: false,
                photoData: '',
                cameraStatus: 'Inisialisasi...',
                lat: null,
                lng: null,
                gpsText: 'Mencari sinyal GPS...',
                outsideGeofence: false,
                reason: '',
                submitting: false,

                init() {
                    this.startCamera();
                    this.getLocation();
                },

                startCamera() {
                    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
                        .then(stream => {
                            this.videoStream = stream;
                            this.$refs.video.srcObject = stream;
                            this.cameraStatus = 'Kamera Siap';
                        })
                        .catch(err => {
                            this.cameraStatus = 'Kamera Gagal Diakses';
                            alert('Gagal mengakses kamera depan.');
                        });
                },

                capturePhoto() {
                    if (this.photoCaptured) {
                        this.photoCaptured = false;
                        this.photoData = '';
                        this.startCamera();
                    } else {
                        const video = this.$refs.video;
                        const canvas = this.$refs.canvas;
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        
                        this.photoData = canvas.toDataURL('image/png');
                        this.photoCaptured = true;
                        this.cameraStatus = 'Foto Terkunci';

                        if (this.videoStream) {
                            this.videoStream.getTracks().forEach(track => track.stop());
                        }
                    }
                },

                getLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.lat = position.coords.latitude;
                                this.lng = position.coords.longitude;
                                this.gpsText = `${this.lat.toFixed(6)}, ${this.lng.toFixed(6)}`;
                            },
                            (error) => {
                                this.gpsText = 'Gagal mengambil GPS. Pastikan Location aktif.';
                                alert('Harap berikan izin akses lokasi (GPS) pada browser.');
                            },
                            { enableHighAccuracy: true }
                        );
                    } else {
                        this.gpsText = 'Browser tidak mendukung GPS.';
                    }
                },

                submitCheckIn() {
                    if (!this.photoCaptured) {
                        alert('Harap ambil foto selfie terlebih dahulu.');
                        return;
                    }
                    if (!this.lat || !this.lng) {
                        alert('Harap tunggu sampai sinyal GPS didapatkan.');
                        return;
                    }

                    this.submitting = true;

                    fetch(`/volunteer/attendance/check-in-daily/store`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            lat: this.lat,
                            lng: this.lng,
                            selfie: this.photoData,
                            reason: this.reason
                        })
                    })
                    .then(res => {
                        this.submitting = false;
                        if (res.status === 422) {
                            res.json().then(data => {
                                if (data.outside) {
                                    this.outsideGeofence = true;
                                    alert(data.message);
                                } else {
                                    alert(data.message);
                                }
                            });
                        } else if (res.ok) {
                            alert('Check-In Harian Berhasil!');
                            window.location.href = '{{ route("volunteer.attendance.index") }}';
                        } else {
                            alert('Terjadi kesalahan jaringan.');
                        }
                    })
                    .catch(err => {
                        this.submitting = false;
                        alert('Gagal mengirim absensi.');
                    });
                }
            }
        }
    </script>
</x-volunteer-layout>
