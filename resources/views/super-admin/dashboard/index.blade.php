<x-app-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-3xl border border-gray-150 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900 font-serif">Global Command Center</h1>
                <p class="text-xs text-slate-400 mt-1">Super Admin Dashboard - Community Development & Carbon Impact Monitoring.</p>
            </div>
            <span class="text-xs font-bold text-slate-400">{{ date('d M Y') }}</span>
        </div>

        <!-- 1. Ringkasan Program -->
        <div class="space-y-3">
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ringkasan Program Kerja</span>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="bg-white border p-5 rounded-2xl shadow-sm text-center">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase">Total Program</span>
                    <strong class="block text-2xl font-extrabold text-slate-800 mt-1">{{ $stats['total_programs'] }}</strong>
                </div>
                <div class="bg-white border p-5 rounded-2xl shadow-sm text-center">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase">Total Zona</span>
                    <strong class="block text-2xl font-extrabold text-slate-800 mt-1">{{ $stats['total_zones'] }}</strong>
                </div>
                <div class="bg-white border p-5 rounded-2xl shadow-sm text-center">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase">Total Peserta</span>
                    <strong class="block text-2xl font-extrabold text-slate-800 mt-1">{{ $stats['total_participants'] }}</strong>
                </div>
                <div class="bg-white border p-5 rounded-2xl shadow-sm text-center">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase">Aktivitas</span>
                    <strong class="block text-2xl font-extrabold text-slate-800 mt-1">{{ $stats['total_activities'] }}</strong>
                </div>
                <div class="bg-white border p-5 rounded-2xl shadow-sm text-center">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase">Laporan</span>
                    <strong class="block text-2xl font-extrabold text-slate-800 mt-1">{{ $stats['total_reports'] }}</strong>
                </div>
                <div class="bg-white border p-5 rounded-2xl shadow-sm text-center">
                    <span class="block text-[10px] font-bold text-gray-400 uppercase">Jam Kontribusi</span>
                    <strong class="block text-2xl font-extrabold text-slate-800 mt-1">{{ number_format($stats['total_hours'], 1) }}h</strong>
                </div>
            </div>
        </div>

        <!-- 2. Ringkasan Karbon -->
        <div class="space-y-3">
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ringkasan Karbon & Dampak Lingkungan</span>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-red-50 border border-red-100 p-5 rounded-2xl text-center">
                    <span class="block text-[10px] font-bold text-red-500 uppercase">Total Emisi Karbon</span>
                    <strong class="block text-xl font-extrabold text-red-950 mt-1">{{ number_format($carbon['total_emissions'], 2) }} Kg CO2e</strong>
                </div>
                <div class="bg-green-50 border border-green-100 p-5 rounded-2xl text-center">
                    <span class="block text-[10px] font-bold text-green-600 uppercase">Pengurangan Karbon</span>
                    <strong class="block text-xl font-extrabold text-green-950 mt-1">{{ number_format($carbon['total_offsets'], 2) }} Kg CO2e</strong>
                </div>
                <div class="bg-blue-50 border border-blue-100 p-5 rounded-2xl text-center">
                    <span class="block text-[10px] font-bold text-blue-600 uppercase">Carbon Balance</span>
                    <strong class="block text-xl font-extrabold text-blue-950 mt-1">{{ number_format($carbon['net_balance'], 2) }} Kg CO2e</strong>
                </div>
                <div class="bg-lime-50 border border-lime-100 p-5 rounded-2xl text-center">
                    <span class="block text-[10px] font-bold text-lime-700 uppercase">Pohon Ditanam</span>
                    <strong class="block text-xl font-extrabold text-lime-950 mt-1">{{ $carbon['total_trees'] }} Pohon</strong>
                </div>
                <div class="bg-amber-50 border border-amber-100 p-5 rounded-2xl text-center">
                    <span class="block text-[10px] font-bold text-amber-700 uppercase">Sampah Terkelola</span>
                    <strong class="block text-xl font-extrabold text-amber-950 mt-1">{{ number_format($carbon['total_waste'], 1) }} Kg</strong>
                </div>
            </div>
        </div>

        <!-- 3. Ringkasan Geospasial (Interactive Leaflet Map showing Polygons) -->
        <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4">
            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Ringkasan Geospasial (Peta Sebaran Zona Kerja)</span>
            <div id="sa-dashboard-map" class="w-full h-[400px] rounded-2xl z-10"></div>
            <div class="flex gap-4 text-xs font-semibold text-gray-500 justify-center">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#4CAF50]"></span> Aktif</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#EF4444]"></span> Belum Aktif</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#F59E0B]"></span> Dalam Monitoring</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#3B82F6]"></span> Selesai</span>
            </div>
        </div>

        <!-- Recent activities audit -->
        <div class="bg-white border rounded-3xl shadow-sm overflow-hidden">
            <div class="p-5 border-b">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Aktivitas Sistem Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase"><th class="p-4">User</th><th class="p-4">Action</th><th class="p-4">Target Model</th><th class="p-4">Waktu</th></tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($recentLogs as $log)
                            <tr>
                                <td class="p-4 font-semibold">{{ $log->user?->name ?? 'System' }}</td>
                                <td class="p-4"><span class="bg-slate-100 text-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">{{ $log->action }}</span></td>
                                <td class="p-4 font-mono text-gray-400">{{ class_basename($log->model_type) }} (ID: {{ substr($log->model_id, 0, 8) }}...)</td>
                                <td class="p-4 text-gray-400">{{ $log->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Leaflet Script for Multi-Polygons Rendering -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('sa-dashboard-map').setView([-6.200000, 106.816666], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Render geofence zones
            const zones = @json($zones);
            const layersGroup = L.featureGroup();

            zones.forEach(zone => {
                if (zone.geojson && zone.geojson.length >= 3) {
                    let color = '#4CAF50'; // default aktif
                    if (zone.status_zona === 'belum_aktif') color = '#EF4444';
                    else if (zone.status_zona === 'monitoring') color = '#F59E0B';
                    else if (zone.status_zona === 'selesai') color = '#3B82F6';

                    const polygon = L.polygon(zone.geojson, {
                        color: color,
                        fillColor: color,
                        fillOpacity: 0.25,
                        weight: 2
                    }).bindPopup(`
                        <div class="p-2">
                            <h4 class="font-bold text-slate-800 text-sm">${zone.name}</h4>
                            <p class="text-xs text-slate-400 mt-1">Program: ${zone.program ? zone.program.name : '-'}</p>
                            <span class="inline-block bg-slate-100 text-slate-800 text-[10px] font-bold px-2 py-0.5 rounded mt-2 uppercase">Status: ${zone.status_zona}</span>
                        </div>
                    `);
                    layersGroup.addLayer(polygon);
                }
            });

            if (zones.length > 0) {
                layersGroup.addTo(map);
                map.fitBounds(layersGroup.getBounds());
            }
        });
    </script>
</x-app-layout>
