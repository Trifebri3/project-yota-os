<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>YOTA PROJECT OS - Super Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Leaflet CSS/JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-[#1b1b18] text-white shrink-0 hidden lg:flex flex-col justify-between p-6">
            <div class="space-y-8">
                <!-- Brand logo -->
                <div class="flex items-center gap-3">
                    <img src="/images/logo.webp" alt="Yota Logo" class="w-10 h-10 object-contain bg-white rounded-xl p-1">
                    <div>
                        <h2 class="text-base font-bold tracking-wide">YOTA OS</h2>
                        <span class="text-[9px] font-bold text-[#D9F99D] uppercase tracking-widest">Super Admin panel</span>
                    </div>
                </div>

                <nav class="space-y-6">
                    <div>
                        <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Sistem</span>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('super-admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('super-admin.dashboard') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('super-admin.programs.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('super-admin.programs.*') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    Manajemen Program
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('super-admin.users.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('super-admin.users.*') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    Manajemen User
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('super-admin.zones.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('super-admin.zones.*') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    Zona Geofence
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('super-admin.forms.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('super-admin.forms.*') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    Custom Form Engine
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('super-admin.audits.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('super-admin.audits.*') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    Audit & Compliance
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('super-admin.audit-logs.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('super-admin.audit-logs.*') ? 'bg-[#4CAF50] text-white font-semibold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                                    Audit Trails
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>

            <!-- Footer user info -->
            <div class="border-t border-white/10 pt-4 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold">{{ Auth::user()->name }}</p>
                    <span class="text-[10px] text-gray-500">Super Admin</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Content area -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between lg:justify-end">
                <button type="button" class="lg:hidden p-2 text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-3">
                    <span class="bg-lime-100 text-lime-900 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">Super Administrator</span>
                </div>
            </header>

            <main class="flex-1 p-6 overflow-y-auto">
                @if(session('success'))
                    <div class="mb-6 bg-green-50 text-[#2E7D32] border border-green-200 text-sm font-semibold px-4 py-3 rounded-xl flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
