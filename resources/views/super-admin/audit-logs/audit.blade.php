<x-app-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-3xl border border-gray-150 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900 font-serif">Audit & Compliance</h1>
                <p class="text-xs text-slate-400 mt-1">Audit keabsahan aktivitas lapangan, kebenaran titik koordinat (Geospasial), kelengkapan dokumen pendukung, dan keaktifan peserta.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- 1. Audit Aktivitas & Geospasial (Submissions) -->
            <div class="bg-white border rounded-3xl p-6 shadow-sm space-y-6">
                <div class="border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-800 uppercase">Audit Laporan Tugas & Geospasial</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">Memeriksa apakah koordinat pengiriman form sesuai dengan batas wilayah geofence zona program.</p>
                </div>
                <div class="space-y-4">
                    @foreach($submissions as $sub)
                        <div class="border p-4 rounded-2xl bg-slate-50 space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <strong class="text-xs text-slate-800 block">{{ $sub->task?->title }}</strong>
                                    <span class="text-[10px] text-gray-400">Oleh: {{ $sub->user?->name }}</span>
                                </div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded uppercase {{ $sub->status === 'approved' ? 'bg-green-100 text-green-950' : 'bg-yellow-100 text-yellow-950' }}">
                                    {{ $sub->status }}
                                </span>
                            </div>
                            
                            <!-- Geospatial Validation flags -->
                            <div class="grid grid-cols-2 gap-4 text-[10px] bg-white border p-3 rounded-xl">
                                <div>
                                    <span class="text-gray-400 font-bold block">Koordinat GPS:</span>
                                    <span class="text-slate-800 font-mono">{{ $sub->content_data['lokasi_gps'] ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400 font-bold block">Foto Bukti (Selfie):</span>
                                    @if($sub->selfie_path)
                                        <a href="{{ $sub->selfie_path }}" target="_blank" class="text-blue-500 hover:underline">Lihat Foto</a>
                                    @else
                                        <span class="text-gray-300">Tanpa Foto</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($submissions->isEmpty())
                        <p class="text-center py-6 text-gray-400 text-xs">Belum ada laporan aktivitas lapangan dikirim.</p>
                    @endif
                </div>
            </div>

            <!-- 2. Audit Kehadiran Geospasial (Attendances) -->
            <div class="bg-white border rounded-3xl p-6 shadow-sm space-y-6">
                <div class="border-b pb-3">
                    <h3 class="text-sm font-bold text-slate-800 uppercase">Audit Kehadiran Relawan</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">Memeriksa status ray-casting check-in relawan apakah dilakukan di dalam area geofence.</p>
                </div>
                <div class="space-y-4">
                    @foreach($attendances as $att)
                        <div class="border p-4 rounded-2xl bg-slate-50 space-y-2">
                            <div class="flex justify-between items-center text-xs">
                                <strong>{{ $att->user?->name }}</strong>
                                <span class="font-mono text-gray-400">{{ $att->created_at->format('d M H:i') }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white p-2.5 rounded-xl border text-[10px]">
                                <div>
                                    <span class="text-gray-400">Kegiatan:</span>
                                    <span class="font-bold text-slate-750 block">{{ $att->activity?->name }}</span>
                                </div>
                                <span class="font-bold px-2 py-0.5 rounded uppercase {{ $att->check_in_status === 'inside_zone' ? 'bg-green-100 text-green-950' : 'bg-red-100 text-red-950' }}">
                                    {{ $att->check_in_status === 'inside_zone' ? 'Valid (Inside)' : 'Bypass / Outside' }}
                                </span>
                            </div>
                            @if($att->bypass_reason)
                                <p class="text-[9px] text-red-600 bg-red-50 p-2 rounded-lg font-medium border border-red-100">
                                    Alasan Bypass: "{{ $att->bypass_reason }}"
                                </p>
                            @endif
                        </div>
                    @endforeach
                    @if($attendances->isEmpty())
                        <p class="text-center py-6 text-gray-400 text-xs">Belum ada absen check-in dilakukan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
