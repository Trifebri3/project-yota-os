<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $program->name }} — Platform Program Yota Adiwidya Center</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Leaflet Map CSS/JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <style>
        body { 
            font-family: 'Outfit', sans-serif; 
            background-color: #f8fafc; 
            color: #0f172a;
        }
        .glow-accent-light {
            background: radial-gradient(circle at 50% 0%, rgba(16, 185, 129, 0.10) 0%, rgba(14, 165, 233, 0.06) 40%, transparent 70%);
        }
        .seamless-card {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 4px 12px -2px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .polaroid-card {
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 8px 25px -4px rgba(0, 0, 0, 0.06), 0 2px 6px -1px rgba(0, 0, 0, 0.03);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .polaroid-card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 20px 35px -8px rgba(16, 185, 129, 0.15), 0 6px 12px -2px rgba(0, 0, 0, 0.04);
        }
        .custom-scroll::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .custom-scroll::-webkit-scrollbar-thumb:hover {
            background: #10b981;
        }
        @keyframes float-gentle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }
        .animate-float {
            animation: float-gentle 3.5s ease-in-out infinite;
        }
    </style>
</head>
<body class="antialiased min-h-screen bg-slate-50 text-slate-800 flex flex-col justify-between overflow-x-hidden select-none"
      x-data="impactDeck({
          totalSlides: 9,
          currentSlide: 0,
          slideInterval: 8000,
          idleTimeout: 10000,
          activeDayIndex: 0,
          totalDays: {{ count($dailyGroups) }},
          activeVolunteerFilter: 'all',
          activeLogDetail: null,
          activeMilestoneIndex: 0,
          activeBeforeAfterIndex: 0,
          activeLocationPhotoIndex: 0,
          beforeAfterPos: 50
      })"
      @mousemove="onUserMovement()"
      @keydown="onUserMovement()"
      @touchstart="onUserMovement()"
      @click="onUserClick($event)">

    <!-- Ambient Subtle Glow Background -->
    <div class="fixed top-0 left-0 right-0 h-[500px] glow-accent-light pointer-events-none -z-10"></div>

    <!-- Top Autoplay Progress Bar -->
    <div class="fixed top-0 left-0 right-0 h-1 bg-slate-200/80 z-50">
        <div class="h-full bg-gradient-to-r from-emerald-500 via-teal-500 to-sky-500 transition-all ease-linear shadow-sm"
             :style="'width: ' + progressPercent + '%'"></div>
    </div>

    <!-- Header Navigation & YOTA ADIWIDYA CENTER Brand Identity -->
    <header class="w-full px-4 sm:px-8 py-3.5 bg-white/90 backdrop-blur-md border-b border-slate-200/70 z-40">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3 sm:gap-5">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-slate-500 hover:text-emerald-700 transition group text-xs font-bold">
                    <span class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 group-hover:bg-emerald-600 group-hover:text-white transition shadow-sm">&larr;</span>
                    <span class="hidden sm:inline">Katalog Program</span>
                </a>

                <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>

                <!-- YOTA ADIWIDYA CENTER Official Title & Emblem -->
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center font-black text-xs shadow-md shadow-emerald-500/20">
                        Y
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs sm:text-sm font-black text-slate-900 tracking-tight">
                                YOTA ADIWIDYA CENTER
                            </span>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium block -mt-0.5 truncate max-w-[200px] sm:max-w-md">
                            Platform Program: {{ $program->name }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Slideshow Mode Controls -->
            <div class="flex items-center gap-2">
                <!-- Evidence Review & Official Report Document Button -->
                <a href="{{ route('public.projects.evidence-report', $program->id) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-900 hover:bg-emerald-700 text-white text-[11px] font-bold shadow-sm transition">
                    <span>📑</span>
                    <span class="hidden md:inline">Laporan & Tinjauan Bukti (v1.0)</span>
                    <span class="md:hidden">Laporan v1.0</span>
                </a>

                <!-- Status Badge (Autoplay vs Paused) -->
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 text-[11px] font-mono">
                    <template x-if="isPaused">
                        <span class="flex items-center gap-1.5 text-amber-600 font-bold">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span class="hidden sm:inline">Interaksi Aktif (Jeda)</span>
                            <span class="sm:hidden">Jeda</span>
                        </span>
                    </template>
                    <template x-if="!isPaused">
                        <span class="flex items-center gap-1.5 text-emerald-600 font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            <span class="hidden sm:inline">Slideshow Otomatis</span>
                            <span class="sm:hidden">Auto</span>
                        </span>
                    </template>
                </div>

                <!-- Play/Pause Toggle -->
                <button 
                    @click.stop="togglePlayPause()" 
                    class="p-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 transition shadow-sm active:scale-95"
                    title="Jeda / Putar Slideshow">
                    <template x-if="!isPaused">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                    </template>
                    <template x-if="isPaused">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </template>
                </button>

                <!-- Fullscreen Toggle -->
                <button 
                    @click.stop="toggleFullscreen()" 
                    class="p-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 transition shadow-sm active:scale-95"
                    title="Layar Penuh (Mode Kiosk / Pameran)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                </button>
            </div>
        </div>
    </header>

    <!-- ========================================== -->
    <!-- MAIN SEAMLESS STAGE (SUPER SEAMLESS CANVAS)-->
    <!-- ========================================== -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 flex flex-col justify-center relative min-h-[600px]">
        
        <!-- ========================================== -->
        <!-- SLIDE 0: NAMA PROGRAM & IDENTITAS (FOTO)   -->
        <!-- ========================================== -->
        <div x-show="currentSlide === 0" 
             x-transition:enter="transition-all ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-3 scale-99"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <!-- Left: Program Info -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="bg-emerald-100 text-emerald-800 text-[11px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                            {{ $program->kategori_program ?? 'Program Lingkungan & ESG' }}
                        </span>
                        <span class="bg-slate-200/80 text-slate-700 text-[11px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            Mitra: {{ $program->mitra ?? 'DLH Jabar & Tokoh Nelayan' }}
                        </span>
                    </div>

                    <h2 class="text-4xl sm:text-5xl font-black text-slate-900 leading-[1.15] tracking-tight">
                        {{ $program->name }}
                    </h2>

                    <p class="text-slate-600 text-base leading-relaxed max-w-2xl">
                        {{ $program->description }}
                    </p>

                    <!-- Seamless Floating Metric Pills (No Heavy Boxes!) -->
                    <div class="grid grid-cols-3 gap-4 pt-2">
                        <div class="seamless-card p-4 text-center hover:scale-105 transition">
                            <span class="text-2xl block mb-1">🌱</span>
                            <strong class="block text-2xl font-black text-emerald-600">
                                {{ number_format($program->stats['carbon_offset'] ?? 0) }} Kg
                            </strong>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Reduksi Karbon CO₂e</span>
                        </div>
                        <div class="seamless-card p-4 text-center hover:scale-105 transition">
                            <span class="text-2xl block mb-1">👥</span>
                            <strong class="block text-2xl font-black text-slate-900">
                                {{ number_format($program->stats['beneficiaries_count'] ?? 0) }} Jiwa
                            </strong>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Penerima Manfaat</span>
                        </div>
                        <div class="seamless-card p-4 text-center hover:scale-105 transition">
                            <span class="text-2xl block mb-1">⚡</span>
                            <strong class="block text-2xl font-black text-slate-900">
                                {{ number_format($program->stats['volunteers_count'] ?? 0) }} Orang
                            </strong>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Relawan Lapangan</span>
                        </div>
                    </div>

                    @if($program->projectCharter?->vision)
                        <div class="flex items-start gap-3 p-4 bg-emerald-50/80 rounded-2xl border-l-4 border-emerald-500 text-xs text-slate-700">
                            <span class="text-emerald-600 font-bold text-sm">❝</span>
                            <div>
                                <strong class="text-emerald-800 font-bold block uppercase text-[10px] tracking-wider">Visi Program</strong>
                                <p class="italic mt-0.5">{{ $program->projectCharter->vision }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Official Document Badge & Direct Action Link -->
                    <div class="seamless-card p-4 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-lg">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="bg-emerald-500 text-slate-950 font-black text-[9px] uppercase px-2 py-0.5 rounded">Versi 1.0</span>
                                <strong class="text-xs font-black tracking-tight">Dokumen Kerja: Laporan Program & Tinjauan Bukti</strong>
                            </div>
                            <p class="text-[11px] text-slate-300">
                                372 kg Sampah Pesisir &bull; 400 kg Residu Tungku &bull; 12 Anggota Bale Resik &bull; 83 UMKM &bull; RAB Rp15.6M
                            </p>
                        </div>
                        <a href="{{ route('public.projects.evidence-report', $program->id) }}" target="_blank"
                           class="shrink-0 bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-black px-4 py-2 rounded-xl transition flex items-center gap-1.5 shadow">
                            <span>Buka Laporan & Bukti</span> &rarr;
                        </a>
                    </div>
                </div>

                <!-- Right: Hero Banner (Full-Bleed Rounded) -->
                <div class="lg:col-span-5">
                    <div class="rounded-3xl overflow-hidden aspect-[4/3] shadow-2xl relative group">
                        <img src="{{ $program->cover_image ?? '/images/projects/mangrove.jpg' }}" 
                             alt="{{ $program->name }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent flex items-end p-6">
                            <div>
                                <span class="bg-emerald-500/90 text-white text-[10px] font-black uppercase px-2.5 py-1 rounded-md mb-1.5 inline-block">Tapak Aksi Resmi</span>
                                <h3 class="text-base text-white font-bold drop-shadow">{{ $program->projectCharter?->scope ?? 'Garut Selatan / Jawa Barat' }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SLIDE 1: LOKASI DAN FOTO                   -->
        <!-- ========================================== -->
        <div x-show="currentSlide === 1" 
             x-transition:enter="transition-all ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-3 scale-99"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="w-full">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-5 space-y-5">
                    <div>
                        <span class="bg-sky-100 text-sky-800 text-[11px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                            Lokasi & Foto Tapak
                        </span>
                        <h3 class="text-3xl font-black text-slate-900 mt-2">
                            Peta Tapak & Dokumentasi Lapangan
                        </h3>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                            Aksi divalidasi dengan radius GPS presisi tinggi dan batas poligon geofence desa binaan.
                        </p>
                    </div>

                    <!-- Geofence Zone Chips -->
                    <div class="space-y-2">
                        @forelse($program->zones as $zone)
                            <div class="seamless-card p-3.5 flex items-center justify-between">
                                <div>
                                    <strong class="text-slate-900 text-xs block">{{ $zone->name }}</strong>
                                    <span class="text-[10px] text-slate-400">{{ $zone->description }}</span>
                                </div>
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase">
                                    {{ $zone->status_zona ?? 'Aktif' }}
                                </span>
                            </div>
                        @empty
                            <div class="seamless-card p-3 text-xs text-slate-400">Zona Geofence Purbayani</div>
                        @endforelse
                    </div>

                    <!-- Real Location Photo Gallery -->
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2">Dokumentasi Lokasi:</span>
                        <div class="grid grid-cols-3 gap-2.5">
                            @foreach($locationPhotos->take(3) as $lIdx => $lPhoto)
                                <div @click.stop="activeLocationPhotoIndex = {{ $lIdx }}"
                                     :class="activeLocationPhotoIndex === {{ $lIdx }} ? 'ring-2 ring-emerald-500 scale-105 shadow-md' : 'opacity-75 hover:opacity-100'"
                                     class="rounded-2xl overflow-hidden aspect-video cursor-pointer transition shadow-sm bg-slate-100">
                                    <img src="{{ $lPhoto['url'] }}" alt="{{ $lPhoto['caption'] }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <div class="rounded-3xl overflow-hidden shadow-2xl relative bg-white">
                        <div id="deck-map" class="w-full h-[360px] rounded-3xl z-10"></div>
                        <div class="absolute bottom-4 right-4 z-20 bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-xl border border-slate-200 text-[11px] text-slate-700 shadow-md">
                            📍 Koordinat: <span class="text-emerald-700 font-mono font-bold">-6.089000, 106.720000</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SLIDE 2: NAMA ORANG-ORANG                  -->
        <!-- (HANYA VOLUNTEER & PROJECT OFFICER)       -->
        <!-- ========================================== -->
        <div x-show="currentSlide === 2" 
             x-transition:enter="transition-all ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-3 scale-99"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="w-full space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
                <div>
                    <span class="bg-purple-100 text-purple-800 text-[11px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                        Tim Relawan & Project Officer
                    </span>
                    <h3 class="text-3xl font-black text-slate-900 mt-1">
                        Para Penggerak Aksi Lapangan
                    </h3>
                </div>
                <div class="text-xs text-slate-500 font-bold bg-white px-3 py-1.5 rounded-full shadow-sm">
                    ✨ {{ $assignedUsers->count() }} Relawan & Project Officer Terverifikasi
                </div>
            </div>

            <!-- Playful Polaroid-Style Person Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 max-h-[460px] overflow-y-auto custom-scroll pr-1 pb-4">
                @forelse($assignedUsers as $person)
                    <div class="polaroid-card p-4 text-center space-y-3 flex flex-col items-center group">
                        <!-- Selfie Photo Bubble with Halos -->
                        <div class="w-20 h-20 rounded-full overflow-hidden bg-slate-100 border-4 border-white shadow-md group-hover:scale-110 transition duration-300 relative">
                            @if($person->avatar_photo)
                                <img src="{{ $person->avatar_photo }}" alt="{{ $person->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gradient-to-tr from-emerald-400 to-teal-500 flex items-center justify-center font-black text-white text-xl">
                                    {{ substr($person->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="w-full min-w-0">
                            <strong class="text-xs font-bold text-slate-900 block truncate" title="{{ $person->name }}">
                                {{ $person->name }}
                            </strong>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full mt-1 inline-block uppercase {{ strtolower($person->role) === 'project-officer' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ strtolower($person->role) === 'project-officer' ? '⭐ Project Officer' : '🌱 Relawan' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-6 text-center py-12 text-slate-400 text-xs">Belum ada tim relawan terdaftar.</div>
                @endforelse
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SLIDE 3: JEJAK KARBON                     -->
        <!-- ========================================== -->
        <div x-show="currentSlide === 3" 
             x-transition:enter="transition-all ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-3 scale-99"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="w-full space-y-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
                <div>
                    <span class="bg-teal-100 text-teal-800 text-[11px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                        Sains & Dekonstruksi Karbon
                    </span>
                    <h3 class="text-3xl font-black text-slate-900 mt-1">
                        Jejak Karbon & Emisi Bersih (IPCC Tier 1)
                    </h3>
                </div>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full shadow-sm">
                    Yota Carbon Engine Verified
                </span>
            </div>

            <!-- Big Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
                <div class="seamless-card p-8 space-y-2 hover:scale-105 transition">
                    <span class="text-3xl block mb-2">🚗</span>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Emisi Operasional</span>
                    <strong class="text-4xl font-black text-slate-800 block">{{ number_format($carbonMetrics['total_emissions'], 1) }}</strong>
                    <span class="text-xs text-slate-500">Kg CO₂e (Perjalanan relawan & listrik)</span>
                </div>

                <div class="seamless-card p-8 space-y-2 bg-gradient-to-br from-emerald-50 to-teal-50/50 hover:scale-105 transition shadow-lg">
                    <span class="text-3xl block mb-2">♻️</span>
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider block">Emisi Dihindari (Avoided)</span>
                    <strong class="text-4xl font-black text-emerald-600 block">{{ number_format($carbonMetrics['total_offsets'], 1) }}</strong>
                    <span class="text-xs text-emerald-700 font-medium">Kg CO₂e (Daur ulang & komposting)</span>
                </div>

                <div class="seamless-card p-8 space-y-2 hover:scale-105 transition">
                    <span class="text-3xl block mb-2">🌳</span>
                    <span class="text-xs font-bold text-teal-800 uppercase tracking-wider block">Potensi Karbon Diserap</span>
                    <strong class="text-4xl font-black text-teal-600 block">310.0</strong>
                    <span class="text-xs text-teal-700 font-medium">Kg CO₂e (Potensi reduksi daur ulang & maggot BSF)</span>
                </div>
            </div>

            <div class="seamless-card p-6 text-xs text-slate-600 leading-relaxed max-w-3xl mx-auto text-center">
                <strong class="text-slate-900 block mb-1">Metodologi Ilmiah Pengukuran:</strong>
                Emisi dihitung otomatis dari logbook transportasi dan operasional, sedangkan reduksi karbon dihitung dari timbulan 372 kg sampah pesisir yang dipindahkan ke TPS3R, pengomposan sampah organik pasar, serta pengolahan residu ber-SOP di Bale Resik.
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SLIDE 4: CERITA HARIAN DAY 1 S/D AKHIR     -->
        <!-- (SUPER SEAMLESS, ENAK NAVIGASI & "LUCU")   -->
        <!-- ========================================== -->
        <div x-show="currentSlide === 4" 
             x-transition:enter="transition-all ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-3 scale-99"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="w-full space-y-5">

            <!-- Charming Header & Super Easy Day Navigation Controller -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white/80 backdrop-blur-md p-4 rounded-3xl shadow-sm border border-slate-100">
                <!-- Day Stepper Controller (Previous / Current Day / Next) -->
                <div class="flex items-center gap-2">
                    <button 
                        @click.stop="activeDayIndex = Math.max(0, Number(activeDayIndex) - 1)" 
                        :disabled="Number(activeDayIndex) === 0"
                        :class="Number(activeDayIndex) === 0 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-200 active:scale-95'"
                        class="p-2.5 rounded-full bg-slate-100 text-slate-700 font-bold transition shadow-sm"
                        title="Hari Sebelumnya">
                        &larr;
                    </button>

                    <!-- Active Day Badge & Title -->
                    <div class="px-4 py-1.5 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center gap-2">
                        <span class="text-lg">🌟</span>
                        <div>
                            <strong class="text-xs sm:text-sm font-black text-slate-900 block" x-text="dailyGroupTitles[Number(activeDayIndex)] || 'Hari Kegiatan'"></strong>
                            <span class="text-[10px] text-emerald-700 font-mono block -mt-0.5" x-text="'Hari ke-' + (Number(activeDayIndex) + 1) + ' dari ' + totalDays + ' &bull; ' + (dailyGroupDates[Number(activeDayIndex)] || '')"></span>
                        </div>
                    </div>

                    <button 
                        @click.stop="activeDayIndex = Math.min(totalDays - 1, Number(activeDayIndex) + 1)" 
                        :disabled="Number(activeDayIndex) >= totalDays - 1"
                        :class="Number(activeDayIndex) >= totalDays - 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-200 active:scale-95'"
                        class="p-2.5 rounded-full bg-slate-100 text-slate-700 font-bold transition shadow-sm"
                        title="Hari Selanjutnya">
                        &rarr;
                    </button>
                </div>

                <!-- Quick Milestone Jump Dropdown (NO 41 TINY BUTTONS!) -->
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider hidden sm:inline">Lompat Cepat:</span>
                    <select 
                        x-model.number="activeDayIndex" 
                        class="text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-xl border-0 focus:ring-2 focus:ring-emerald-500 cursor-pointer shadow-sm">
                        @foreach($dailyGroups as $dIdx => $dG)
                            <option :value="{{ $dIdx }}" value="{{ $dIdx }}">
                                {{ $dG['formatted_day'] }} — {{ Str::limit($dG['primary_title'], 28) }} ({{ $dG['total_photos'] }} foto)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Cute Volunteer Filter Avatar Badges -->
            <div class="flex items-center gap-2 overflow-x-auto py-1 custom-scroll">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1">Relawan:</span>
                <button 
                    @click.stop="activeVolunteerFilter = 'all'" 
                    :class="activeVolunteerFilter === 'all' ? 'bg-slate-900 text-white font-bold shadow-md' : 'bg-white hover:bg-slate-100 text-slate-700 font-medium border border-slate-200'"
                    class="px-3 py-1.5 rounded-full text-[11px] shrink-0 transition flex items-center gap-1.5">
                    <span>✨ Semua Tim</span>
                </button>
                @foreach($uniqueVolunteers->take(10) as $vol)
                    <button 
                        @click.stop="activeVolunteerFilter = '{{ $vol['id'] }}'" 
                        :class="activeVolunteerFilter === '{{ $vol['id'] }}' ? 'bg-emerald-600 text-white font-bold shadow-md' : 'bg-white hover:bg-slate-100 text-slate-700 font-medium border border-slate-200'"
                        class="px-3 py-1.5 rounded-full text-[11px] shrink-0 transition flex items-center gap-1.5">
                        <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-800 text-[8px] font-black flex items-center justify-center">
                            {{ substr($vol['name'], 0, 1) }}
                        </span>
                        <span>{{ $vol['name'] }}</span>
                    </button>
                @endforeach
            </div>

            <!-- Multi-Photo Visual Story Wall (Banyak Gambar, Super Enak Dilihat, Lucu) -->
            @foreach($dailyGroups as $dIdx => $dGroup)
                <div x-show="Number(activeDayIndex) === {{ $dIdx }}" x-transition class="space-y-4">
                    <!-- Photo Grid with Polaroid Feel & Cheerful Notes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 max-h-[500px] overflow-y-auto custom-scroll pr-1 pb-4">
                        @foreach($dGroup['logs'] as $logItem)
                            <div x-show="activeVolunteerFilter === 'all' || activeVolunteerFilter === '{{ $logItem['user_id'] }}'"
                                 class="polaroid-card p-3 space-y-3 flex flex-col justify-between group cursor-pointer"
                                 @click.stop="activeLogDetail = {{ json_encode($logItem) }}">
                                
                                <!-- Big Charming Photo with Tape / Badge -->
                                <div class="aspect-[4/3] w-full rounded-xl overflow-hidden bg-slate-100 relative shadow-inner">
                                    <img src="{{ $logItem['photo_path'] }}" alt="{{ $logItem['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    <div class="absolute top-2.5 left-2.5 bg-white/90 backdrop-blur-sm px-2.5 py-0.5 rounded-full text-[9px] font-bold text-slate-700 shadow-sm flex items-center gap-1">
                                        <span>📷</span>
                                        <span>Foto Asli Logbook</span>
                                    </div>
                                    <div class="absolute bottom-2.5 right-2.5 bg-slate-900/80 text-white text-[9px] font-mono px-2 py-0.5 rounded-md">
                                        {{ $logItem['time'] ?? '10:00' }}
                                    </div>
                                </div>

                                <!-- Volunteer Info & Cute Speech Bubble Note -->
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black flex items-center justify-center shrink-0 shadow-sm">
                                                {{ substr($logItem['user_name'], 0, 1) }}
                                            </div>
                                            <strong class="text-xs font-bold text-slate-900 truncate" title="{{ $logItem['user_name'] }}">
                                                {{ $logItem['user_name'] }}
                                            </strong>
                                        </div>
                                        <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 uppercase">
                                            {{ $logItem['user_role'] }}
                                        </span>
                                    </div>

                                    <h4 class="text-xs font-extrabold text-slate-800 line-clamp-1 leading-snug">
                                        {{ $logItem['title'] }}
                                    </h4>

                                    <!-- Speech Bubble Note -->
                                    <div class="p-2.5 bg-slate-50/90 rounded-xl text-[11px] text-slate-600 line-clamp-2 leading-relaxed relative border-l-2 border-emerald-400">
                                        {{ $logItem['content'] }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Lightbox Zoom Modal for High-Res Inspection -->
            <div x-show="activeLogDetail !== null" 
                 x-transition 
                 class="fixed inset-0 bg-slate-950/70 backdrop-blur-md z-50 flex items-center justify-center p-4"
                 @click.self="activeLogDetail = null">
                <div class="bg-white rounded-3xl max-w-2xl w-full overflow-hidden shadow-2xl space-y-4 p-5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-black flex items-center justify-center text-xs">
                                <span x-text="activeLogDetail ? activeLogDetail.user_name.substring(0, 1) : 'V'"></span>
                            </div>
                            <div>
                                <strong class="text-sm font-bold text-slate-900 block" x-text="activeLogDetail ? activeLogDetail.user_name : ''"></strong>
                                <span class="text-[10px] text-emerald-700 font-bold uppercase" x-text="activeLogDetail ? activeLogDetail.user_role : 'Relawan'"></span>
                            </div>
                        </div>
                        <button @click="activeLogDetail = null" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center">&times;</button>
                    </div>

                    <div class="aspect-video w-full rounded-2xl overflow-hidden bg-slate-100 shadow-md">
                        <img :src="activeLogDetail ? activeLogDetail.photo_path : ''" class="w-full h-full object-cover">
                    </div>

                    <div class="space-y-2">
                        <h4 class="text-base font-black text-slate-900" x-text="activeLogDetail ? activeLogDetail.title : ''"></h4>
                        <div class="p-3.5 bg-slate-50 rounded-2xl text-xs text-slate-700 leading-relaxed border-l-4 border-emerald-500">
                            <strong class="block text-[10px] uppercase tracking-wider text-slate-400 mb-1">Keterangan Catatan Lapangan:</strong>
                            <p x-text="activeLogDetail ? activeLogDetail.content : ''"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SLIDE 5: MILESTONE & TAHAPAN PROGRAM       -->
        <!-- ========================================== -->
        <div x-show="currentSlide === 5" 
             x-transition:enter="transition-all ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-3 scale-99"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="w-full space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
                <div>
                    <span class="bg-indigo-100 text-indigo-800 text-[11px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                        Tahapan Aksi Terstruktur
                    </span>
                    <h3 class="text-3xl font-black text-slate-900 mt-1">
                        Milestone Perjalanan Intervensi
                    </h3>
                </div>
                <span class="text-xs font-bold text-slate-500">
                    5 Babak Aksi Terencana
                </span>
            </div>

            <!-- Milestone Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                @forelse($program->storyMilestones as $mIdx => $ms)
                    <div @click.stop="activeMilestoneIndex = {{ $mIdx }}"
                         :class="activeMilestoneIndex === {{ $mIdx }} ? 'ring-2 ring-emerald-500 shadow-xl scale-105 bg-white' : 'seamless-card hover:shadow-md'"
                         class="p-5 flex flex-col justify-between space-y-3 transition cursor-pointer">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="bg-indigo-100 text-indigo-800 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full">
                                    Babak {{ sprintf('%02d', $mIdx + 1) }}
                                </span>
                                <span class="text-[10px] font-mono text-emerald-700 uppercase font-bold">{{ $ms->phase }}</span>
                            </div>
                            <h4 class="text-xs font-black text-slate-900 leading-snug line-clamp-2">{{ $ms->title }}</h4>
                            <p class="text-[11px] text-slate-600 line-clamp-3 leading-relaxed">{{ $ms->story_narrative }}</p>
                        </div>

                        <div class="pt-2 border-t border-slate-100 text-[10px] space-y-1">
                            <span class="text-emerald-700 font-bold block">{{ $ms->key_achievement }}</span>
                            <span class="text-slate-400 text-[9px] block">{{ $ms->personnel_involved }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-5 text-center py-12 text-slate-400 text-xs">Belum ada data milestone.</div>
                @endforelse
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SLIDE 6: KETERCAPAIAN KPI (DENGAN GRAFIS)  -->
        <!-- ========================================== -->
        <div x-show="currentSlide === 6" 
             x-transition:enter="transition-all ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-3 scale-99"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="w-full space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
                <div>
                    <span class="bg-amber-100 text-amber-800 text-[11px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                        Grafis Ketercapaian
                    </span>
                    <h3 class="text-3xl font-black text-slate-900 mt-1">
                        Evaluasi Target vs Realisasi
                    </h3>
                </div>
                <span class="text-xs font-bold text-slate-500">
                    {{ $program->kpiIndicators->count() }} Indikator Tervalidasi
                </span>
            </div>

            <!-- KPI Grid with Visual Charts & Graphics -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($program->kpiIndicators as $kpi)
                    @php
                        $pct = $kpi->target > 0 ? min(100, round(($kpi->actual / $kpi->target) * 100)) : 100;
                        $strokeDash = 2 * 3.14159 * 28;
                        $dashOffset = $strokeDash * (1 - ($pct / 100));
                    @endphp
                    <div class="seamless-card p-5 space-y-3 hover:scale-102 transition">
                        <div class="flex items-center justify-between text-[10px]">
                            <span class="bg-slate-100 text-slate-700 font-mono font-bold px-2 py-0.5 rounded-md">
                                {{ $kpi->code ?? 'KPI' }}
                            </span>
                            <span class="text-emerald-700 font-bold uppercase bg-emerald-50 px-2 py-0.5 rounded-full">
                                Tercapai {{ $pct }}%
                            </span>
                        </div>

                        <div class="flex items-center gap-4">
                            <!-- Animated Donut Gauge Chart -->
                            <div class="relative w-16 h-16 shrink-0 flex items-center justify-center">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 64 64">
                                    <circle cx="32" cy="32" r="28" stroke="#f1f5f9" stroke-width="5" fill="none"/>
                                    <circle cx="32" cy="32" r="28" stroke="#10b981" stroke-width="5" fill="none"
                                            stroke-dasharray="{{ $strokeDash }}"
                                            stroke-dashoffset="{{ $dashOffset }}"
                                            stroke-linecap="round"
                                            class="transition-all duration-1000 ease-out"/>
                                </svg>
                                <span class="absolute font-black text-xs text-slate-900">{{ $pct }}%</span>
                            </div>

                            <div class="min-w-0">
                                <h4 class="text-sm font-black text-slate-900 leading-snug truncate" title="{{ $kpi->name }}">{{ $kpi->name }}</h4>
                                <div class="mt-1">
                                    <strong class="text-lg font-black text-slate-900">{{ number_format($kpi->actual) }}</strong>
                                    <span class="text-xs text-slate-400">/ {{ number_format($kpi->target) }} {{ $kpi->unit }}</span>
                                </div>
                            </div>
                        </div>

                        @if($kpi->graphic_image)
                            <div class="rounded-xl overflow-hidden aspect-video bg-slate-100 shadow-inner">
                                <img src="{{ $kpi->graphic_image }}" alt="{{ $kpi->name }}" class="w-full h-full object-cover">
                            </div>
                        @endif

                        <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">{{ $kpi->definition }}</p>
                        
                        <div class="pt-2 border-t border-slate-100 text-[10px] text-slate-400 flex justify-between">
                            <span>Verifikator:</span>
                            <strong class="text-slate-700">{{ $kpi->verified_by ?? 'DLH Jabar' }}</strong>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 text-slate-400 text-xs">Belum ada data KPI.</div>
                @endforelse
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SLIDE 7: BEFORE & AFTER (BANYAK REKORD)    -->
        <!-- ========================================== -->
        <div x-show="currentSlide === 7" 
             x-transition:enter="transition-all ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-3 scale-99"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="w-full space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3">
                <div>
                    <span class="bg-emerald-100 text-emerald-800 text-[11px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                        Komparasi Visual Transformasi
                    </span>
                    <h3 class="text-3xl font-black text-slate-900 mt-1">
                        Before & After Transformasi Nyata
                    </h3>
                </div>
                <span class="text-xs font-bold text-slate-500">
                    {{ $program->beforeAfterRecords->count() }} Titik Perubahan Terekam
                </span>
            </div>

            <!-- Record Selector Pills -->
            <div class="flex items-center gap-2 overflow-x-auto py-1 custom-scroll">
                @foreach($program->beforeAfterRecords as $baIdx => $bar)
                    <button 
                        @click.stop="activeBeforeAfterIndex = {{ $baIdx }}" 
                        :class="activeBeforeAfterIndex === {{ $baIdx }} ? 'bg-emerald-600 text-white font-black shadow-md' : 'bg-white hover:bg-slate-100 text-slate-700 font-bold border border-slate-200'"
                        class="px-4 py-2 rounded-full text-xs shrink-0 transition">
                        {{ $baIdx + 1 }}. {{ $bar->title }}
                    </button>
                @endforeach
            </div>

            <!-- Active Split Comparison Slider (Cinematic) -->
            @foreach($program->beforeAfterRecords as $baIdx => $ba)
                <div x-show="activeBeforeAfterIndex === {{ $baIdx }}" x-transition class="space-y-4">
                    <div class="relative w-full h-[360px] sm:h-[420px] rounded-3xl overflow-hidden select-none shadow-2xl">
                        <!-- After Image -->
                        <img src="{{ $ba->after_image }}" alt="After" class="absolute inset-0 w-full h-full object-cover">
                        <span class="absolute top-4 right-4 bg-emerald-600 text-white text-[11px] font-black px-3.5 py-1.5 rounded-full uppercase tracking-wider z-10 shadow-lg">
                            SESUDAH (HASIL AKSI)
                        </span>

                        <!-- Before Image (Clipped) -->
                        <div class="absolute inset-0 overflow-hidden" :style="'width: ' + beforeAfterPos + '%'">
                            <img src="{{ $ba->before_image }}" alt="Before" class="absolute inset-0 w-[1200px] h-full object-cover max-w-none">
                            <span class="absolute top-4 left-4 bg-slate-900 text-white text-[11px] font-black px-3.5 py-1.5 rounded-full uppercase tracking-wider z-10 shadow-lg">
                                SEBELUM (KONDISI AWAL)
                            </span>
                        </div>

                        <!-- Divider Handle -->
                        <div class="absolute top-0 bottom-0 w-1 bg-white shadow-2xl cursor-ew-resize flex items-center justify-center z-20" :style="'left: ' + beforeAfterPos + '%'">
                            <div class="w-9 h-9 rounded-full bg-white text-slate-900 flex items-center justify-center font-black shadow-2xl text-xs -translate-x-1/2 border border-slate-200">
                                &harr;
                            </div>
                        </div>

                        <input type="range" min="0" max="100" x-model="beforeAfterPos" class="absolute inset-0 w-full h-full opacity-0 cursor-ew-resize z-30">
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-xs text-slate-600 seamless-card p-4">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Kondisi Awal:</span>
                            <p>{{ $ba->before_description }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-emerald-700 uppercase block mb-1">Hasil Transformasi:</span>
                            <p>{{ $ba->after_description }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ========================================== -->
        <!-- SLIDE 8: LUARAN PROGRAM (OUTPUTS GALLERY)  -->
        <!-- ========================================== -->
        <div x-show="currentSlide === 8" 
             x-transition:enter="transition-all ease-out duration-400 transform"
             x-transition:enter-start="opacity-0 translate-y-3 scale-99"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             class="w-full space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-4">
                <div>
                    <span class="bg-lime-100 text-lime-800 text-[11px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                        Hasil Nyata & Terbitan
                    </span>
                    <h3 class="text-3xl font-black text-slate-900 mt-1">
                        Katalog Produk, Buku & HAKI
                    </h3>
                </div>
                <span class="text-xs font-bold text-slate-500">
                    {{ $program->projectOutputs->count() }} Luaran Terverifikasi
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @forelse($program->projectOutputs as $out)
                    <div class="seamless-card p-5 space-y-3 flex flex-col justify-between hover:scale-105 transition">
                        <div class="space-y-2">
                            <span class="bg-lime-100 text-lime-800 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-full">
                                {{ $out->category }}
                            </span>
                            <h4 class="text-xs font-black text-slate-900 leading-snug">{{ $out->title }}</h4>
                            <p class="text-[11px] text-slate-500 line-clamp-3 leading-relaxed">{{ $out->description }}</p>
                        </div>
                        <div class="pt-2 border-t border-slate-100 text-[10px] text-slate-400 flex items-center justify-between gap-1">
                            <span class="font-mono text-emerald-700 font-bold truncate max-w-[120px]">{{ $out->ip_number ?? 'VERIFIED' }}</span>
                            @if($out->evidence_url === '#laporan-lengkap')
                                <a href="{{ route('public.projects.evidence-report', $program->id) }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-2 py-0.5 rounded-lg text-[9px] transition shrink-0">
                                    Buka Dokumen &rarr;
                                </a>
                            @elseif(str_starts_with($out->evidence_url ?? '', 'http'))
                                <a href="{{ $out->evidence_url }}" target="_blank" class="bg-slate-900 hover:bg-emerald-600 text-white font-bold px-2 py-0.5 rounded text-[9px] transition shrink-0">
                                    Kunjungi &rarr;
                                </a>
                            @else
                                <span class="font-bold text-slate-600 truncate max-w-[110px]">{{ $out->authors_or_team }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-12 text-slate-400 text-xs">Belum ada data luaran terdaftar.</div>
                @endforelse
            </div>
        </div>

    </main>

    <!-- ========================================== -->
    <!-- BOTTOM CHAPTER STEPPER / NAVIGATION PILLS  -->
    <!-- ========================================== -->
    <footer class="w-full px-4 sm:px-8 py-4 bg-white/90 backdrop-blur-md border-t border-slate-200/70 z-40">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Prev Button -->
            <button 
                @click.stop="prevSlide()" 
                class="px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700 transition flex items-center gap-1.5 active:scale-95 shadow-sm">
                <span>&larr;</span> Sebelumnya
            </button>

            <!-- Chapter Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto custom-scroll py-1 max-w-full">
                @php
                    $slideTitles = [
                        '1. Profil',
                        '2. Lokasi & Foto',
                        '3. Tim Relawan',
                        '4. Jejak Karbon',
                        '5. Cerita Harian',
                        '6. Milestone',
                        '7. Ketercapaian',
                        '8. Before & After',
                        '9. Luaran Karya',
                    ];
                @endphp
                @foreach($slideTitles as $sIdx => $sTitle)
                    <button 
                        @click.stop="goToSlide({{ $sIdx }})" 
                        :class="currentSlide === {{ $sIdx }} ? 'bg-emerald-600 text-white font-black shadow-md shadow-emerald-600/30' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 border-0'"
                        class="px-3.5 py-1.5 rounded-full text-xs font-semibold shrink-0 transition">
                        {{ $sTitle }}
                    </button>
                @endforeach
            </div>

            <!-- Next Button -->
            <button 
                @click.stop="nextSlide()" 
                class="px-4 py-2 rounded-full bg-emerald-600 hover:bg-emerald-500 text-xs font-bold text-white transition flex items-center gap-1.5 active:scale-95 shadow-md shadow-emerald-600/20">
                Selanjutnya <span>&rarr;</span>
            </button>
        </div>
    </footer>

    <!-- Alpine State Machine & Autoplay / Idle Controller -->
    <script>
        const dailyGroupTitles = @json($dailyGroups->pluck('primary_title'));
        const dailyGroupDates = @json($dailyGroups->pluck('date_str'));

        function impactDeck(config) {
            return {
                totalSlides: config.totalSlides,
                currentSlide: config.currentSlide,
                slideInterval: config.slideInterval,
                idleTimeout: config.idleTimeout,
                activeDayIndex: config.activeDayIndex,
                totalDays: config.totalDays,
                activeVolunteerFilter: config.activeVolunteerFilter,
                activeLogDetail: config.activeLogDetail,
                activeMilestoneIndex: config.activeMilestoneIndex,
                activeBeforeAfterIndex: config.activeBeforeAfterIndex,
                activeLocationPhotoIndex: config.activeLocationPhotoIndex,
                beforeAfterPos: config.beforeAfterPos,
                isPaused: false,
                isUserInteracting: false,
                progressPercent: 0,
                progressTimer: null,
                idleTimer: null,
                mapInitialized: false,

                init() {
                    this.startAutoplay();
                },

                startAutoplay() {
                    const stepMs = 50;
                    const increment = (stepMs / this.slideInterval) * 100;
                    
                    if (this.progressTimer) clearInterval(this.progressTimer);
                    
                    this.progressTimer = setInterval(() => {
                        if (!this.isPaused && !this.isUserInteracting) {
                            this.progressPercent += increment;
                            if (this.progressPercent >= 100) {
                                this.progressPercent = 0;
                                this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                                this.onSlideChanged();
                            }
                        }
                    }, stepMs);
                },

                onUserClick(event) {
                    this.isUserInteracting = true;
                    this.isPaused = true;
                    this.resetIdleTimer();
                },

                onUserMovement() {
                    if (this.isUserInteracting) {
                        this.resetIdleTimer();
                    }
                },

                resetIdleTimer() {
                    if (this.idleTimer) clearTimeout(this.idleTimer);
                    
                    // Resume slideshow when there is NO movement / idle for 10 seconds
                    this.idleTimer = setTimeout(() => {
                        this.isUserInteracting = false;
                        this.isPaused = false;
                        this.progressPercent = 0;
                    }, this.idleTimeout);
                },

                goToSlide(index) {
                    this.currentSlide = index;
                    this.progressPercent = 0;
                    this.isUserInteracting = true;
                    this.isPaused = true;
                    this.resetIdleTimer();
                    this.onSlideChanged();
                },

                nextSlide() {
                    this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                    this.progressPercent = 0;
                    this.isUserInteracting = true;
                    this.isPaused = true;
                    this.resetIdleTimer();
                    this.onSlideChanged();
                },

                prevSlide() {
                    this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
                    this.progressPercent = 0;
                    this.isUserInteracting = true;
                    this.isPaused = true;
                    this.resetIdleTimer();
                    this.onSlideChanged();
                },

                togglePlayPause() {
                    this.isPaused = !this.isPaused;
                    if (!this.isPaused) {
                        this.isUserInteracting = false;
                    }
                },

                onSlideChanged() {
                    if (this.currentSlide === 1 && !this.mapInitialized) {
                        setTimeout(() => {
                            this.initDeckMap();
                            this.mapInitialized = true;
                        }, 200);
                    }
                },

                toggleFullscreen() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen();
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                        }
                    }
                },

                initDeckMap() {
                    const mapEl = document.getElementById('deck-map');
                    if (!mapEl) return;
                    
                    const map = L.map('deck-map').setView([-6.089000, 106.720000], 11);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(map);

                    fetch('/map-data?program_id={{ $program->id }}')
                        .then(res => res.json())
                        .then(geoJsonData => {
                            if (geoJsonData.features && geoJsonData.features.length > 0) {
                                const layer = L.geoJSON(geoJsonData, {
                                    style: {
                                        fillColor: '#10b981',
                                        weight: 2,
                                        color: '#059669',
                                        fillOpacity: 0.3
                                    }
                                }).addTo(map);
                                map.fitBounds(layer.getBounds(), { padding: [20, 20] });
                            }
                        })
                        .catch(() => {});
                }
            }
        }
    </script>
</body>
</html>
