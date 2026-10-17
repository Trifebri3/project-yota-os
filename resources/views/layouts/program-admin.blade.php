<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Yota OS - Admin Workspace</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Leaflet CSS/JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <meta name="theme-color" content="#4CAF50">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/images/logo.webp">

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
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-[#1b1b18] text-white shrink-0 hidden lg:flex flex-col justify-between p-6">
            <div class="space-y-8">
                <!-- Brand logo -->
                <div class="flex items-center gap-3">
                    <img src="/images/logo.webp" alt="Yota Logo" class="w-10 h-10 object-contain bg-white rounded-xl p-1">
                    <div>
                        <h2 class="text-base font-bold tracking-wide">YOTA OS</h2>
                        <p class="text-[9px] font-bold text-[#D9F99D] uppercase tracking-widest">Admin Workspace</p>
                    </div>
                </div>

                <button id="pwa-install-btn" style="display: none;" class="w-full bg-[#4CAF50] text-white text-[10px] font-bold py-2 rounded-xl hover:bg-[#388e3c] transition flex items-center justify-center gap-1.5 shadow-md">
                    ⬇️ Install Admin App
                </button>

                <!-- Navigation menus divided by workspaces -->
                <nav class="space-y-6">
                    <div>
                        <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Workspaces</span>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('program-admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('program-admin.dashboard') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.project-manager.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('program-admin.project-manager.*') || request()->routeIs('program-admin.charter.*') || request()->routeIs('program-admin.workplans.*') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Project Manager
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.operations.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('program-admin.operations.index') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    Jadwal Kegiatan
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.operations.timeline') }}" class="flex items-center gap-3 pl-8 py-2 rounded-xl text-xs transition {{ request()->routeIs('program-admin.operations.timeline') ? 'text-[#D9F99D] font-bold' : 'text-gray-400 hover:text-white' }}">
                                    • Agenda Kalender & PJ
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.operations.routines') }}" class="flex items-center gap-3 pl-8 py-2 rounded-xl text-xs transition {{ request()->routeIs('program-admin.operations.routines') ? 'text-[#D9F99D] font-bold' : 'text-gray-400 hover:text-white' }}">
                                    • Checklist Rutinitas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.operations.additional-logs') }}" class="flex items-center gap-3 pl-8 py-2 rounded-xl text-xs transition {{ request()->routeIs('program-admin.operations.additional-logs') ? 'text-[#D9F99D] font-bold' : 'text-gray-400 hover:text-white' }}">
                                    • Aktivitas Tambahan
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.carbon.fill') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('program-admin.carbon.*') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-11.314l.707.707m11.314 11.314l.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z"/></svg>
                                    Input Karbon Kelompok
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.finance.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('program-admin.finance.*') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Finance & SPJ
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.administration.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('program-admin.administration.index') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Administration & LPJ
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.inventory.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('program-admin.inventory.*') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    Inventory Management
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.scan-volunteer.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('program-admin.scan-volunteer.*') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-4v-4m-4 8H4m0 0v-4m0 4h4m4-12h2m-2 0H8m-2 0H4v4m0-4h4"/></svg>
                                    Scan Presensi Relawan
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.administration.resources') }}" class="flex items-center gap-3 pl-8 py-2 rounded-xl text-xs transition {{ request()->routeIs('program-admin.administration.resources') ? 'text-[#D9F99D] font-bold' : 'text-gray-400 hover:text-white' }}">
                                    • SOP & Modul Resource
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.administration.certificates') }}" class="flex items-center gap-3 pl-8 py-2 rounded-xl text-xs transition {{ request()->routeIs('program-admin.administration.certificates') ? 'text-[#D9F99D] font-bold' : 'text-gray-400 hover:text-white' }}">
                                    • Piagam & Sertifikat
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.administration.facilities') }}" class="flex items-center gap-3 pl-8 py-2 rounded-xl text-xs transition {{ request()->routeIs('program-admin.administration.facilities') ? 'text-[#D9F99D] font-bold' : 'text-gray-400 hover:text-white' }}">
                                    • Inventaris & Atribut
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.administration.complaints') }}" class="flex items-center gap-3 pl-8 py-2 rounded-xl text-xs transition {{ request()->routeIs('program-admin.administration.complaints') ? 'text-[#D9F99D] font-bold' : 'text-gray-400 hover:text-white' }}">
                                    • Pengaduan Relawan
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.administration.reprimands') }}" class="flex items-center gap-3 pl-8 py-2 rounded-xl text-xs transition {{ request()->routeIs('program-admin.administration.reprimands') ? 'text-[#D9F99D] font-bold' : 'text-gray-400 hover:text-white' }}">
                                    • Teguran & Disiplin
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.administration.daily-reports') }}" class="flex items-center gap-3 pl-8 py-2 rounded-xl text-xs transition {{ request()->routeIs('program-admin.administration.daily-reports') ? 'text-[#D9F99D] font-bold' : 'text-gray-400 hover:text-white' }}">
                                    • Daily Situation Reports
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.administration.todos') }}" class="flex items-center gap-3 pl-8 py-2 rounded-xl text-xs transition {{ request()->routeIs('program-admin.administration.todos') ? 'text-[#D9F99D] font-bold' : 'text-gray-400 hover:text-white' }}">
                                    • Manajemen To-Do List
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.attendance.print') }}" target="_blank" class="flex items-center gap-3 pl-8 py-2 rounded-xl text-xs transition {{ request()->routeIs('program-admin.attendance.print') ? 'text-[#D9F99D] font-bold' : 'text-gray-400 hover:text-white' }}">
                                    • Cetak Kehadiran Lapangan
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Verifikasi Lapangan</span>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('program-admin.attendance.validation') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('program-admin.attendance.validation') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    Absensi Luar Zona
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.tasks.approval') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('program-admin.tasks.approval') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    Laporan Tugas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('program-admin.logbooks.approval') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('program-admin.logbooks.approval') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    Logbook Kegiatan
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Pengaturan</span>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('program-admin.settings.profile') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('program-admin.settings.profile') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    Profile & TTD
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>

            <!-- Footer block user info -->
            <div class="border-t border-white/10 pt-4 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold">{{ Auth::user()->name }}</p>
                    <span class="text-[10px] text-gray-500">{{ Auth::user()->role === 'viewer' ? 'Viewer Mode' : 'Program Admin' }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar (Mobile menu trigger) -->
            <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between lg:justify-end">
                <button type="button" class="lg:hidden p-2 text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-slate-500">Workspace Aktif</span>
                    <span class="bg-lime-100 text-lime-900 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">CSR & Social Impact</span>
                </div>
            </header>

            <main class="flex-1 p-6 overflow-y-auto">
                @if(session('success'))
                    <div class="mb-6 bg-green-50 text-[#2E7D32] border border-green-200 text-sm font-semibold px-4 py-3 rounded-xl flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#2E7D32]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 text-[#C62828] border border-red-200 text-sm font-semibold px-4 py-3 rounded-xl flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#C62828]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="mb-6 bg-amber-50 text-[#D84315] border border-amber-200 text-sm font-semibold px-4 py-3 rounded-xl flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#D84315]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 bg-red-50 text-[#C62828] border border-red-200 text-sm font-semibold px-4 py-3 rounded-xl space-y-1">
                        <div class="flex items-center gap-2 font-bold text-[#B71C1C]">
                            <svg class="w-5 h-5 text-[#B71C1C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            <span>Gagal memvalidasi data:</span>
                        </div>
                        <ul class="list-disc pl-6 space-y-0.5 text-xs text-red-800">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- Floating Admin Scan Volunteer Button -->
    <a href="{{ route('program-admin.scan-volunteer.index') }}" class="fixed bottom-6 right-6 bg-[#4CAF50] hover:bg-[#2E7D32] text-white p-3.5 rounded-full shadow-2xl hover:shadow-lime-300/50 transition duration-300 z-50 flex items-center justify-center group active:scale-95 border-2 border-white" title="Scan Presensi Relawan">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-4v-4m-4 8H4m0 0v-4m0 4h4m4-12h2m-2 0H8m-2 0H4v4m0-4h4" />
        </svg>
        <span class="max-w-0 overflow-hidden group-hover:max-w-xs group-hover:ml-2 transition-all duration-300 ease-out text-[10px] font-bold whitespace-nowrap block">
            Scan Presensi
        </span>
    </a>

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
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the PWA install prompt');
                    } else {
                        console.log('User dismissed the PWA install prompt');
                    }
                    deferredPrompt = null;
                });
            });
        }

        window.addEventListener('appinstalled', (evt) => {
            console.log('PWA installed successfully!');
            if (installBtn) installBtn.style.display = 'none';
        });
    </script>
</body>
</html>
