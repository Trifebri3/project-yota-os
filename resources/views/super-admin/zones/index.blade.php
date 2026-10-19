<x-app-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-3xl border border-gray-150 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900 font-serif">Manajemen Wilayah & Geofence</h1>
                <p class="text-xs text-slate-400 mt-1">Buat batas area kerja polygon geofence resmi program, hubungkan untuk verifikasi kehadiran dan laporan lapangan.</p>
            </div>
            <button onclick="resetZoneForm()" class="bg-[#D9F99D] hover:bg-lime-300 text-lime-950 text-xs font-extrabold px-4 py-2.5 rounded-full shadow transition">
                Reset / Buat Baru
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Interactive Map Drawing -->
            <div class="lg:col-span-2 bg-white border rounded-3xl p-5 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <span id="map-instructions" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Polygon Coordinates Drawing Board</span>
                    <button onclick="clearMapPoints()" class="text-xs text-red-500 font-semibold hover:underline">Clear Points</button>
                </div>
                <div id="draw-map" class="w-full h-[400px] rounded-2xl z-10"></div>
                <div class="text-[10px] text-gray-400">
                    * Klik berurutan pada peta untuk menggambar area batas wilayah kerja Polygon (Minimal 3 titik).
                </div>
            </div>

            <!-- Right: Create/Edit Zone Form -->
            <div class="bg-white border rounded-3xl p-6 shadow-sm">
                <form id="zone-form" method="POST" action="{{ route('super-admin.zones.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf
                    <h3 id="form-title" class="text-sm font-bold text-slate-900 uppercase">Simpan Area Geofence</h3>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Hubungkan Ke Program Utama</label>
                        <select id="zone-program" name="program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                            @foreach($programs as $prog)
                                <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Wilayah Zona</label>
                        <input type="text" id="zone-name" name="name" required placeholder="e.g. Pantai Rancabuaya" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Deskripsi Area</label>
                        <textarea id="zone-desc" name="description" placeholder="Penjelasan area..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50" rows="2"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tipe Penggunaan</label>
                            <select id="zone-type" name="type" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                                <option value="attendance">Absensi Kehadiran</option>
                                <option value="task">Tugas Lapangan</option>
                                <option value="activity">Umum / Kegiatan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Status Zona</label>
                            <select id="zone-status" name="status_zona" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                                <option value="aktif">Aktif</option>
                                <option value="belum_aktif">Belum Aktif</option>
                                <option value="monitoring">Dalam Monitoring</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Foto Lokasi Zona (Image)</label>
                        <input type="file" name="photo_zona" accept="image/*" class="w-full mt-1">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Koordinat Polygon (GeoJSON)</label>
                        <textarea id="geojson-input" name="geojson" required readonly placeholder="Klik pada peta di sebelah kiri untuk meletakkan titik koordinat..." class="w-full mt-1 px-4 py-2.5 border rounded-xl font-mono bg-gray-100 text-gray-500 cursor-not-allowed" rows="3"></textarea>
                    </div>

                    <button type="submit" id="submit-btn" class="w-full bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold py-3 rounded-full shadow transition">
                        Simpan Area Geofence
                    </button>
                </form>
            </div>
        </div>

        <!-- Zones Listing with colors -->
        <div class="bg-white border rounded-3xl shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-sm font-bold text-slate-900 uppercase">Daftar Zona Kerja Lapangan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Foto Zona</th>
                            <th class="p-4">Nama Zona</th>
                            <th class="p-4">Program Kerja</th>
                            <th class="p-4">Tipe & Status</th>
                            <th class="p-4">Jumlah Koordinat</th>
                            <th class="p-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($zones as $zone)
                            <tr>
                                <td class="p-4">
                                    @if($zone->photo_zona)
                                        <img src="{{ $zone->photo_zona }}" class="w-12 h-8 rounded-lg object-cover">
                                    @else
                                        <span class="text-gray-300">No Photo</span>
                                    @endif
                                </td>
                                <td class="p-4 font-semibold">{{ $zone->name }}</td>
                                <td class="p-4 text-gray-500">{{ $zone->program?->name }}</td>
                                <td class="p-4">
                                    <span class="bg-lime-50 text-lime-900 text-[10px] font-bold px-2 py-0.5 rounded uppercase mr-1">
                                        {{ $zone->type }}
                                    </span>
                                    @php
                                        $colorMap = [
                                            'aktif' => 'bg-green-100 text-green-950',
                                            'belum_aktif' => 'bg-red-100 text-red-950',
                                            'monitoring' => 'bg-yellow-100 text-yellow-950',
                                            'selesai' => 'bg-blue-100 text-blue-950'
                                        ];
                                    @endphp
                                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full {{ $colorMap[$zone->status_zona] ?? 'bg-slate-100 text-slate-900' }} uppercase">
                                        {{ $zone->status_zona }}
                                    </span>
                                </td>
                                <td class="p-4 font-mono">{{ is_array($zone->geojson) ? count($zone->geojson) : 0 }} titik</td>
                                <td class="p-4 flex gap-2">
                                    <button onclick="loadZoneToForm({{ json_encode($zone) }})" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-xl transition">
                                        Edit
                                    </button>
                                    <form action="{{ route('super-admin.zones.delete', $zone->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus zona ini?');" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-650 font-bold px-3 py-1.5 rounded-xl transition">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let map;
        let drawnPolygon = null;
        let markerPoints = [];
        let rawCoords = [];

        document.addEventListener('DOMContentLoaded', function () {
            map = L.map('draw-map').setView([-6.200000, 106.816666], 11);

            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri, USGS',
                maxZoom: 19
            }).addTo(map);

            // Draw click points
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;

                rawCoords.push([lat, lng]);

                const marker = L.marker([lat, lng]).addTo(map);
                markerPoints.push(marker);

                if (drawnPolygon) {
                    map.removeLayer(drawnPolygon);
                }

                if (rawCoords.length >= 3) {
                    drawnPolygon = L.polygon(rawCoords, { color: '#4CAF50', fillColor: '#4CAF50', fillOpacity: 0.2 }).addTo(map);
                }

                document.getElementById('geojson-input').value = JSON.stringify(rawCoords);
            });
        });

        function clearMapPoints() {
            markerPoints.forEach(m => map.removeLayer(m));
            markerPoints = [];

            if (drawnPolygon) {
                map.removeLayer(drawnPolygon);
                drawnPolygon = null;
            }

            rawCoords = [];
            document.getElementById('geojson-input').value = '';
        }

        function loadZoneToForm(zone) {
            // Set Form Title & Action to Update
            document.getElementById('form-title').innerText = 'Edit Area Geofence';
            document.getElementById('zone-form').action = `/super-admin/zones/update/${zone.id}`;
            document.getElementById('submit-btn').innerText = 'Perbarui Area Geofence';
            
            // Clear current map markers
            clearMapPoints();

            // Prepopulate input fields
            document.getElementById('zone-program').value = zone.program_id || '';
            document.getElementById('zone-name').value = zone.name || '';
            document.getElementById('zone-desc').value = zone.description || '';
            document.getElementById('zone-type').value = zone.type || 'attendance';
            document.getElementById('zone-status').value = zone.status_zona || 'aktif';

            // Populate Raw Coordinates
            if (zone.geojson && zone.geojson.length > 0) {
                rawCoords = zone.geojson;
                document.getElementById('geojson-input').value = JSON.stringify(rawCoords);

                // Add markers & draw polygon on Leaflet map
                rawCoords.forEach(point => {
                    const marker = L.marker(point).addTo(map);
                    markerPoints.push(marker);
                });

                drawnPolygon = L.polygon(rawCoords, { color: '#4CAF50', fillColor: '#4CAF50', fillOpacity: 0.2 }).addTo(map);
                
                // Adjust map zoom to fit coordinates boundary bounds
                const bounds = L.latLngBounds(rawCoords);
                map.fitBounds(bounds);
            }
        }

        function resetZoneForm() {
            document.getElementById('form-title').innerText = 'Simpan Area Geofence';
            document.getElementById('zone-form').action = "{{ route('super-admin.zones.store') }}";
            document.getElementById('submit-btn').innerText = 'Simpan Area Geofence';

            document.getElementById('zone-name').value = '';
            document.getElementById('zone-desc').value = '';
            document.getElementById('zone-type').value = 'attendance';
            document.getElementById('zone-status').value = 'aktif';

            clearMapPoints();
        }
    </script>
</x-app-layout>
