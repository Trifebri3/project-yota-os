<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transparansi Aset Program YOTA OS</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen pb-12">

    <!-- Container -->
    <div class="max-w-md mx-auto min-h-screen bg-slate-50 shadow-2xl relative border-x border-slate-100 flex flex-col">
        <!-- Header -->
        <header class="bg-slate-900 text-white px-6 py-5 flex items-center gap-3 rounded-b-[2rem] shadow-md relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-[#4CAF50]/10 to-transparent pointer-events-none"></div>
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-lg shrink-0 backdrop-blur-sm border border-white/20">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <div class="z-10">
                <h1 class="text-sm font-extrabold tracking-wide uppercase text-white">Transparansi Aset Program</h1>
                <span class="text-[9px] font-extrabold text-[#D9F99D] uppercase tracking-wider block">YOTA OS IMPACT TRAIL</span>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-5 py-6 space-y-6">
            
            <!-- Asset Main Info card -->
            <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4 text-xs relative overflow-hidden">
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <span class="bg-[#4CAF50]/10 text-[#2E7D32] border border-[#4CAF50]/20 px-2 py-0.5 rounded text-[8px] font-extrabold uppercase">
                            {{ $asset->category }}
                        </span>
                        <h2 class="text-base font-extrabold text-slate-800 mt-1.5 leading-snug">{{ $asset->name }}</h2>
                        <span class="block font-mono text-[10px] text-slate-400 mt-1">ID Tag: {{ $asset->asset_id }}</span>
                    </div>

                    @php
                        $badgeColor = 'bg-green-50 text-green-800 border-green-200';
                        if (in_array($asset->current_condition, ['Rusak Ringan'])) {
                            $badgeColor = 'bg-amber-50 text-amber-800 border-amber-200';
                        } elseif (in_array($asset->current_condition, ['Rusak Berat'])) {
                            $badgeColor = 'bg-red-50 text-red-800 border-red-200';
                        }
                    @endphp
                    <span class="px-3 py-1 rounded-full text-[9px] font-extrabold uppercase border {{ $badgeColor }} shrink-0 shadow-sm">
                        {{ $asset->current_condition }}
                    </span>
                </div>

                <!-- Asset photo -->
                @if($asset->photo_path)
                    <div class="rounded-2xl overflow-hidden aspect-video bg-slate-50 border shadow-inner">
                        <img src="{{ $asset->photo_path }}" alt="{{ $asset->name }}" class="w-full h-full object-cover">
                    </div>
                @endif

                <div class="space-y-2.5 pt-2 border-t border-slate-100">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-slate-400">Program Pemilik:</span>
                        <strong class="font-bold text-slate-750 text-right">{{ $asset->program?->name ?? 'Yota General Program' }}</strong>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-slate-400">Organisasi Pelaksana:</span>
                        <strong class="font-bold text-slate-750 text-right">{{ $asset->program?->organisasi_pelaksana ?? 'Organisasi Yota' }}</strong>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-slate-400">Lokasi Penempatan:</span>
                        <strong class="font-bold text-slate-750 text-right">{{ $asset->location }}</strong>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-slate-400">Tanggal Pembelian:</span>
                        <strong class="font-bold text-slate-750">{{ $asset->purchase_date?->format('d M Y') ?? '-' }}</strong>
                    </div>
                    <div class="flex justify-between pb-1">
                        <span class="text-slate-400">Jumlah Unit Terdata:</span>
                        <strong class="font-bold text-slate-750">{{ $asset->quantity }} {{ $asset->unit }}</strong>
                    </div>
                </div>

                <!-- Note explaining hidden metrics -->
                <div class="bg-blue-50 border border-blue-150 p-3 rounded-2xl text-[9px] text-blue-900 leading-relaxed flex gap-2">
                    <svg class="w-4 h-4 text-blue-650 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <strong>Informasi Keuangan & Supplier Internal Sengaja Disembunyikan</strong>. Untuk mematuhi regulasi privasi & keamanan data aset, kolom harga pembelian, total nilai investasi, supplier, dan data administratif internal hanya dapat diakses oleh Admin Program yang berwenang melalui dasbor terenkripsi.
                    </div>
                </div>
            </div>

            <!-- Transparency Timeline History -->
            <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4 text-xs">
                <div>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Riwayat Pemeliharaan & Update</h3>
                    <p class="text-[9px] text-slate-400">Jejak rekaman transparansi status dan audit barang</p>
                </div>

                <div class="relative pl-5 border-l-2 border-slate-100 space-y-5 py-2">
                    @foreach($asset->logs as $log)
                        <div class="relative space-y-1">
                            <!-- Bullet marker -->
                            <div class="absolute -left-[26px] top-1 w-2.5 h-2.5 rounded-full bg-slate-350 border border-white"></div>
                            
                            <div class="flex justify-between items-center text-[9px] text-slate-400">
                                <span>{{ $log->created_at->format('d M Y') }}</span>
                                <span class="bg-slate-100 px-1.5 py-0.5 rounded font-extrabold uppercase text-[7px]">{{ $log->action === 'create' ? 'Inisialisasi' : 'Pembaruan' }}</span>
                            </div>
                            <div class="text-[10px]">
                                <span class="text-slate-500">Kondisi dan penempatan aset dilaporkan di:</span>
                                <strong class="text-slate-700 block mt-0.5">{{ $log->location ?? $asset->location }}</strong>
                            </div>

                            @if(!empty($log->new_values) && !empty($log->old_values))
                                <div class="bg-slate-50 p-2 border rounded-xl text-[9px] font-mono mt-1">
                                    @if(isset($log->new_values['current_condition']) && $log->old_values['current_condition'] !== $log->new_values['current_condition'])
                                        <div>
                                            Kondisi: <span class="text-red-500 line-through">{{ $log->old_values['current_condition'] }}</span> &rarr; <span class="text-green-600 font-bold">{{ $log->new_values['current_condition'] }}</span>
                                        </div>
                                    @endif
                                    @if(isset($log->new_values['location']) && $log->old_values['location'] !== $log->new_values['location'])
                                        <div>
                                            Lokasi: <span class="text-red-500 line-through">{{ $log->old_values['location'] }}</span> &rarr; <span class="text-green-600 font-bold">{{ $log->new_values['location'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </main>

        <!-- Footer -->
        <footer class="text-center py-6 text-[10px] text-slate-400 border-t border-slate-100 bg-white">
            <p>&copy; {{ date('Y') }} YOTA OS System. Hak Cipta Dilindungi.</p>
            <p class="mt-0.5">Platform Transparansi Aset & GIS Mapping Terintegrasi</p>
        </footer>
    </div>

</body>
</html>
