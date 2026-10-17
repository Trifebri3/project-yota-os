<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Yota OS - Viewer & Monitoring Workspace</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Leaflet CSS/JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <meta name="theme-color" content="#0EA5E9">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/images/logo.webp">
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen">
    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebar-backdrop" onclick="toggleMobileSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar Navigation (Responsive Drawer) -->
        <aside id="viewer-sidebar" class="w-64 bg-[#0F172A] text-white shrink-0 fixed inset-y-0 left-0 z-50 flex flex-col justify-between p-6 transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-300 ease-in-out">
            <div class="space-y-8">
                <!-- Brand logo with mobile close button -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="/images/logo.webp" alt="Yota Logo" class="w-10 h-10 object-contain bg-white rounded-xl p-1">
                        <div>
                            <h2 class="text-base font-bold tracking-wide">YOTA OS</h2>
                            <p class="text-[9px] font-bold text-sky-400 uppercase tracking-widest">Viewer Workspace</p>
                        </div>
                    </div>
                    <!-- Close button for mobile screens -->
                    <button onclick="toggleMobileSidebar()" class="lg:hidden text-slate-400 hover:text-white text-xl p-1 px-2.5 rounded-xl hover:bg-white/5 transition">&times;</button>
                </div>

                <!-- Navigation menus -->
                <nav class="space-y-6">
                    <div>
                        <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Monitoring Menu</span>
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route('viewer.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm transition {{ request()->routeIs('viewer.dashboard') ? 'bg-sky-600 text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    Global Dashboard
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Daftar Program Kerja</span>
                        <ul class="space-y-1 max-h-60 overflow-y-auto pr-1 text-slate-400 text-xs">
                            @php
                                $sidebarPrograms = \App\Models\Program::all();
                            @endphp
                            @foreach($sidebarPrograms as $sp)
                                <li>
                                    <a href="{{ route('viewer.programs.show', $sp->id) }}" class="block px-4 py-2 rounded-lg transition truncate hover:text-white hover:bg-white/5 {{ request()->is('viewer/programs/' . $sp->id) ? 'text-sky-400 font-semibold bg-white/5' : '' }}" title="{{ $sp->name }}">
                                        {{ $sp->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </nav>
            </div>

            <!-- Footer block user info -->
            <div class="border-t border-white/10 pt-4 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold">{{ Auth::user()->name }}</p>
                    <span class="text-[9px] bg-sky-950 text-sky-400 font-extrabold px-2 py-0.5 rounded-full uppercase tracking-wider">Viewer Mode</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-500 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between lg:justify-end">
                <button onclick="toggleMobileSidebar()" type="button" class="lg:hidden p-2 text-gray-500 hover:bg-slate-100 rounded-xl transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-slate-500">Workspace Aktif:</span>
                    <span class="bg-sky-100 text-sky-900 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">Monitoring & Tracking</span>
                </div>
            </header>

            <main class="flex-1 p-6 overflow-y-auto">
                @if(session('success'))
                    <div class="mb-6 bg-green-50 text-[#2E7D32] border border-green-200 text-sm font-semibold px-4 py-3 rounded-xl flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 text-[#C62828] border border-red-200 text-sm font-semibold px-4 py-3 rounded-xl flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    <!-- JavaScript Mobile Drawer Toggle -->
    <script>
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('viewer-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }
    </script>
</body>
</html>
