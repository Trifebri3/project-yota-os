<x-app-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-3xl border border-gray-150 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900 font-serif">Manajemen Program</h1>
                <p class="text-xs text-slate-400 mt-1">Buat program baru, atur Project Officer pelaksana, supervisor verifikasi, dan mitra sponsor.</p>
            </div>
            <button onclick="openCreateProgramModal()" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full shadow transition">
                + Tambah Program Baru
            </button>
        </div>

        <!-- Programs Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($programs as $prog)
                <div class="bg-white border rounded-3xl overflow-hidden shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    @if($prog->banner_program)
                        <div class="h-36 w-full bg-cover bg-center" style="background-image: url('{{ $prog->banner_program }}')"></div>
                    @else
                        <div class="h-36 w-full bg-gradient-to-tr from-[#D9F99D] to-lime-200 flex items-center justify-center text-lime-950 font-serif font-bold text-lg">
                            YOTA SOCIAL PROGRAM
                        </div>
                    @endif

                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="bg-lime-100 text-lime-900 text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">{{ $prog->kategori_program }}</span>
                                <h3 class="text-base font-extrabold text-slate-900 mt-1.5">{{ $prog->name }}</h3>
                            </div>
                            <span class="bg-slate-100 text-slate-800 text-[10px] font-bold px-3 py-1 rounded-full uppercase">{{ $prog->status }}</span>
                        </div>

                        <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed">{{ $prog->description }}</p>

                        <div class="bg-slate-50 p-4 rounded-2xl text-[11px] space-y-2 border">
                            <div class="flex justify-between border-b pb-1"><span class="text-gray-400">Officer Utama:</span><strong class="text-slate-800">{{ $prog->projectOfficer?->name ?? 'Belum Ditugaskan' }}</strong></div>
                            <div class="flex justify-between border-b pb-1"><span class="text-gray-400">Supervisor:</span><strong class="text-slate-800">{{ $prog->supervisor?->name ?? 'Belum Ditugaskan' }}</strong></div>
                            <div class="flex justify-between border-b pb-1"><span class="text-gray-400">Anggaran Charter:</span><strong class="text-slate-800">IDR {{ number_format($prog->projectCharter?->budget ?? 0, 0, ',', '.') }}</strong></div>
                            <div class="flex justify-between border-b pb-1"><span class="text-gray-400">Periode:</span><strong class="text-slate-800">{{ $prog->start_date?->format('d M Y') ?? '-' }} s/d {{ $prog->end_date?->format('d M Y') ?? '-' }}</strong></div>
                            <div class="flex justify-between border-b pb-1"><span class="text-gray-400">Mitra / Sponsor:</span><strong class="text-slate-800">{{ $prog->mitra ?? '-' }} / {{ $prog->sponsor ?? '-' }}</strong></div>
                            <div class="flex justify-between"><span class="text-gray-400">Target Capaian:</span><strong class="text-slate-700 truncate max-w-[200px]" title="{{ $prog->target_capaian }}">{{ $prog->target_capaian ?? '-' }}</strong></div>
                        </div>

                        <!-- Kelola Impact Experience (Identitas, Banner, KPI Grafis & Before-After) -->
                        <a href="{{ route('super-admin.programs.impact-experience', $prog->id) }}" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-2.5 rounded-xl text-center transition text-xs mb-1.5 flex items-center justify-center gap-1.5 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                            <span>Kelola Impact Experience (Banner, KPI Grafis & Before-After)</span>
                        </a>

                        <!-- Timeline management button -->
                        <button onclick="openTimelineManagerModal('{{ $prog->id }}', '{{ $prog->name }}')" class="w-full bg-[#D9F99D] hover:bg-lime-300 text-lime-950 font-extrabold py-2.5 rounded-xl text-center transition text-xs mb-1">
                            Kelola Timeline Utama (Pra, Pelaksanaan, Monitoring)
                        </button>

                        <!-- CRUD Action buttons -->
                        <div class="flex gap-2">
                            <button onclick="openEditProgramModal({{ json_encode($prog) }}, {{ $prog->projectCharter?->budget ?? 0 }})" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl text-center transition">
                                Edit Program
                            </button>
                            <form action="{{ route('super-admin.programs.delete', $prog->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus program ini?');" class="inline">
                                @csrf
                                <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-650 font-bold px-4 py-2.5 rounded-xl transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Program Timelines Manager Modal -->
        <div id="timelineModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-2xl w-full shadow-2xl border overflow-y-auto max-h-[90vh] space-y-6">
                <div class="flex justify-between items-center border-b pb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase">Kelola Timeline Utama</h3>
                        <p id="timeline-program-name" class="text-[10px] text-gray-400 mt-0.5">Program: -</p>
                    </div>
                    <button onclick="toggleTimelineModal(false)" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
                </div>

                <!-- Add/Edit Timeline Phase Form -->
                <form id="timeline-form" onsubmit="submitTimelineForm(event)" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end text-xs bg-slate-50 p-4 rounded-2xl border">
                    <input type="hidden" id="timeline-id">
                    <div class="md:col-span-1">
                        <label class="block text-[9px] font-bold text-slate-400 uppercase">Nama Tahap / Fase</label>
                        <input type="text" id="timeline-name" required placeholder="e.g. Pra Pelaksanaan" class="w-full mt-1 px-3 py-2 border rounded-xl bg-white text-xs">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase">Tanggal Mulai</label>
                        <input type="date" id="timeline-start" required class="w-full mt-1 px-3 py-2 border rounded-xl bg-white text-xs">
                    </div>
                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase">Tanggal Selesai</label>
                        <input type="date" id="timeline-end" required class="w-full mt-1 px-3 py-2 border rounded-xl bg-white text-xs">
                    </div>
                    <div class="flex items-center gap-2 h-9">
                        <label class="inline-flex items-center text-[10px] font-semibold text-slate-650 cursor-pointer">
                            <input type="checkbox" id="timeline-locked" value="1" class="rounded text-[#4CAF50] focus:ring-[#4CAF50]">
                            <span class="ml-1">Kunci (Lock)</span>
                        </label>
                        <button type="submit" id="timeline-submit-btn" class="ml-auto bg-[#4CAF50] hover:bg-[#2E7D32] text-white px-3 py-2 rounded-xl font-bold transition">
                            Tambah
                        </button>
                    </div>
                </form>

                <!-- Timelines List Table -->
                <div class="border rounded-2xl overflow-hidden bg-white">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                                <th class="p-3">Tahap / Fase</th>
                                <th class="p-3">Rentang Tanggal</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="timelines-table-body" class="divide-y text-slate-700">
                            <!-- Injected dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add/Edit Program Modal Dialog -->
        <div id="progModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 id="modal-title" class="text-sm font-bold text-slate-900 uppercase">Tambah Program Kerja Baru</h3>
                    <button onclick="toggleProgramModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form id="program-form" method="POST" action="{{ route('super-admin.programs.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Program</label>
                        <input type="text" id="prog-name" name="name" required placeholder="e.g. Program Pemberdayaan Ekonomi Pesisir" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Kategori Program</label>
                            <select id="prog-kategori" name="kategori_program" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                                <option value="Lingkungan">Lingkungan</option>
                                <option value="Pendidikan">Pendidikan</option>
                                <option value="Pemberdayaan Ekonomi">Pemberdayaan Ekonomi</option>
                                <option value="Penelitian">Penelitian</option>
                                <option value="Kesehatan">Kesehatan</option>
                                <option value="Sosial">Sosial</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Pagu Anggaran (IDR)</label>
                            <input type="number" id="prog-budget" name="budget" required placeholder="e.g. 150000000" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Project Officer Utama</label>
                            <select id="prog-po" name="project_officer_id" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                                <option value="">-- Pilih Project Officer --</option>
                                @foreach($projectOfficers as $po)
                                    <option value="{{ $po->id }}">{{ $po->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Supervisor Verifikasi</label>
                            <select id="prog-supervisor" name="supervisor_id" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                                <option value="">-- Pilih Supervisor --</option>
                                @foreach($supervisors as $sp)
                                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal Mulai</label>
                            <input type="date" id="prog-start" name="start_date" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal Selesai</label>
                            <input type="date" id="prog-end" name="end_date" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Tujuan Program</label>
                        <textarea id="prog-tujuan" name="tujuan_program" placeholder="Tujuan strategis program..." class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]" rows="2"></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Deskripsi Program</label>
                        <textarea id="prog-desc" name="description" placeholder="Deskripsi..." class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]" rows="2"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Mitra Utama</label>
                            <input type="text" id="prog-mitra" name="mitra" placeholder="Mitra..." class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Sponsor Pendanaan</label>
                            <input type="text" id="prog-sponsor" name="sponsor" placeholder="Sponsor..." class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Organisasi Pelaksana</label>
                            <input type="text" id="prog-org" name="organisasi_pelaksana" placeholder="NGO..." class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Target Capaian</label>
                        <textarea id="prog-target" name="target_capaian" placeholder="e.g. 5000 pohon..." class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]" rows="2"></textarea>
                    </div>

                    <!-- Program Status (Used in edit mode) -->
                    <div id="status-container" class="hidden">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Status Program</label>
                        <select id="prog-status" name="status" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                            <option value="draft">Draft</option>
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                            <option value="arsip">Arsip</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Logo Program (Image)</label>
                            <input type="file" name="logo_program" accept="image/*" class="w-full mt-1">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Banner Program (Image)</label>
                            <input type="file" name="banner_program" accept="image/*" class="w-full mt-1">
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="toggleProgramModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full transition">Simpan Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let activeProgramId = null;

        function toggleProgramModal(show) {
            document.getElementById('progModal').classList.toggle('hidden', !show);
        }

        function toggleTimelineModal(show) {
            document.getElementById('timelineModal').classList.toggle('hidden', !show);
        }

        function openCreateProgramModal() {
            document.getElementById('modal-title').innerText = 'Tambah Program Kerja Baru';
            document.getElementById('program-form').action = "{{ route('super-admin.programs.store') }}";
            document.getElementById('status-container').classList.add('hidden');
            
            // Clear fields
            document.getElementById('prog-name').value = '';
            document.getElementById('prog-kategori').value = 'Lingkungan';
            document.getElementById('prog-budget').value = '';
            document.getElementById('prog-po').value = '';
            document.getElementById('prog-supervisor').value = '';
            document.getElementById('prog-start').value = '';
            document.getElementById('prog-end').value = '';
            document.getElementById('prog-tujuan').value = '';
            document.getElementById('prog-desc').value = '';
            document.getElementById('prog-mitra').value = '';
            document.getElementById('prog-sponsor').value = '';
            document.getElementById('prog-org').value = '';
            document.getElementById('prog-target').value = '';

            toggleProgramModal(true);
        }

        function openEditProgramModal(prog, budget) {
            document.getElementById('modal-title').innerText = 'Edit Program Kerja';
            document.getElementById('program-form').action = `/super-admin/programs/update/${prog.id}`;
            document.getElementById('status-container').classList.remove('hidden');

            // Populate fields
            document.getElementById('prog-name').value = prog.name || '';
            document.getElementById('prog-kategori').value = prog.kategori_program || 'Lingkungan';
            document.getElementById('prog-budget').value = budget || '';
            document.getElementById('prog-po').value = prog.project_officer_id || '';
            document.getElementById('prog-supervisor').value = prog.supervisor_id || '';
            
            if(prog.start_date) {
                document.getElementById('prog-start').value = prog.start_date.substring(0, 10);
            }
            if(prog.end_date) {
                document.getElementById('prog-end').value = prog.end_date.substring(0, 10);
            }

            document.getElementById('prog-tujuan').value = prog.tujuan_program || '';
            document.getElementById('prog-desc').value = prog.description || '';
            document.getElementById('prog-mitra').value = prog.mitra || '';
            document.getElementById('prog-sponsor').value = prog.sponsor || '';
            document.getElementById('prog-org').value = prog.organisasi_pelaksana || '';
            document.getElementById('prog-target').value = prog.target_capaian || '';
            document.getElementById('prog-status').value = prog.status || 'draft';

            toggleProgramModal(true);
        }

        // Program Timeline AJAX manager
        function openTimelineManagerModal(programId, programName) {
            activeProgramId = programId;
            document.getElementById('timeline-program-name').innerText = 'Program: ' + programName;
            
            // Reset timeline form
            resetTimelineForm();

            // Load existing timelines
            loadTimelinesList();
            toggleTimelineModal(true);
        }

        function loadTimelinesList() {
            fetch(`/super-admin/programs/${activeProgramId}/timelines`)
                .then(res => res.json())
                .then(timelines => {
                    const tbody = document.getElementById('timelines-table-body');
                    tbody.innerHTML = '';

                    timelines.forEach(tl => {
                        const tr = document.createElement('tr');
                        tr.className = 'border-b hover:bg-slate-50';
                        
                        const start = new Date(tl.start_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'});
                        const end = new Date(tl.end_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'});

                        tr.innerHTML = `
                            <td class="p-3 font-semibold text-slate-800">${tl.name}</td>
                            <td class="p-3 text-slate-500">${start} s/d ${end}</td>
                            <td class="p-3 font-bold uppercase text-[10px] ${tl.is_locked ? 'text-red-500' : 'text-green-600'}">
                                ${tl.is_locked ? '🔴 Locked (Kunci)' : '🟢 Active'}
                            </td>
                            <td class="p-3 flex gap-2">
                                <button onclick="editTimelinePhase(${JSON.stringify(tl).replace(/"/g, '&quot;')})" class="text-blue-500 font-bold hover:underline">Edit</button>
                                <button onclick="deleteTimelinePhase('${tl.id}')" class="text-red-500 font-bold hover:underline">Hapus</button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });

                    if (timelines.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="4" class="p-4 text-center text-gray-400 italic">Belum ada timeline utama dikonfigurasi.</td></tr>`;
                    }
                });
        }

        function resetTimelineForm() {
            document.getElementById('timeline-id').value = '';
            document.getElementById('timeline-name').value = '';
            document.getElementById('timeline-start').value = '';
            document.getElementById('timeline-end').value = '';
            document.getElementById('timeline-locked').checked = false;
            document.getElementById('timeline-submit-btn').innerText = 'Tambah';
        }

        function editTimelinePhase(tl) {
            document.getElementById('timeline-id').value = tl.id;
            document.getElementById('timeline-name').value = tl.name;
            document.getElementById('timeline-start').value = tl.start_date.substring(0, 10);
            document.getElementById('timeline-end').value = tl.end_date.substring(0, 10);
            document.getElementById('timeline-locked').checked = tl.is_locked;
            document.getElementById('timeline-submit-btn').innerText = 'Update';
        }

        function submitTimelineForm(e) {
            e.preventDefault();

            const tlId = document.getElementById('timeline-id').value;
            const name = document.getElementById('timeline-name').value;
            const start = document.getElementById('timeline-start').value;
            const end = document.getElementById('timeline-end').value;
            const locked = document.getElementById('timeline-locked').checked ? 1 : 0;

            const url = tlId 
                ? `/super-admin/programs/timelines/update/${tlId}` 
                : `/super-admin/programs/${activeProgramId}/timelines/store`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    name: name,
                    start_date: start,
                    end_date: end,
                    is_locked: locked
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    resetTimelineForm();
                    loadTimelinesList();
                } else {
                    alert('Gagal menyimpan timeline.');
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan jaringan.');
            });
        }

        function deleteTimelinePhase(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus timeline utama ini?')) return;

            fetch(`/super-admin/programs/timelines/delete/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadTimelinesList();
                }
            });
        }
    </script>
</x-app-layout>
