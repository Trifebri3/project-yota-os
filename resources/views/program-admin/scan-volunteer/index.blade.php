<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in text-xs" x-data="adminAttendanceScanner()">
        <!-- Header -->
        <div class="flex flex-wrap justify-between items-center bg-white p-6 rounded-2xl border border-slate-100 shadow-sm gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Presensi & Scan QR Relawan</h1>
                <p class="text-xs text-slate-400 mt-1">Pindai QR ID Anggota/Program milik relawan dan ambil foto verifikasi wajah mereka untuk konfirmasi kehadiran langsung.</p>
            </div>
        </div>

        <!-- Persistent Config Card at the Top -->
        <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="block text-[10px] font-extrabold text-[#4CAF50] uppercase tracking-wider">Pengaturan Sesi Presensi Aktif</span>
                <span class="text-[9px] font-bold text-slate-400">Pilih kegiatan hari ini untuk mengaktifkan scanner</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Pilih Kegiatan Hari Ini (Wajib)</label>
                    <select x-model="selectedKegiatanId" @change="saveConfig()" class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-700 text-xs font-bold focus:border-[#4CAF50] focus:ring-1 focus:ring-[#4CAF50] outline-none">
                        <option value="">-- Pilih Kegiatan Aktif Hari Ini --</option>
                        @foreach($activities as $act)
                            <option value="{{ $act->id }}">[{{ $act->workPlan?->program?->name }}] {{ $act->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Bobot Poin Kehadiran (Achievement)</label>
                    <input type="number" x-model="pointsWeight" @input="saveConfig()" min="0" class="w-full mt-1 px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-700 text-xs font-bold focus:border-[#4CAF50] focus:ring-1 focus:ring-[#4CAF50] outline-none">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Panel: Instructions & Status -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white border rounded-3xl p-6 shadow-sm space-y-4">
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status Absensi Relawan</span>
                    
                    <!-- Steps Indicator -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center font-bold"
                                 :class="step === 1 ? 'bg-[#4CAF50] text-white' : (step > 1 ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-400')">
                                1
                            </div>
                            <div>
                                <span class="block font-bold text-slate-800">Scan QR Code Relawan</span>
                                <span class="text-[9px] text-slate-400" x-text="scannedQr ? 'QR Terbaca: ' + scannedQr.substring(0, 15) + '...' : 'Menunggu pemindaian...'"></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center font-bold"
                                 :class="step === 2 ? 'bg-[#4CAF50] text-white' : (step > 2 ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-400')">
                                2
                            </div>
                            <div>
                                <span class="block font-bold text-slate-800">Foto Verifikasi Wajah</span>
                                <span class="text-[9px] text-slate-400" x-text="selfiePhoto ? 'Foto berhasil diambil' : 'Ambil foto wajah relawan'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- GPS Location Indicator -->
                    <div class="border-t border-slate-100 pt-4 space-y-2">
                        <span class="block text-[9px] font-bold text-slate-400 uppercase">Akurasi GPS Admin</span>
                        <div class="flex items-center gap-2 text-slate-600">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span x-text="latitude && longitude ? latitude.toFixed(6) + ', ' + longitude.toFixed(6) : 'Mencari lokasi GPS...'"></span>
                        </div>
                    </div>
                </div>

                <!-- Reset button -->
                <button type="button" @click="resetScanner" class="w-full py-3 bg-slate-100 hover:bg-slate-200 border text-slate-700 font-bold rounded-2xl transition">
                    Ulangi Presensi / Reset
                </button>
            </div>

            <!-- Center & Right Panel: Camera Capture Arena -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Placeholder when no kegiatan selected -->
                <div x-show="!selectedKegiatanId" class="bg-white border rounded-3xl p-8 text-center text-slate-400 space-y-3 shadow-sm py-16">
                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mx-auto border border-dashed border-slate-300">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-4v-4m-4 8H4m0 0v-4m0 4h4m4-12h2m-2 0H8m-2 0H4v4m0-4h4"/></svg>
                    </div>
                    <h3 class="text-xs font-bold text-slate-805 uppercase tracking-wider">Silakan Pilih Kegiatan</h3>
                    <p class="text-[10px] text-slate-400 max-w-xs mx-auto">Pilih salah satu kegiatan aktif hari ini pada panel pengaturan di atas untuk mengaktifkan kamera scanner QR.</p>
                </div>

                <!-- STEP 1: QR Scanner View -->
                <div x-show="selectedKegiatanId && step === 1" class="bg-white border rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="text-center">
                        <span class="bg-[#4CAF50]/10 text-[#2E7D32] text-[8px] font-extrabold px-2.5 py-0.5 rounded uppercase tracking-wider">Langkah 1</span>
                        <h2 class="text-base font-extrabold text-slate-800 mt-2">Pindai QR ID / Program Relawan</h2>
                        <p class="text-slate-400 text-[10px] mt-0.5">Hadapkan kode QR di handphone relawan ke kamera pemindai ini.</p>
                    </div>

                    <!-- Scanner Container -->
                    <div class="relative max-w-sm mx-auto aspect-square bg-slate-900 border rounded-3xl overflow-hidden shadow-inner">
                        <div id="qr-reader" class="w-full h-full"></div>
                        <div class="absolute inset-0 border-[3px] border-[#4CAF50]/40 border-dashed rounded-3xl pointer-events-none z-10"></div>
                    </div>
                </div>

                <!-- STEP 2: Face Capture View -->
                <div x-show="selectedKegiatanId && step === 2" class="bg-white border rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="text-center">
                        <span class="bg-[#4CAF50]/10 text-[#2E7D32] text-[8px] font-extrabold px-2.5 py-0.5 rounded uppercase tracking-wider">Langkah 2</span>
                        <h2 class="text-base font-extrabold text-slate-800 mt-2">Verifikasi & Ambil Foto Wajah Relawan</h2>
                        <p class="text-slate-400 text-[10px] mt-0.5">Ambil gambar wajah relawan sebagai dokumen kehadiran dan pembuat Logbook otomatis.</p>
                    </div>

                    <!-- Camera & Canvas Area -->
                    <div class="relative max-w-sm mx-auto aspect-video bg-slate-900 border rounded-3xl overflow-hidden shadow-inner">
                        <!-- Video Feed -->
                        <video x-ref="video" autoplay playsinline class="w-full h-full object-cover" x-show="!selfiePhoto"></video>
                        <!-- Photo Preview -->
                        <img :src="selfiePhoto" class="w-full h-full object-cover" x-show="selfiePhoto">
                    </div>

                    <!-- Controls -->
                    <div class="flex justify-center gap-4">
                        <template x-if="!selfiePhoto">
                            <button type="button" @click="captureSelfie" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-6 py-3 rounded-full shadow transition">
                                📸 Ambil Foto Wajah
                            </button>
                        </template>
                        <template x-if="selfiePhoto">
                            <div class="flex gap-3 w-full max-w-sm">
                                <button type="button" @click="retakeSelfie" class="w-1/2 bg-slate-100 hover:bg-slate-200 border text-slate-700 font-bold py-3 rounded-full transition">
                                    Foto Ulang
                                </button>
                                <button type="button" @click="submitAttendance" :disabled="submitting" class="w-1/2 bg-[#4CAF50] hover:bg-[#2E7D32] text-white font-bold py-3 rounded-full shadow transition flex items-center justify-center gap-2">
                                    <span x-text="submitting ? 'Memproses...' : 'Kirim Presensi'"></span>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Hidden canvas for capturing video frame -->
                <canvas x-ref="canvas" class="hidden" width="640" height="480"></canvas>
            </div>
        </div>

        <!-- Daftar Hadir Khusus (Presensi Relawan) -->
        <div class="bg-white border rounded-3xl p-6 shadow-sm space-y-4">
            <div>
                <h3 class="text-sm font-extrabold text-slate-805 uppercase tracking-wider">Daftar Hadir Khusus (Verified Scan)</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Daftar presensi relawan yang tercatat hari ini melalui pemindaian QR Code dan verifikasi wajah oleh Admin.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-[10px] uppercase font-bold text-slate-450 bg-slate-50/50">
                            <th class="py-3 px-4">Foto Wajah</th>
                            <th class="py-3 px-4">Nama Relawan</th>
                            <th class="py-3 px-4">Program & Sub-Program</th>
                            <th class="py-3 px-4">Kegiatan/Aktivitas</th>
                            <th class="py-3 px-4">Waktu Scan</th>
                            <th class="py-3 px-4">GPS Lokasi</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($history as $log)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-4">
                                    @if($log->photo_path)
                                        <img src="{{ $log->photo_path }}" class="w-10 h-10 rounded-lg object-cover border shadow-sm">
                                    @else
                                        <div class="w-10 h-10 bg-slate-50 border rounded-lg flex items-center justify-center text-slate-400">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <strong class="block text-slate-805">{{ $log->user?->name }}</strong>
                                    <span class="text-[9px] text-slate-400">{{ $log->user?->email }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <strong class="block text-slate-850">{{ $log->program?->name ?? 'General Program' }}</strong>
                                    <span class="text-[9px] text-slate-400">{{ $log->subProgram?->name ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-4 text-slate-700 font-semibold">
                                    {{ $log->kegiatan?->name ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-slate-500">
                                    {{ $log->scan_time ? $log->scan_time->format('d M Y - H:i') : '-' }}
                                </td>
                                <td class="py-3 px-4 text-slate-500 font-mono text-[9px]">
                                    @if($log->latitude && $log->longitude)
                                        {{ $log->latitude }}, {{ $log->longitude }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[8px] font-extrabold uppercase bg-green-50 text-green-800 border border-green-200">
                                        {{ $log->status ?? 'VALID' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        @if($history->isEmpty())
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 italic font-semibold">Belum ada presensi relawan yang tercatat.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Html5Qrcode Library (Directly imported for scanner engine) -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        function adminAttendanceScanner() {
            return {
                step: 1,
                scannedQr: '',
                selfiePhoto: '',
                latitude: null,
                longitude: null,
                html5Qrcode: null,
                stream: null,
                submitting: false,
                selectedKegiatanId: '',
                pointsWeight: 10,

                init() {
                    // Restore from localStorage to preserve session across reloads/resets
                    this.selectedKegiatanId = localStorage.getItem('admin_selected_kegiatan_id') || '';
                    this.pointsWeight = parseInt(localStorage.getItem('admin_points_weight')) || 10;

                    this.requestLocation();
                    if (this.selectedKegiatanId) {
                        this.startQrScanner();
                    }
                },

                saveConfig() {
                    localStorage.setItem('admin_selected_kegiatan_id', this.selectedKegiatanId);
                    localStorage.setItem('admin_points_weight', this.pointsWeight);
                    
                    if (this.selectedKegiatanId) {
                        if (this.step === 1 && (!this.html5Qrcode || !this.html5Qrcode.isScanning)) {
                            this.startQrScanner();
                        }
                    } else {
                        if (this.html5Qrcode) {
                            try {
                                this.html5Qrcode.stop();
                            } catch(e) {}
                        }
                    }
                },

                requestLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (pos) => {
                                this.latitude = pos.coords.latitude;
                                this.longitude = pos.coords.longitude;
                            },
                            (err) => {
                                console.warn('Gagal mendapatkan koordinat GPS.', err);
                            },
                            { enableHighAccuracy: true }
                        );
                    }
                },

                startQrScanner() {
                    setTimeout(() => {
                        this.html5Qrcode = new Html5Qrcode("qr-reader");
                        this.html5Qrcode.start(
                            { facingMode: "environment" },
                            {
                                fps: 10,
                                qrbox: { width: 250, height: 250 }
                            },
                            (decodedText) => {
                                this.scannedQr = decodedText;
                                try {
                                    let audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                                    let osc = audioCtx.createOscillator();
                                    osc.type = 'sine';
                                    osc.frequency.setValueAtTime(880, audioCtx.currentTime);
                                    osc.connect(audioCtx.destination);
                                    osc.start();
                                    osc.stop(audioCtx.currentTime + 0.1);
                                } catch(e) {}

                                this.html5Qrcode.stop().then(() => {
                                    this.step = 2;
                                    this.startCameraFeed();
                                });
                            },
                            (errorMessage) => {}
                        ).catch(err => {
                            console.error("Gagal menyalakan scanner QR", err);
                        });
                    }, 500);
                },

                startCameraFeed() {
                    navigator.mediaDevices.getUserMedia({
                        video: { facingMode: "environment" },
                        audio: false
                    }).then(s => {
                        this.stream = s;
                        this.$refs.video.srcObject = s;
                    }).catch(err => {
                        navigator.mediaDevices.getUserMedia({
                            video: true,
                            audio: false
                        }).then(s => {
                            this.stream = s;
                            this.$refs.video.srcObject = s;
                        }).catch(e => {
                            alert("Kamera tidak dapat diakses.");
                        });
                    });
                },

                captureSelfie() {
                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;
                    const context = canvas.getContext('2d');
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    this.selfiePhoto = canvas.toDataURL('image/png');
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                    }
                },

                retakeSelfie() {
                    this.selfiePhoto = '';
                    this.startCameraFeed();
                },

                submitAttendance() {
                    this.submitting = true;
                    
                    fetch("{{ route('program-admin.scan-volunteer.submit') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            scanned_qr: this.scannedQr,
                            selfie_photo: this.selfiePhoto,
                            lat: this.latitude,
                            lng: this.longitude,
                            kegiatan_id: this.selectedKegiatanId,
                            points: this.pointsWeight
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.submitting = false;
                        if (data.success) {
                            alert(data.message);

                            // Dynamically insert a row to the table to avoid full page reloads!
                            const tbody = document.querySelector('table tbody');
                            if (tbody) {
                                // Remove empty placeholder row if present
                                const emptyRow = tbody.querySelector('tr td.italic');
                                if (emptyRow) {
                                    tbody.innerHTML = '';
                                }
                                
                                const newRow = document.createElement('tr');
                                newRow.className = 'hover:bg-slate-50 transition border-b border-slate-100';
                                newRow.innerHTML = `
                                    <td class="py-3 px-4">
                                        ${data.photo_url ? `<img src="${data.photo_url}" class="w-10 h-10 rounded-lg object-cover border shadow-sm">` : `
                                            <div class="w-10 h-10 bg-slate-50 border rounded-lg flex items-center justify-center text-slate-400">
                                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        `}
                                    </td>
                                    <td class="py-3 px-4">
                                        <strong class="block text-slate-805">${data.volunteer_name}</strong>
                                        <span class="text-[9px] text-slate-400">Baru Terdaftar</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <strong class="block text-slate-850">Program Aktif</strong>
                                        <span class="text-[9px] text-slate-400">-</span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-700 font-semibold">
                                        ${data.activity_name}
                                    </td>
                                    <td class="py-3 px-4 text-slate-500">
                                        Hari Ini
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 font-mono text-[9px]">
                                        ${this.latitude ? this.latitude.toFixed(6) + ', ' + this.longitude.toFixed(6) : '-'}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[8px] font-extrabold uppercase bg-green-50 text-green-800 border border-green-200">
                                            VALID
                                        </span>
                                    </td>
                                `;
                                tbody.insertBefore(newRow, tbody.firstChild);
                            }

                            // Return to Step 1 loop without resetting selected activities
                            this.resetLoop();
                        } else {
                            alert('Gagal: ' + data.message);
                        }
                    })
                    .catch(err => {
                        this.submitting = false;
                        alert('Gagal mengirim data presensi.');
                    });
                },

                resetScanner() {
                    // Manual reset of configuration and loop
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.stream = null;
                    }
                    if (this.html5Qrcode && this.html5Qrcode.isScanning) {
                        this.html5Qrcode.stop();
                    }

                    this.step = 1;
                    this.scannedQr = '';
                    this.selfiePhoto = '';
                    this.selectedKegiatanId = '';
                    this.pointsWeight = 10;
                    localStorage.removeItem('admin_selected_kegiatan_id');
                    localStorage.removeItem('admin_points_weight');
                },

                resetLoop() {
                    // Loop reset: resets only scanner and selfie, keeping activity selection intact!
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.stream = null;
                    }
                    if (this.html5Qrcode && this.html5Qrcode.isScanning) {
                        this.html5Qrcode.stop();
                    }

                    this.step = 1;
                    this.scannedQr = '';
                    this.selfiePhoto = '';
                    
                    if (this.selectedKegiatanId) {
                        this.startQrScanner();
                    }
                }
            };
        }
    </script>
</x-program-admin-layout>
