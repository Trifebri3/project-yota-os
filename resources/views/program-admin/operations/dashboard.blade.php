<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in text-xs">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Operations Workspace</h1>
                <p class="text-xs text-slate-400 mt-1">Jadwalkan kegiatan lapangan, hubungkan dengan zona geofence, dan pantau aktivitas relawan.</p>
            </div>
            <button onclick="toggleActivityModal(true)" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow transition">
                + Tambah Kegiatan Baru
            </button>
        </div>

        <!-- Validation Error Alert -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-xs">
                @foreach($errors->all() as $error)
                    <p class="font-bold">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Activities Grid -->
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Kegiatan Aktif & Jadwal Lapangan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase text-[10px]">
                            <th class="p-4">Nama Kegiatan</th>
                            <th class="p-4">Program Kerja Induk</th>
                            <th class="p-4">Jadwal Pelaksanaan</th>
                            <th class="p-4">Zona Geofence</th>
                            <th class="p-4">Timeline Acuan</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($activities as $act)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="p-4">
                                    <strong class="text-slate-900 text-xs block">{{ $act->name }}</strong>
                                    @if($act->description)
                                        <span class="text-[10px] text-slate-400 block mt-0.5">{{ Str::limit($act->description, 60) }}</span>
                                    @endif
                                </td>
                                <td class="p-4 text-gray-500 font-medium">{{ $act->workPlan?->name }}</td>
                                <td class="p-4 font-medium text-slate-650">
                                    {{ $act->start_date?->format('d M Y') ?? '-' }} s/d {{ $act->end_date?->format('d M Y') ?? '-' }}
                                </td>
                                <td class="p-4">
                                    @if($act->zone)
                                        <span class="bg-lime-100 text-lime-900 font-semibold px-2 py-0.5 rounded text-[10px] uppercase">
                                            {{ $act->zone->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">No Geofence</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($act->timeline)
                                        <span class="bg-red-50 text-red-900 border border-red-150 text-[10px] font-bold px-2.5 py-0.5 rounded">
                                            {{ $act->timeline->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">Global</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="bg-slate-100 text-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">
                                        {{ $act->status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button 
                                            onclick='openEditActivityModal(@json($act))' 
                                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold px-3 py-1.5 rounded-lg transition text-[10px]">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('program-admin.activity.delete', $act->id) }}" onsubmit="return confirm('Hapus kegiatan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold px-3 py-1.5 rounded-lg transition text-[10px]">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @if($activities->isEmpty())
                            <tr><td colspan="7" class="p-8 text-center text-gray-400 italic">Belum ada kegiatan didaftarkan.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Activity Modal -->
        <div id="activityModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-gray-150 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xs font-bold text-slate-900 uppercase">Tambah Kegiatan Baru</h3>
                    <button onclick="toggleActivityModal(false)" class="text-slate-400 hover:text-slate-650 font-bold">&times;</button>
                </div>
                @php
                    $workplans = App\Models\ProgramKerja::with('program')->get();
                @endphp
                <form method="POST" action="{{ route('program-admin.activity.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Hubungkan Ke Program Kerja</label>
                        <select name="work_plan_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                            @foreach($workplans as $wp)
                                <option value="{{ $wp->id }}">{{ $wp->program?->name }} &rarr; {{ $wp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Mengacu Ke Timeline Utama</label>
                        <select name="program_timeline_id" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                            <option value="">-- Tanpa Mengacu Timeline --</option>
                            @foreach($programs as $p)
                                <optgroup label="Program: {{ $p->name }}">
                                    @foreach($p->timelines as $tl)
                                        <option value="{{ $tl->id }}">{{ $tl->name }} ({{ $tl->start_date->format('d M Y') }} s/d {{ $tl->end_date->format('d M Y') }})</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal Mulai</label>
                            <input type="date" name="start_date" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal Selesai</label>
                            <input type="date" name="end_date" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Kegiatan Lapangan</label>
                        <input type="text" name="name" required placeholder="e.g. Pembagian Bibit Bakau Tahap 1" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Deskripsi Kegiatan</label>
                        <textarea name="description" placeholder="Detail kegiatan..." class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]" rows="3"></textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Zona Geofence Validasi (Optional)</label>
                        <select name="zone_id" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                            <option value="">-- Tanpa Validasi Geofence --</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->name }} ({{ $zone->type }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Penerima Tugas Kegiatan (Relawan)</label>
                        <div class="bg-slate-50 p-4 rounded-xl border space-y-2 max-h-40 overflow-y-auto">
                            <label class="flex items-center gap-2 cursor-pointer font-semibold pb-1.5 border-b mb-1">
                                <input type="checkbox" id="selectAllVolunteers" onchange="toggleSelectAll(this)" class="rounded text-[#4CAF50] focus:ring-[#4CAF50]">
                                <span class="text-slate-800 font-bold">Pilih Seluruh Relawan</span>
                            </label>
                            @foreach($volunteers as $v)
                                <label class="flex items-center gap-2 cursor-pointer py-1">
                                    <input type="checkbox" name="assigned_to[]" value="{{ $v->id }}" class="volunteer-checkbox rounded text-[#4CAF50] focus:ring-[#4CAF50]">
                                    <span>{{ $v->name }} ({{ $v->email }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="toggleActivityModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full transition">Simpan Kegiatan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Activity Modal -->
        <div id="editActivityModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-gray-150 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xs font-bold text-slate-900 uppercase">Edit Kegiatan Lapangan</h3>
                    <button onclick="toggleEditActivityModal(false)" class="text-slate-400 hover:text-slate-650 font-bold">&times;</button>
                </div>
                <form id="editActivityForm" method="POST" action="" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Hubungkan Ke Program Kerja</label>
                        <select name="work_plan_id" id="edit_work_plan_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                            @foreach($workplans as $wp)
                                <option value="{{ $wp->id }}">{{ $wp->program?->name }} &rarr; {{ $wp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Mengacu Ke Timeline Utama</label>
                        <select name="program_timeline_id" id="edit_program_timeline_id" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                            <option value="">-- Tanpa Mengacu Timeline --</option>
                            @foreach($programs as $p)
                                <optgroup label="Program: {{ $p->name }}">
                                    @foreach($p->timelines as $tl)
                                        <option value="{{ $tl->id }}">{{ $tl->name }} ({{ $tl->start_date->format('d M Y') }} s/d {{ $tl->end_date->format('d M Y') }})</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="edit_start_date" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="edit_end_date" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Kegiatan Lapangan</label>
                        <input type="text" name="name" id="edit_name" required placeholder="e.g. Pembagian Bibit Bakau Tahap 1" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Deskripsi Kegiatan</label>
                        <textarea name="description" id="edit_description" placeholder="Detail kegiatan..." class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]" rows="3"></textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Zona Geofence Validasi (Optional)</label>
                        <select name="zone_id" id="edit_zone_id" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                            <option value="">-- Tanpa Validasi Geofence --</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->name }} ({{ $zone->type }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Penerima Tugas Kegiatan (Relawan)</label>
                        <div class="bg-slate-50 p-4 rounded-xl border space-y-2 max-h-40 overflow-y-auto">
                            <label class="flex items-center gap-2 cursor-pointer font-semibold pb-1.5 border-b mb-1">
                                <input type="checkbox" id="editSelectAllVolunteers" onchange="toggleEditSelectAll(this)" class="rounded text-[#4CAF50] focus:ring-[#4CAF50]">
                                <span class="text-slate-800 font-bold">Pilih Seluruh Relawan</span>
                            </label>
                            @foreach($volunteers as $v)
                                <label class="flex items-center gap-2 cursor-pointer py-1">
                                    <input type="checkbox" name="assigned_to[]" value="{{ $v->id }}" class="edit-volunteer-checkbox rounded text-[#4CAF50] focus:ring-[#4CAF50]">
                                    <span>{{ $v->name }} ({{ $v->email }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="toggleEditActivityModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full transition">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleActivityModal(show) {
            document.getElementById('activityModal').classList.toggle('hidden', !show);
        }

        function toggleEditActivityModal(show) {
            document.getElementById('editActivityModal').classList.toggle('hidden', !show);
        }

        function toggleSelectAll(source) {
            const checkboxes = document.querySelectorAll('.volunteer-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
        }

        function toggleEditSelectAll(source) {
            const checkboxes = document.querySelectorAll('.edit-volunteer-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
        }

        function openEditActivityModal(activity) {
            // Set form action
            document.getElementById('editActivityForm').action = "/program-admin/activity/update/" + activity.id;

            // Set inputs
            document.getElementById('edit_work_plan_id').value = activity.work_plan_id;
            document.getElementById('edit_program_timeline_id').value = activity.program_timeline_id || '';
            document.getElementById('edit_start_date').value = activity.start_date ? activity.start_date.substring(0, 10) : '';
            document.getElementById('edit_end_date').value = activity.end_date ? activity.end_date.substring(0, 10) : '';
            document.getElementById('edit_name').value = activity.name;
            document.getElementById('edit_description').value = activity.description || '';
            document.getElementById('edit_zone_id').value = activity.zone_id || '';

            // Reset checkboxes
            const checkboxes = document.querySelectorAll('.edit-volunteer-checkbox');
            checkboxes.forEach(cb => cb.checked = false);
            document.getElementById('editSelectAllVolunteers').checked = false;

            // Mark check boxes based on assigned_to
            const assignedTo = (activity.tasks && activity.tasks.length > 0) ? activity.tasks[0].assigned_to : [];
            if (assignedTo && assignedTo.length > 0) {
                checkboxes.forEach(cb => {
                    if (assignedTo.includes(cb.value)) {
                        cb.checked = true;
                    }
                });
            } else {
                // If assignedTo is empty or null, it represents "Select All" / everyone
                document.getElementById('editSelectAllVolunteers').checked = true;
                checkboxes.forEach(cb => cb.checked = true);
            }

            toggleEditActivityModal(true);
        }
    </script>
</x-program-admin-layout>
