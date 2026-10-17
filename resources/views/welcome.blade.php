<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YOTA PROJECT OS - Interactive Impact Experience & Social OS</title>
    
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
            background-color: #fafaf9; 
        }
        .gradient-glow {
            background: radial-gradient(circle at 50% 50%, rgba(76, 175, 80, 0.12) 0%, transparent 60%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
    </style>
</head>
<body class="antialiased text-slate-800 relative min-h-screen overflow-x-hidden" x-data="{ activeCategory: 'all' }">
    
    <!-- Gradient background accent -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-[650px] gradient-glow pointer-events-none -z-10"></div>

    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200/80 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="/images/logo.webp" alt="Yota Logo" class="w-10 h-10 object-contain bg-white rounded-xl p-1 shadow-sm border border-slate-100">
                <div>
                    <span class="block text-base font-extrabold text-slate-900 tracking-wide leading-none">YOTA PROJECT OS</span>
                    <span class="text-[9px] font-bold text-[#2E7D32] uppercase tracking-wider block mt-0.5">Social Development & Interactive Impact Platform</span>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="#projects-catalog" class="hidden md:inline-flex text-xs font-bold text-slate-600 hover:text-[#2E7D32] transition items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Katalog Proyek
                </a>
                <a href="#map-section" class="hidden md:inline-flex text-xs font-bold text-slate-600 hover:text-[#2E7D32] transition">
                    Peta Zona
                </a>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-[#2E7D32] hover:bg-[#1B5E20] text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-md shadow-green-900/10 transition active:scale-95">
                            Workspace Saya
                        </a>
                        <a href="{{ route('login') }}" class="bg-[#2E7D32] hover:bg-[#1B5E20] text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-md shadow-green-900/10 transition active:scale-95">
                            Masuk Workspace
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-6 pt-12 pb-10 grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
        <div class="space-y-6 lg:col-span-7">
            <div class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-800 text-[10px] font-extrabold px-4 py-1.5 rounded-full uppercase tracking-widest border border-emerald-200/80">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                EVIDENCE-BASED STORYTELLING & ESG OS
            </div>
            
            <h1 class="text-4xl lg:text-5xl font-black text-slate-900 leading-[1.15] tracking-tight">
                Every Project Has a Story.<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600">
                    Explore the Journey.
                </span>
            </h1>
            
            <p class="text-slate-600 text-sm sm:text-base leading-relaxed max-w-xl">
                Jelajahi perjalanan aksi sosial, restorasi lingkungan, dan pemberdayaan masyarakat layaknya menonton dokumenter interaktif. Lengkap dengan bukti logbook terverifikasi, alur rantai dampak <em>(Input &rarr; Impact)</em>, serta kontrol KPI presisi.
            </p>
            
            <div class="flex flex-wrap gap-3 pt-2">
                <a href="#projects-catalog" class="bg-[#2E7D32] hover:bg-[#1B5E20] text-white font-extrabold px-6 py-3.5 rounded-xl shadow-lg shadow-green-950/15 transition active:scale-95 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pilih & Jelajahi Proyek (Story Mode)
                </a>
                <a href="#map-section" class="bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold px-6 py-3.5 rounded-xl shadow-sm transition active:scale-95 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Peta Geofence Wilayah
                </a>
            </div>
        </div>

        <!-- Hero Card Macro Stats -->
        <div class="lg:col-span-5 bg-white border border-slate-200/80 rounded-3xl p-7 shadow-xl shadow-slate-200/50 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full blur-2xl -z-10"></div>
            
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-widest bg-emerald-50 px-2.5 py-1 rounded-md">
                        Akumulasi Ekosistem Yota
                    </span>
                    <span class="text-[10px] font-bold text-slate-400">Update Terverifikasi</span>
                </div>
                <h3 class="text-xl font-black text-slate-900 leading-snug pt-1">
                    "Community Development & Environmental Impact Operating System"
                </h3>
                <p class="text-xs text-slate-500">Transparansi data terdesentralisasi dari 4 program kerja lapangan aktif.</p>
            </div>
            
            <!-- Statistics Grid -->
            <div class="grid grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-100 mt-6 text-center">
                <div class="p-2">
                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Carbon Offset</span>
                    <strong class="block text-base font-black text-emerald-700 mt-1">{{ number_format($totalCarbonOffset) }} Kg</strong>
                    <span class="text-[8px] text-slate-400 font-medium">CO₂e Terhindari</span>
                </div>
                <div class="p-2 border-x border-slate-200/60">
                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Penerima</span>
                    <strong class="block text-base font-black text-slate-900 mt-1">{{ number_format($totalBeneficiaries) }} Jiwa</strong>
                    <span class="text-[8px] text-slate-400 font-medium">Warga Terdampak</span>
                </div>
                <div class="p-2">
                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider">Relawan</span>
                    <strong class="block text-base font-black text-slate-900 mt-1">{{ number_format($totalVolunteers) }} Orang</strong>
                    <span class="text-[8px] text-slate-400 font-medium">Personel Lapangan</span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span class="flex items-center gap-1.5 font-medium">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    Audited by Yota Carbon Engine
                </span>
                <a href="#projects-catalog" class="font-bold text-emerald-700 hover:underline">Jelajahi &rarr;</a>
            </div>
        </div>
    </section>

    <!-- PROJECT SHOWCASE / CATALOG SECTION (THE CORE REQUEST) -->
    <section id="projects-catalog" class="max-w-7xl mx-auto px-6 py-12">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
            <div>
                <div class="inline-flex items-center gap-2 bg-lime-100 text-lime-900 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider mb-2">
                    KATALOG PROYEK & INTERACTIVE DASHBOARD
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Pilih Program & Buka Dashboard Publik Interaktif
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                    Setiap program memiliki tampilan dashboard public dan *Story Experience* masing-masing. Klik program untuk menjelajahi timeline harian, bukti logbook, alur dampak, hingga produk luaran.
                </p>
            </div>

            <!-- Filter Categories -->
            <div class="flex flex-wrap gap-2 shrink-0">
                <button 
                    @click="activeCategory = 'all'" 
                    :class="activeCategory === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">
                    Semua ({{ $programs->count() }})
                </button>
                <button 
                    @click="activeCategory = 'lingkungan'" 
                    :class="activeCategory === 'lingkungan' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">
                    Restorasi & Pesisir
                </button>
                <button 
                    @click="activeCategory = 'pangan'" 
                    :class="activeCategory === 'pangan' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">
                    Ketahanan Pangan
                </button>
                <button 
                    @click="activeCategory = 'sanitasi'" 
                    :class="activeCategory === 'sanitasi' ? 'bg-emerald-700 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">
                    Air Bersih
                </button>
            </div>
        </div>

        <!-- Project Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
            @foreach($programs as $prog)
                @php
                    $cat = strtolower($prog->kategori_program ?? '');
                    $catFilter = 'other';
                    if (str_contains($cat, 'lingkungan') || str_contains($cat, 'pesisir') || str_contains($cat, 'karbon')) {
                        $catFilter = 'lingkungan';
                    } elseif (str_contains($cat, 'pangan') || str_contains($cat, 'waste') || str_contains($cat, 'sampah')) {
                        $catFilter = 'pangan';
                    } elseif (str_contains($cat, 'air') || str_contains($cat, 'sanitasi') || str_contains($cat, 'sehat')) {
                        $catFilter = 'sanitasi';
                    }
                @endphp
                <div 
                    x-show="activeCategory === 'all' || activeCategory === '{{ $catFilter }}'"
                    x-transition
                    class="bg-white border border-slate-200/90 rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                    
                    <div>
                        <!-- Cover Image with Badges (Clickable to Detail) -->
                        <a href="{{ route('public.projects.show', $prog->id) }}" class="block relative h-56 w-full overflow-hidden bg-slate-100 group/img">
                            <img 
                                src="{{ $prog->cover_image ?? '/images/projects/mangrove.jpg' }}" 
                                alt="{{ $prog->name }}" 
                                class="w-full h-full object-cover group-hover/img:scale-105 transition-transform duration-500">
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-transparent"></div>

                            <!-- Top Badges -->
                            <div class="absolute top-4 left-4 right-4 flex items-center justify-between pointer-events-none">
                                <span class="bg-white/95 backdrop-blur-md text-slate-900 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
                                    {{ $prog->kategori_program ?? 'Aksi Sosial' }}
                                </span>
                                
                                <span class="bg-emerald-500/95 backdrop-blur-md text-white text-[10px] font-bold px-3 py-1 rounded-full flex items-center gap-1.5 shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                                    Aksi Berjalan
                                </span>
                            </div>

                            <!-- Bottom Meta over Image -->
                            <div class="absolute bottom-4 left-4 right-4 text-white">
                                <span class="text-[10px] font-bold text-emerald-300 tracking-wider uppercase block">
                                    {{ $prog->mitra ?? 'DLH & CSR Partnership' }}
                                </span>
                                <h3 class="text-xl font-black text-white leading-snug line-clamp-2 mt-0.5 group-hover/img:text-emerald-300 transition">
                                    {{ $prog->name }}
                                </h3>
                            </div>
                        </a>

                        <!-- Card Body -->
                        <div class="p-6 space-y-4">
                            <p class="text-slate-600 text-xs sm:text-sm line-clamp-3 leading-relaxed">
                                {{ $prog->description }}
                            </p>

                            <!-- Micro-Metrics Grid (Evidence) -->
                            <div class="grid grid-cols-3 gap-2 bg-slate-50 p-3 rounded-2xl border border-slate-100 text-center">
                                <div>
                                    <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-wider">Carbon Offset</span>
                                    <strong class="block text-xs font-black text-emerald-700 mt-0.5">
                                        {{ number_format($prog->stats['carbon_offset'] ?? 0) }} Kg
                                    </strong>
                                </div>
                                <div class="border-x border-slate-200">
                                    <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-wider">Penerima</span>
                                    <strong class="block text-xs font-black text-slate-800 mt-0.5">
                                        {{ number_format($prog->stats['beneficiaries_count'] ?? 0) }} Jiwa
                                    </strong>
                                </div>
                                <div>
                                    <span class="block text-[8px] font-bold text-slate-400 uppercase tracking-wider">Relawan</span>
                                    <strong class="block text-xs font-black text-slate-800 mt-0.5">
                                        {{ number_format($prog->stats['volunteers_count'] ?? 0) }} Org
                                    </strong>
                                </div>
                            </div>

                            <!-- Highlights Story & KPIs indicators -->
                            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
                                <span class="flex items-center gap-1 font-semibold text-slate-700">
                                    <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    {{ $prog->storyMilestones->count() > 0 ? $prog->storyMilestones->count() . ' Chapter Cerita' : 'Storyline Tersedia' }}
                                </span>
                                <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-bold text-[10px]">
                                    {{ $prog->kpiIndicators->count() > 0 ? $prog->kpiIndicators->count() . ' Indikator KPI' : 'SDG Integrated' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Action: Direct Detail Link ONLY (No Gabung button) -->
                    <div class="p-6 pt-0 border-t border-slate-100 mt-2">
                        <a 
                            href="{{ route('public.projects.show', $prog->id) }}" 
                            class="w-full bg-[#2E7D32] hover:bg-[#1B5E20] text-white font-extrabold text-xs py-3.5 px-5 rounded-2xl shadow-md shadow-green-900/15 flex items-center justify-center gap-2 group transition active:scale-95">
                            <span>Masuk ke Detail & Dashboard Publik</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Map Section -->
    <section id="map-section" class="max-w-7xl mx-auto px-6 py-12">
        <div class="text-center mb-8 max-w-2xl mx-auto">
            <span class="text-[10px] font-extrabold text-emerald-700 uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                PETA GEOFENCE TERINTEGRASI
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">Peta Sebaran Dampak Sosial</h2>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Visualisasi sebaran wilayah binaan dan area kegiatan lapangan yang tervalidasi menggunakan Geofencing</p>
        </div>
        
        <div class="bg-white border border-slate-200/80 rounded-3xl p-3 shadow-xl shadow-slate-100/50">
            <div id="map" class="w-full h-[450px] rounded-2xl z-10 border border-slate-100"></div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-400 py-12 mt-16 px-6 border-t border-slate-900">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-3">
                <img src="/images/logo.webp" alt="Yota Logo" class="w-10 h-10 object-contain bg-white rounded-xl p-1 shadow-sm">
                <div>
                    <h4 class="text-white font-bold text-xs tracking-wider">YOTA PROJECT OS</h4>
                    <p class="text-[10px] text-slate-500 mt-0.5">Community-Led Sustainable Action Platform & Interactive Impact Experience.</p>
                </div>
            </div>
            <p class="text-[10px] text-slate-600">&copy; 2026 Yota Project OS. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Leaflet Map focused on West Java (where all projects are situated)
            const map = L.map('map').setView([-6.800000, 107.100000], 8);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            fetch('/map-data')
                .then(res => res.json())
                .then(geoJsonData => {
                    if (geoJsonData.features && geoJsonData.features.length > 0) {
                        const geoJsonLayer = L.geoJSON(geoJsonData, {
                            style: function (feature) {
                                return {
                                    fillColor: '#2E7D32',
                                    weight: 2,
                                    opacity: 1,
                                    color: '#1B5E20',
                                    fillOpacity: 0.2
                                };
                            },
                            onEachFeature: function (feature, layer) {
                                const progId = feature.properties.program_id;
                                const projectUrl = progId ? `/projects/${progId}` : '#';
                                
                                layer.bindPopup(`
                                    <div class="p-3 text-xs max-w-xs font-sans">
                                        <span class="bg-emerald-100 text-emerald-900 text-[9px] font-extrabold px-2 py-0.5 rounded-full uppercase tracking-wider block w-fit mb-1.5">
                                            ${feature.properties.status || 'Aktif'}
                                        </span>
                                        <h4 class="font-extrabold text-slate-900 text-sm">${feature.properties.name}</h4>
                                        <p class="text-emerald-700 font-bold text-[10px] mt-0.5">${feature.properties.program_name}</p>
                                        <p class="text-slate-500 text-[11px] mt-1.5 leading-relaxed">${feature.properties.description}</p>
                                        
                                        <div class="mt-3 pt-2 border-t border-slate-100">
                                            <a href="${projectUrl}" class="inline-flex items-center gap-1.5 bg-[#2E7D32] hover:bg-[#1B5E20] text-white text-[10px] font-extrabold px-3 py-1.5 rounded-lg shadow-sm transition">
                                                <span>Buka Dashboard & Story</span> &rarr;
                                            </a>
                                        </div>
                                    </div>
                                `);
                            }
                        }).addTo(map);

                        map.fitBounds(geoJsonLayer.getBounds(), { padding: [30, 30] });
                    }
                });
        });
    </script>
</body>
</html>
