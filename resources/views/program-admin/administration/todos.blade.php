<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Manajemen & Verifikasi To-Do List</h1>
                <p class="text-xs text-slate-400 mt-1">Buat tugas khusus admin program, serta verifikasi laporan tugas mandiri relawan untuk dikonversi menjadi jam logbook.</p>
            </div>
        </div>

        <!-- Create Admin Todo & Verification Request section -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Left: Create Form -->
            <div class="bg-white border rounded-2xl p-6 shadow-sm space-y-4 xl:col-span-1">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b pb-2">Buat Tugas To-Do Admin</h3>
                <form method="POST" action="{{ route('program-admin.todos.admin.store') }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Program Kerja</label>
                        <select name="program_id" required class="w-full px-3 py-2 border rounded-lg bg-slate-50 focus:border-[#4CAF50] focus:ring-0">
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Judul / Nama Tugas</label>
                        <input type="text" name="title" required placeholder="e.g. Sosialisasi Program Kerja Kebersihan" class="w-full px-3 py-2 border rounded-lg bg-slate-50 focus:border-[#4CAF50] focus:ring-0">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Deskripsi Detail</label>
                        <textarea name="description" rows="3" placeholder="Jelaskan langkah-langkah pelaksanaan..." class="w-full px-3 py-2 border rounded-lg bg-slate-50 focus:border-[#4CAF50] focus:ring-0"></textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Target Penugasan</label>
                        <select name="target_type" id="target-type" onchange="toggleAdminCollabSelection()" class="w-full px-3 py-2 border rounded-lg bg-slate-50 focus:border-[#4CAF50] focus:ring-0">
                            <option value="all">Semua Relawan dalam Program</option>
                            <option value="specific">Relawan Tertentu saja (Spesifik)</option>
                        </select>
                    </div>

                    <div id="admin-collab-box" class="hidden">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Pilih Relawan Sasaran</label>
                        <div class="max-h-32 overflow-y-auto border rounded-lg p-3 bg-slate-50 space-y-1.5">
                            @foreach($users as $u)
                                <label class="flex items-center gap-2 text-xs text-slate-700">
                                    <input type="checkbox" name="collaborators[]" value="{{ $u->id }}" class="rounded text-[#4CAF50] focus:ring-0">
                                    <span>{{ $u->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#4CAF50] hover:bg-[#2E7D32] text-white font-bold py-2 rounded-xl transition">
                        Terbitkan Tugas To-Do
                    </button>
                </form>
            </div>

            <!-- Right: Pending Review Submissions -->
            <div class="bg-white border rounded-2xl shadow-sm xl:col-span-2 overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="p-6 border-b">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Persetujuan Laporan Penyelesaian Tugas</h3>
                        <p class="text-[10px] text-slate-400 mt-0.5">Daftar laporan tugas individu / admin yang diajukan oleh relawan dan perlu ditinjau.</p>
                    </div>
                    <div class="overflow-x-auto text-xs">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                                    <th class="p-4">Relawan</th>
                                    <th class="p-4">Tugas / To-Do</th>
                                    <th class="p-4">Bukti Laporan</th>
                                    <th class="p-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y text-slate-700">
                                @php
                                    $pendingTodos = $todos->where('verification_status', 'pending')->whereNotNull('photo_path');
                                @endphp
                                @foreach($pendingTodos as $pt)
                                    <tr>
                                        <td class="p-4">
                                            <strong class="text-slate-800">{{ $pt->completedBy?->name ?? $pt->creator?->name }}</strong>
                                        </td>
                                        <td class="p-4">
                                            <strong class="text-slate-850">{{ $pt->title }}</strong>
                                            <p class="text-gray-400 text-[10px] mt-0.5">{{ $pt->description }}</p>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-3">
                                                <a href="{{ $pt->photo_path ? (str_starts_with($pt->photo_path, 'http') ? $pt->photo_path : (str_starts_with($pt->photo_path, '/storage/') ? $pt->photo_path : (str_starts_with($pt->photo_path, 'storage/') ? '/' . $pt->photo_path : '/storage/' . ltrim($pt->photo_path, '/')))) : '' }}" target="_blank" class="w-10 h-10 rounded-lg overflow-hidden border shrink-0 bg-slate-50">
                                                    <img src="{{ $pt->photo_path ? (str_starts_with($pt->photo_path, 'http') ? $pt->photo_path : (str_starts_with($pt->photo_path, '/storage/') ? $pt->photo_path : (str_starts_with($pt->photo_path, 'storage/') ? '/' . $pt->photo_path : '/storage/' . ltrim($pt->photo_path, '/')))) : '' }}" class="w-full h-full object-cover">
                                                </a>
                                                <span class="text-[10px] text-slate-600 italic">"{{ $pt->notes }}"</span>
                                            </div>
                                        </td>
                                        <td class="p-4 flex items-center gap-2">
                                            <form method="POST" action="{{ route('program-admin.todos.approve', $pt->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white font-bold px-3 py-1.5 rounded-lg transition">Setuju</button>
                                            </form>
                                            <form method="POST" action="{{ route('program-admin.todos.reject', $pt->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-red-500 hover:bg-red-650 text-white font-bold px-3 py-1.5 rounded-lg transition">Tolak</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($pendingTodos->isEmpty())
                                    <tr><td colspan="4" class="p-8 text-center text-gray-400">Tidak ada laporan penyelesaian yang perlu ditinjau saat ini.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Admin/User Todos Listing -->
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Daftar Semua Tugas To-Do Aktif</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5 font-medium">Rekapitulasi seluruh tugas yang dibuat oleh admin maupun inisiatif relawan.</p>
                </div>
            </div>
            <div class="overflow-x-auto text-xs">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Nama Tugas</th>
                            <th class="p-4">Program Kerja</th>
                            <th class="p-4">Jenis</th>
                            <th class="p-4">Pembuat</th>
                            <th class="p-4">Status Verifikasi</th>
                            <th class="p-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($todos as $td)
                            <tr>
                                <td class="p-4">
                                    <strong class="text-slate-800">{{ $td->title }}</strong>
                                    @if($td->description)
                                        <p class="text-slate-400 text-[10px] mt-0.5 leading-relaxed">{{ $td->description }}</p>
                                    @endif
                                </td>
                                <td class="p-4 text-slate-500 font-semibold">{{ $td->program?->name }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 text-[9px] font-bold rounded-md uppercase {{ $td->type === 'admin' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : ($td->type === 'collaborative' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200') }}">
                                        {{ $td->type }}
                                    </span>
                                </td>
                                <td class="p-4 text-slate-500">{{ $td->creator?->name }}</td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 text-[9px] font-bold rounded-md uppercase {{ $td->status === 'completed' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200' }}">
                                        {{ $td->status }} ({{ $td->verification_status }})
                                    </span>
                                </td>
                                <td class="p-4">
                                    <form method="POST" action="{{ route('program-admin.todos.delete', $td->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus To-Do ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        @if($todos->isEmpty())
                            <tr><td colspan="6" class="p-8 text-center text-gray-400">Belum ada tugas To-Do aktif yang terdaftar.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleAdminCollabSelection() {
            const targetType = document.getElementById('target-type').value;
            document.getElementById('admin-collab-box').classList.toggle('hidden', targetType !== 'specific');
        }
    </script>
</x-program-admin-layout>
