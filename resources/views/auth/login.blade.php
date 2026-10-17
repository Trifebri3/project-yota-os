<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Yota Project OS</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        .bg-mesh {
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, hsla(142,72%,29%,0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, hsla(142,72%,29%,0.15) 0px, transparent 50%);
        }
    </style>
</head>
<body class="bg-mesh min-h-screen flex items-center justify-center p-4 md:p-8 text-slate-100 antialiased">
    <div class="w-full max-w-4xl bg-slate-900/60 backdrop-blur-md rounded-3xl border border-slate-800 shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2">
        
        <!-- Left Pane: Branding & Intro (Hidden on mobile) -->
        <div class="p-8 md:p-12 bg-gradient-to-tr from-[#166534]/30 to-[#1b1b18]/40 flex flex-col justify-between border-b md:border-b-0 md:border-r border-slate-800">
            <div class="space-y-6">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <img src="/images/logo.webp" alt="Yota Logo" class="w-12 h-12 object-contain bg-white rounded-2xl p-1.5 shadow-md">
                    <div>
                        <h1 class="text-lg font-black tracking-wide leading-none text-white">YOTA OS</h1>
                        <span class="text-[9px] font-bold text-[#4CAF50] uppercase tracking-wider">Social Development Hub</span>
                    </div>
                </div>

                <div class="space-y-4 pt-6">
                    <span class="bg-emerald-500/10 text-[#4CAF50] border border-emerald-500/20 text-[10px] font-bold px-3 py-1.5 rounded-full uppercase tracking-widest inline-block">
                        ESG & SDG Integrated
                    </span>
                    <h2 class="text-2xl font-extrabold text-white leading-snug">Wadah aksi relawan lapangan & pemberdayaan masyarakat.</h2>
                    <p class="text-slate-400 text-xs leading-relaxed">
                        Kelola agenda tim, validasi kehadiran geofenced, catat rekapitulasi emisi karbon offset, dan susun logbook pertanggungjawaban dalam satu platform terpadu.
                    </p>
                </div>
            </div>

            <div class="text-[10px] text-slate-500 pt-8 md:pt-0">
                &copy; 2026 Yota Project OS. Community-driven development.
            </div>
        </div>

        <!-- Right Pane: Login Form -->
        <div class="p-8 md:p-12 flex flex-col justify-center bg-slate-950/20">
            <div class="space-y-2 mb-6">
                <h3 class="text-xl font-bold text-white">Selamat Datang Kembali</h3>
                <p class="text-xs text-slate-400">Silakan login untuk mengakses workspace program Anda.</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 bg-emerald-500/10 text-[#4CAF50] border border-emerald-500/20 text-xs font-semibold px-4 py-3 rounded-xl">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4 text-xs">
                @csrf

                <!-- Email Address -->
                <div class="space-y-1">
                    <label for="email" class="block font-bold text-slate-400 uppercase tracking-wider text-[9px]">Email Workspace</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                           placeholder="name@yota.org" 
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-slate-100 placeholder-slate-600 focus:border-[#4CAF50] focus:ring-0 transition">
                    @if ($errors->get('email'))
                        <p class="text-red-500 text-[10px] mt-1">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <!-- Password -->
                <div class="space-y-1">
                    <div class="flex justify-between items-center">
                        <label for="password" class="block font-bold text-slate-400 uppercase tracking-wider text-[9px]">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[10px] text-[#4CAF50] hover:underline font-semibold">
                                Lupa sandi?
                            </a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password" 
                           placeholder="••••••••" 
                           class="w-full bg-slate-900 border border-slate-800 rounded-xl px-4 py-3 text-slate-100 placeholder-slate-600 focus:border-[#4CAF50] focus:ring-0 transition">
                    @if ($errors->get('password'))
                        <p class="text-red-500 text-[10px] mt-1">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <!-- Remember Me -->
                <label for="remember_me" class="inline-flex items-center gap-2 text-slate-400 hover:text-slate-200 cursor-pointer pt-1">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-800 bg-slate-900 text-[#4CAF50] focus:ring-0">
                    <span class="text-[10px] font-medium">Ingat saya di perangkat ini</span>
                </label>

                <!-- Actions -->
                <div class="pt-2">
                    <button type="submit" class="w-full bg-[#4CAF50] hover:bg-[#2E7D32] text-white font-bold py-3.5 rounded-xl transition active:scale-[0.98] shadow-lg shadow-emerald-950/20 text-sm">
                        Masuk Ke Workspace &rarr;
                    </button>
                </div>

                <!-- Divider -->
                <div class="relative flex items-center justify-center my-2.5">
                    <div class="border-t border-slate-800 w-full"></div>
                    <span class="absolute bg-[#0f172a] px-3.5 text-[9px] text-slate-500 font-bold uppercase tracking-wider">Atau</span>
                </div>

                <!-- QR Login Button -->
                <button type="button" onclick="openQrLoginModal()" class="w-full bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-200 font-bold py-3.5 rounded-xl transition flex items-center justify-center gap-2 text-xs">
                    <svg class="w-4 h-4 text-[#4CAF50]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-4v-4m-4 8H4m0 0v-4m0 4h4m4-12h2m-2 0H8m-2 0H4v4m0-4h4" />
                    </svg>
                    <span>Masuk Cepat via QR Code</span>
                </button>
            </form>
        </div>

    </div>

    <!-- QR Scanner Modal -->
    <div id="qrLoginModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 backdrop-blur-sm hidden p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 max-w-sm w-full shadow-2xl text-center space-y-4 text-xs text-slate-100">
            <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                <h3 class="text-sm font-extrabold text-white uppercase">Scan QR Login Cepat</h3>
                <button type="button" onclick="closeQrLoginModal()" class="text-slate-400 hover:text-slate-200 text-xl font-bold p-1">&times;</button>
            </div>
            
            <p class="text-slate-400 text-[10px]">Hadapkan QR Code "Login Cepat" Anda dari Profil Relawan ke kamera untuk masuk otomatis.</p>
            
            <!-- Camera Scanner view -->
            <div class="relative aspect-square bg-slate-950 border border-slate-800 rounded-2xl overflow-hidden shadow-inner max-w-[250px] mx-auto">
                <div id="qr-login-reader" class="w-full h-full"></div>
                <div class="absolute inset-0 border-2 border-dashed border-[#4CAF50]/30 rounded-2xl pointer-events-none z-10"></div>
            </div>

            <button type="button" onclick="closeQrLoginModal()" class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold rounded-xl transition">
                Batalkan
            </button>
        </div>
    </div>

    <!-- Html5Qrcode Library -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        let html5QrCodeScanner = null;

        function openQrLoginModal() {
            document.getElementById('qrLoginModal').classList.remove('hidden');
            
            setTimeout(() => {
                html5QrCodeScanner = new Html5Qrcode("qr-login-reader");
                html5QrCodeScanner.start(
                    { facingMode: "user" },
                    {
                        fps: 15,
                        qrbox: { width: 180, height: 180 }
                    },
                    (decodedText) => {
                        try {
                            let audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                            let osc = audioCtx.createOscillator();
                            osc.type = 'sine';
                            osc.frequency.setValueAtTime(880, audioCtx.currentTime);
                            osc.connect(audioCtx.destination);
                            osc.start();
                            osc.stop(audioCtx.currentTime + 0.1);
                        } catch(e) {}

                        if (decodedText.startsWith('http://') || decodedText.startsWith('https://')) {
                            if (decodedText.includes('/quick-login/')) {
                                window.location.href = decodedText;
                            } else {
                                alert('QR Code terdeteksi bukan QR Login Cepat YOTA OS.');
                            }
                        } else {
                            alert('Format QR Code tidak valid.');
                        }
                        closeQrLoginModal();
                    },
                    (errorMessage) => {}
                ).catch(err => {
                    html5QrCodeScanner.start(
                        { facingMode: "environment" },
                        {
                            fps: 15,
                            qrbox: { width: 180, height: 180 }
                        },
                        (decodedText) => {
                            if (decodedText.startsWith('http://') || decodedText.startsWith('https://')) {
                                if (decodedText.includes('/quick-login/')) {
                                    window.location.href = decodedText;
                                } else {
                                    alert('QR Code terdeteksi bukan QR Login Cepat YOTA OS.');
                                }
                            } else {
                                alert('Format QR Code tidak valid.');
                            }
                            closeQrLoginModal();
                        },
                        (errorMessage) => {}
                    ).catch(e => {
                        alert('Gagal mengakses kamera. Pastikan izin kamera aktif.');
                        closeQrLoginModal();
                    });
                });
            }, 300);
        }

        function closeQrLoginModal() {
            if (html5QrCodeScanner) {
                try {
                    html5QrCodeScanner.stop().then(() => {
                        html5QrCodeScanner = null;
                    }).catch(e => {
                        html5QrCodeScanner = null;
                    });
                } catch(e) {
                    html5QrCodeScanner = null;
                }
            }
            document.getElementById('qrLoginModal').classList.add('hidden');
        }
    </script>
</body>
</html>
