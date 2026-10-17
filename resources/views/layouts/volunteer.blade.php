<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#4CAF50">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/images/logo.webp">

    <title>Yota OS - Participant App</title>
    <!-- HTML5 QR Code CDN -->
    <script src="https://unpkg.com/html5-qrcode" defer></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Leaflet Map CSS/JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('SW Registered!', reg))
                    .catch(err => console.log('SW Registration Failed:', err));
            });
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen pb-24">
    <!-- App Container (Locks width on desktop to feel like native app) -->
    <div class="max-w-md mx-auto min-h-screen bg-slate-50 flex flex-col relative shadow-2xl shadow-slate-200 border-x border-slate-100">
        
        <!-- Header -->
        <header class="sticky top-0 bg-white/80 backdrop-blur-md border-b border-gray-100 px-5 py-4 flex items-center justify-between z-30">
            <div class="flex items-center gap-3">
                <img src="/images/logo.webp" alt="Logo" class="w-8 h-8 object-contain">
                <div>
                    <h1 class="text-base font-bold text-slate-900 leading-none">YOTA OS</h1>
                    <span class="text-[9px] font-bold text-[#4CAF50] uppercase tracking-wider">Participant Workspace</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button id="pwa-install-btn" style="display: none;" class="bg-[#4CAF50] text-white text-[9px] font-bold px-3 py-1.5 rounded-full hover:bg-[#388e3c] transition items-center gap-1">
                    ⬇️ Install App
                </button>
                <a href="{{ route('volunteer.carbon.fill') }}" class="p-2 text-slate-400 hover:text-green-600 rounded-full transition active:scale-95" title="Daily Carbon Log">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-11.314l.707.707m11.314 11.314l.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z"/></svg>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-2 text-slate-400 hover:text-slate-600 rounded-full transition active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 px-5 py-6">
            @if(session('success'))
                <div class="mb-4 bg-green-50 text-[#2E7D32] border border-green-200 text-xs font-semibold px-4 py-3.5 rounded-2xl flex items-center gap-2 shadow-sm animate-fade-in">
                    <svg class="w-4 h-4 shrink-0 text-[#2E7D32]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 text-[#C62828] border border-red-200 text-xs font-semibold px-4 py-3.5 rounded-2xl flex items-center gap-2 shadow-sm animate-shake">
                    <svg class="w-4 h-4 shrink-0 text-[#C62828]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-4 bg-amber-50 text-[#D84315] border border-amber-200 text-xs font-semibold px-4 py-3.5 rounded-2xl flex items-center gap-2 shadow-sm animate-fade-in">
                    <svg class="w-4 h-4 shrink-0 text-[#D84315]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-50 text-[#C62828] border border-red-200 text-xs font-semibold px-4 py-3.5 rounded-2xl shadow-sm space-y-1">
                    <div class="flex items-center gap-2 font-extrabold text-[10px] uppercase tracking-wider text-[#B71C1C]">
                        <svg class="w-4 h-4 shrink-0 text-[#B71C1C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        <span>Gagal memvalidasi data:</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-0.5 text-[10px] text-red-800">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>

        <!-- Bottom Tab Navigation -->
        <nav class="fixed bottom-0 left-0 right-0 md:max-w-md md:mx-auto bg-white border-t border-gray-100 px-6 py-2.5 flex justify-between items-center z-40 rounded-t-3xl shadow-xl shadow-slate-300">
            <!-- Dashboard Tab -->
            <a href="{{ route('volunteer.dashboard') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('volunteer.dashboard') ? 'text-[#4CAF50]' : 'text-slate-400' }} transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-[9px] font-bold">Beranda</span>
            </a>

            <!-- Tasks Tab -->
            <a href="{{ route('volunteer.tasks.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('volunteer.tasks.*') ? 'text-[#4CAF50]' : 'text-slate-400' }} transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span class="text-[9px] font-bold">Tugas</span>
            </a>

            <!-- Check In Center FAB -->
            <div class="relative -mt-6">
                <a href="{{ route('volunteer.attendance.index') }}" class="w-14 h-14 bg-[#4CAF50] hover:bg-[#2E7D32] text-white rounded-full flex items-center justify-center shadow-lg hover:shadow-lime-200/50 transition duration-300 transform hover:scale-105 active:scale-95 border-4 border-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </a>
            </div>

            <!-- Logbook Tab -->
            <a href="{{ route('volunteer.logbooks.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('volunteer.logbooks.*') ? 'text-[#4CAF50]' : 'text-slate-400' }} transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span class="text-[9px] font-bold">Logbook</span>
            </a>

            <!-- Achievements Tab -->
            <a href="{{ route('volunteer.certificates.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('volunteer.certificates.*') ? 'text-[#4CAF50]' : 'text-slate-400' }} transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                </svg>
                <span class="text-[9px] font-bold">Sertifikat</span>
            </a>

            <!-- Profile Tab -->
            <a href="{{ route('volunteer.profile') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('volunteer.profile') ? 'text-[#4CAF50]' : 'text-slate-400' }} transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-[9px] font-bold">Profil</span>
            </a>
        </nav>

        <!-- Floating SCAN QR button (V2 Enhancement) -->
        <button type="button" onclick="openGlobalScanner()" class="absolute bottom-20 right-5 w-12 h-12 bg-[#4CAF50] hover:bg-[#2E7D32] text-white rounded-full flex items-center justify-center shadow-lg transition active:scale-95 border-2 border-white z-40" title="Scan QR Code">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-4v-4m-4 8H4m0 0v-4m0 4h4m4-12h2m-2 0H8m-2 0H4v4m0-4h4" />
            </svg>
        </button>

        <!-- Floating Voice Assistant Toggle Button (V2 human-centered) -->
        <button type="button" id="voice-assistant-fab" onclick="toggleVoiceAssistant()" class="absolute bottom-36 right-5 w-12 h-12 bg-slate-800 hover:bg-slate-900 text-white rounded-full flex items-center justify-center shadow-lg transition active:scale-95 border-2 border-white z-40 group" title="Voice Assistant">
            <svg id="voice-assistant-mic-svg" class="w-5 h-5 text-white transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
            </svg>
            <span id="voice-assistant-pulse" class="absolute inset-0 rounded-full bg-red-500/30 animate-ping hidden"></span>
        </button>

        <!-- Global QR Scanner Modal -->
        <div id="global-qr-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden" style="backdrop-filter: blur(4px);">
            <div class="bg-white rounded-[2rem] p-6 max-w-sm w-full shadow-2xl border mx-4 space-y-4 relative">
                <div class="flex justify-between items-center border-b pb-3">
                    <div>
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Pindai QR Program / Aset</h3>
                        <p class="text-[9px] text-slate-400 mt-0.5">Posisikan kode QR di dalam bingkai</p>
                    </div>
                    <button type="button" onclick="closeGlobalScanner()" class="text-slate-400 hover:text-slate-650 text-xl font-bold p-1">&times;</button>
                </div>
                <div id="reader" class="overflow-hidden rounded-2xl bg-black border w-full aspect-square relative shadow-inner"></div>
                <div id="scanner-status" class="text-center text-[10px] text-slate-500 font-bold bg-slate-50 py-2 rounded-xl">Menginisialisasi kamera...</div>
            </div>
        </div>

        <!-- Global Toast Alert -->
        <div id="global-toast" class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50 bg-slate-900 text-white text-xs px-5 py-3 rounded-full shadow-xl flex items-center gap-2 hidden max-w-xs text-center border border-white/10">
            <span id="global-toast-msg">Menyambung...</span>
        </div>

    </div>

    <!-- HTML5 QR Scanner & Voice Integration scripts -->
    <script>
        let deferredPrompt;
        const installBtn = document.getElementById('pwa-install-btn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            if (installBtn) installBtn.style.display = 'inline-flex';
        });

        if (installBtn) {
            installBtn.addEventListener('click', (e) => {
                installBtn.style.display = 'none';
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    deferredPrompt = null;
                });
            });
        }

        // ==========================================
        // 1. QR CODE SCANNER LOGIC (V2 QR Attendance & Assets)
        // ==========================================
        let html5QrCode = null;

        function openGlobalScanner() {
            document.getElementById('global-qr-modal').classList.remove('hidden');
            document.getElementById('scanner-status').innerText = 'Mengakses Kamera...';

            // Start QR Scanner using html5-qrcode
            setTimeout(() => {
                try {
                    html5QrCode = new Html5Qrcode("reader");
                    const config = { fps: 10, qrbox: { width: 220, height: 220 } };

                    html5QrCode.start(
                        { facingMode: "environment" }, 
                        config,
                        (decodedText) => {
                            // On Scan Success
                            playBeep();
                            handleQrScanSuccess(decodedText);
                        },
                        (errorMessage) => {
                            // Quietly capture scan attempts
                        }
                    ).then(() => {
                        document.getElementById('scanner-status').innerText = 'Kamera Aktif. Silakan scan...';
                    }).catch(err => {
                        console.error("Camera access error:", err);
                        document.getElementById('scanner-status').innerText = 'Gagal mengakses kamera.';
                    });
                } catch(e) {
                    console.error("Scanner setup failed:", e);
                }
            }, 300);
        }

        function closeGlobalScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode = null;
                    document.getElementById('global-qr-modal').classList.add('hidden');
                }).catch(err => {
                    document.getElementById('global-qr-modal').classList.add('hidden');
                });
            } else {
                document.getElementById('global-qr-modal').classList.add('hidden');
            }
        }

        function playBeep() {
            try {
                const context = new (window.AudioContext || window.webkitAudioContext)();
                const osc = context.createOscillator();
                osc.type = "sine";
                osc.frequency.setValueAtTime(880, context.currentTime); // A5 note
                osc.connect(context.destination);
                osc.start();
                osc.stop(context.currentTime + 0.1);
            } catch(e) {}
        }

        function handleQrScanSuccess(decodedText) {
            closeGlobalScanner();
            
            // Check if it's a Sub Program QR
            if (decodedText.startsWith('QR-SUBPROG-')) {
                registerSubProgramAttendance(decodedText);
            } 
            // Check if it's an Asset QR (or redirects to inventory)
            else if (decodedText.startsWith('AST-') || decodedText.startsWith('QR-ASSET-') || decodedText.includes('/inventory/')) {
                let code = decodedText;
                if (decodedText.includes('/inventory/')) {
                    // Extract code/ID from URL
                    const parts = decodedText.split('/');
                    code = parts[parts.length - 1];
                } else if (decodedText.startsWith('QR-ASSET-')) {
                    code = decodedText.substring(9);
                }
                showToast("Membuka data aset " + code + "...");
                setTimeout(() => {
                    window.location.href = '/inventory/asset/' + code;
                }, 1000);
            } 
            else {
                alert('QR Code tidak dikenali: ' + decodedText);
            }
        }

        function registerSubProgramAttendance(qrCode) {
            showToast("Menghubungi GPS & Mencatat Kehadiran...");

            // Fetch current GPS coordinates
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        // Check online status for queue support
                        if (!navigator.onLine) {
                            queueOfflineAttendance(qrCode, lat, lng);
                            return;
                        }

                        // Send AJAX request to store attendance
                        fetch('{{ route("volunteer.subprogram.attendance.scan") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                qr_code: qrCode,
                                latitude: lat,
                                longitude: lng,
                                device_id: navigator.userAgent
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                window.location.reload();
                            } else {
                                alert('⚠️ Gagal Absen: ' + data.message);
                            }
                        })
                        .catch(err => {
                            console.error("Scan attendance error:", err);
                            // Fallback to offline queue on network failures
                            queueOfflineAttendance(qrCode, lat, lng);
                        });
                    },
                    (err) => {
                        alert('⚠️ Gagal scan: GPS tidak diizinkan atau tidak aktif. GPS wajib untuk validasi radius.');
                    },
                    { enableHighAccuracy: true, timeout: 7000 }
                );
            } else {
                alert('⚠️ Perangkat Anda tidak mendukung geolokasi GPS.');
            }
        }

        // ==========================================
        // 2. OFFLINE QUEUE SUPPORT LOGIC
        // ==========================================
        function queueOfflineAttendance(qrCode, lat, lng) {
            let queue = JSON.parse(localStorage.getItem('offline_qr_attendances') || '[]');
            queue.push({
                qr_code: qrCode,
                latitude: lat,
                longitude: lng,
                device_id: navigator.userAgent,
                time: new Date().toISOString()
            });
            localStorage.setItem('offline_qr_attendances', JSON.stringify(queue));
            alert('✈️ Offline Mode Active!\nScan kehadiran Anda disimpan di antrean offline lokal. Kehadiran akan otomatis disinkronkan saat terhubung internet.');
        }

        // Auto Sync when connection returns
        window.addEventListener('online', () => {
            syncOfflineAttendances();
        });

        function syncOfflineAttendances() {
            let queue = JSON.parse(localStorage.getItem('offline_qr_attendances') || '[]');
            if (queue.length === 0) return;

            showToast("🔄 Menyinkronkan " + queue.length + " kehadiran offline...");

            let promises = queue.map((item, index) => {
                return fetch('{{ route("volunteer.subprogram.attendance.scan") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(item)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success || data.outside) {
                        return index; // resolved
                    }
                    return null;
                })
                .catch(() => null);
            });

            Promise.all(promises).then(results => {
                let remainingQueue = queue.filter((_, idx) => !results.includes(idx));
                localStorage.setItem('offline_qr_attendances', JSON.stringify(remainingQueue));
                
                if (remainingQueue.length === 0) {
                    alert('✅ Sinkronisasi kehadiran offline sukses!');
                    window.location.reload();
                } else {
                    showToast("Sebagian data gagal disinkronkan. Mencoba kembali nanti.");
                }
            });
        }

        function showToast(msg) {
            const toast = document.getElementById('global-toast');
            document.getElementById('global-toast-msg').innerText = msg;
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        // Run sync on load if online
        if (navigator.onLine) {
            syncOfflineAttendances();
        }

        // ==========================================
        // 3. HUMAN-CENTERED VOICE ASSISTANT & COMMANDS (V2)
        // ==========================================
        let voiceAssistantActive = false;
        let assistantRecognition = null;

        function toggleVoiceAssistant() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRecognition) {
                alert('⚠️ Peramban Anda tidak mendukung pengenalan suara (Web Speech API).');
                return;
            }

            const micSvg = document.getElementById('voice-assistant-mic-svg');
            const pulse = document.getElementById('voice-assistant-pulse');

            if (voiceAssistantActive) {
                // Deactivate
                voiceAssistantActive = false;
                if (assistantRecognition) {
                    assistantRecognition.stop();
                }
                micSvg.classList.remove('text-red-500');
                micSvg.classList.add('text-white');
                pulse.classList.add('hidden');
                document.getElementById('voice-assistant-fab').classList.remove('bg-red-600', 'hover:bg-red-700');
                document.getElementById('voice-assistant-fab').classList.add('bg-slate-800', 'hover:bg-slate-900');
                showToast("Voice assistant dimatikan");
            } else {
                // Activate
                voiceAssistantActive = true;
                micSvg.classList.remove('text-white');
                micSvg.classList.add('text-red-500');
                pulse.classList.remove('hidden');
                document.getElementById('voice-assistant-fab').classList.remove('bg-slate-800', 'hover:bg-slate-900');
                document.getElementById('voice-assistant-fab').classList.add('bg-red-600', 'hover:bg-red-700');
                
                startListeningCommands(SpeechRecognition);
                showToast("Asisten Suara Aktif. Ucapkan perintah...");
            }
        }

        function startListeningCommands(SpeechRecognition) {
            assistantRecognition = new SpeechRecognition();
            assistantRecognition.lang = 'id-ID';
            assistantRecognition.continuous = true;
            assistantRecognition.interimResults = false;

            assistantRecognition.onresult = (event) => {
                const speechToText = event.results[event.results.length - 1][0].transcript.toLowerCase().trim();
                console.log("Voice Command Recognized:", speechToText);
                showToast(`Mendengar: "${speechToText}"`);
                
                // Route voice commands
                if (speechToText.includes('buka beranda') || speechToText.includes('buka dashboard') || speechToText.includes('dashboard')) {
                    window.location.href = '{{ route("volunteer.dashboard") }}';
                } 
                else if (speechToText.includes('buka tugas') || speechToText.includes('tugas lapangan') || speechToText.includes('tugas')) {
                    window.location.href = '{{ route("volunteer.tasks.index") }}';
                } 
                else if (speechToText.includes('buka logbook') || speechToText.includes('jurnal') || speechToText.includes('logbook')) {
                    window.location.href = '{{ route("volunteer.logbooks.index") }}';
                } 
                else if (speechToText.includes('buka sertifikat') || speechToText.includes('sertifikat')) {
                    window.location.href = '{{ route("volunteer.certificates.index") }}';
                } 
                else if (speechToText.includes('buka kalender') || speechToText.includes('kalender')) {
                    window.location.href = '{{ route("volunteer.workspace.calendar") }}';
                }
                else if (speechToText.includes('buka aduan') || speechToText.includes('aduan') || speechToText.includes('aspirasi')) {
                    window.location.href = '{{ route("volunteer.workspace.complaints.index") }}';
                }
                else if (speechToText.includes('buka rutin') || speechToText.includes('kegiatan rutin') || speechToText.includes('rutinitas')) {
                    window.location.href = '{{ route("volunteer.workspace.routines.index") }}';
                }
                else if (speechToText.includes('buka to do') || speechToText.includes('to do list') || speechToText.includes('tugas harian')) {
                    window.location.href = '{{ route("volunteer.workspace.todos.index") }}';
                }
                else if (speechToText.includes('scan') || speechToText.includes('scan qr') || speechToText.includes('scanner') || speechToText.includes('absen qr')) {
                    openGlobalScanner();
                }
            };

            assistantRecognition.onerror = (e) => {
                console.error("Speech error:", e);
            };

            assistantRecognition.onend = () => {
                if (voiceAssistantActive) {
                    assistantRecognition.start(); // auto restart while toggled active
                }
            };

            assistantRecognition.start();
        }

        // ==========================================
        // 4. SPEECH-TO-TEXT FORM FIELDS INTEGRATION (V2)
        // ==========================================
        document.addEventListener('DOMContentLoaded', () => {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRecognition) return;

            const micSvgMarkup = '<svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>';
            const activeSvgMarkup = '<svg class="w-3.5 h-3.5 text-red-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>';

            // Find all textual input fields and textareas
            const fields = document.querySelectorAll('input[type="text"], textarea');
            fields.forEach(field => {
                // Ensure parent has position relative to anchor microphone
                const parent = field.parentElement;
                if (!parent) return;
                parent.style.position = 'relative';

                // Create floating microphone icon next to input
                const micBtn = document.createElement('button');
                micBtn.type = 'button';
                micBtn.innerHTML = micSvgMarkup;
                micBtn.className = 'absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 transition active:scale-90 text-[11px] p-1 bg-white border border-slate-100 rounded-full shadow-sm flex items-center justify-center w-6 h-6 z-10';
                micBtn.style.marginTop = '2px'; // subtle adjustment
                
                // Listener for Speech-to-Text field filling
                let isListeningField = false;
                let fieldRec = null;

                micBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    // If global assistant is running, pause it temporarily
                    if (voiceAssistantActive && assistantRecognition) {
                        assistantRecognition.stop();
                    }

                    if (isListeningField) {
                        fieldRec.stop();
                    } else {
                        isListeningField = true;
                        micBtn.innerHTML = activeSvgMarkup;
                        micBtn.classList.add('border-red-200');
                        field.placeholder = 'Sedang mendengarkan... Bicara sekarang.';
                        
                        fieldRec = new SpeechRecognition();
                        fieldRec.lang = 'id-ID';
                        fieldRec.continuous = false;
                        fieldRec.interimResults = false;

                        fieldRec.onresult = (evt) => {
                            const resultText = evt.results[0][0].transcript;
                            if (field.value) {
                                field.value += ' ' + resultText;
                            } else {
                                field.value = resultText;
                            }
                            // Dispatch event for AlpineJS / Vue bindings
                            field.dispatchEvent(new Event('input'));
                        };

                        fieldRec.onend = () => {
                            isListeningField = false;
                            micBtn.innerHTML = micSvgMarkup;
                            micBtn.classList.remove('border-red-200');
                            field.placeholder = '';
                            
                            // Resume global assistant if it was active
                            if (voiceAssistantActive && assistantRecognition) {
                                try { assistantRecognition.start(); } catch(err) {}
                            }
                        };

                        fieldRec.onerror = () => {
                            isListeningField = false;
                            micBtn.innerHTML = micSvgMarkup;
                            micBtn.classList.remove('border-red-200');
                        };

                        fieldRec.start();
                    }
                });

                // Append button inside field parent wrapper
                parent.appendChild(micBtn);
                // Give input some right padding so text doesn't overlap the mic button
                field.style.paddingRight = '2.2rem';
            });
        });
    </script>
</body>
</html>
