<x-app-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-3xl border border-gray-150 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900 font-serif">Manajemen User</h1>
                <p class="text-xs text-slate-400 mt-1">Kelola data profil, peran otoritas, dan penugasan wilayah kerja relawan.</p>
            </div>
            <div class="flex gap-2">
                <button onclick="toggleAssignModal(true)" class="bg-[#D9F99D] hover:bg-lime-300 text-lime-950 text-xs font-extrabold px-4 py-2.5 rounded-full shadow transition">
                    + Tugaskan User Ke Wilayah
                </button>
                <button onclick="openCreateUserModal()" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full shadow transition">
                    + Tambah Akun Baru
                </button>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white border rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Foto</th>
                            <th class="p-4">Nama & Organisasi</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">No. HP</th>
                            <th class="p-4">Peran</th>
                            <th class="p-4">Assignment Program</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($users as $user)
                            <tr>
                                <td class="p-4">
                                    @if($user->photo_profil)
                                        <img src="{{ $user->photo_profil }}" class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-500 uppercase">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <strong class="text-slate-800">{{ $user->name }}</strong>
                                    <span class="block text-[10px] text-gray-400 mt-0.5">{{ $user->organisasi ?? 'Mitra Umum' }}</span>
                                </td>
                                <td class="p-4">{{ $user->email }}</td>
                                <td class="p-4 text-gray-400">{{ $user->phone ?? '-' }}</td>
                                <td class="p-4">
                                    <span class="bg-lime-100 text-lime-950 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="p-4 max-w-xs truncate">
                                    @forelse($user->assignments as $as)
                                        <span class="inline-block bg-slate-50 border text-slate-700 text-[10px] px-2 py-0.5 rounded mr-1" title="Program: {{ $as->program?->name }}">
                                            {{ $as->program?->name }} ({{ $as->zone?->name ?? 'Global' }})
                                        </span>
                                    @empty
                                        <span class="text-gray-300">Belum ditugaskan</span>
                                    @endforelse
                                </td>
                                <td class="p-4">
                                    <span class="font-extrabold text-[10px] {{ $user->status === 'active' ? 'text-green-600' : 'text-red-500' }} uppercase">
                                        {{ $user->status }}
                                    </span>
                                </td>
                                <td class="p-4 flex gap-2">
                                    <button onclick="openEditUserModal({{ json_encode($user) }})" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-xl transition">
                                        Edit
                                    </button>
                                    @if(Auth::id() !== $user->id)
                                        <form action="{{ route('super-admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-650 font-bold px-3 py-1.5 rounded-xl transition">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('super-admin.users.toggle', $user->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-slate-50 border text-slate-700 text-[10px] font-semibold px-2 py-1.5 rounded-xl transition">
                                            Toggle
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Add/Edit User Modal Dialog -->
        <div id="userModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border">
                <div class="flex justify-between items-center mb-6">
                    <h3 id="user-modal-title" class="text-sm font-bold text-slate-900 uppercase">Tambah Akun Pengguna</h3>
                    <button onclick="toggleUserModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form id="user-form" method="POST" action="{{ route('super-admin.users.store') }}" enctype="multipart/form-data" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Lengkap</label>
                        <input type="text" id="usr-name" name="name" required placeholder="Nama lengkap..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Email Address</label>
                        <input type="email" id="usr-email" name="email" required placeholder="email@yota.com" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Password (<span id="pwd-required-text">Wajib</span>)</label>
                        <input type="password" id="usr-pwd" name="password" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Peran / Role</label>
                            <select id="usr-role" name="role" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                                <option value="volunteer">Volunteer</option>
                                <option value="peserta">Peserta Program</option>
                                <option value="project-officer">Project Officer</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="program-admin">Program Admin</option>
                                <option value="super-admin">Super Admin</option>
                                <option value="viewer">Viewer (Melihat & Memonitoring)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Organisasi</label>
                            <input type="text" id="usr-org" name="organisasi" placeholder="e.g. CSR Bakrie, DLH" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Telepon (HP)</label>
                            <input type="text" id="usr-phone" name="phone" placeholder="e.g. +62812" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Foto Profil (Image)</label>
                            <input type="file" name="photo_profil" accept="image/*" class="w-full mt-1 text-[10px]">
                        </div>
                    </div>
                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="toggleUserModal(false)" class="bg-slate-100 text-slate-700 font-bold px-4 py-2.5 rounded-full">Cancel</button>
                        <button type="submit" class="bg-[#4CAF50] text-white font-bold px-4 py-2.5 rounded-full shadow">Simpan Akun</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Assignment Modal Dialog -->
        <div id="assignModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Tugaskan User Ke Wilayah Kerja</h3>
                    <button onclick="toggleAssignModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form method="POST" action="{{ route('super-admin.users.assign') }}" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Pilih Pengguna</label>
                        <select name="user_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->role }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Pilih Program Kerja</label>
                        <select name="program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                            @foreach($programs as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Pilih Wilayah Zona (Optional)</label>
                        <select name="zone_id" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                            <option value="">-- Wilayah Global Program (Tanpa Geofence) --</option>
                            @foreach($zones as $z)
                                <option value="{{ $z->id }}">{{ $z->name }} (Program: {{ $z->program?->name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="toggleAssignModal(false)" class="bg-slate-100 text-slate-700 font-bold px-4 py-2.5 rounded-full">Cancel</button>
                        <button type="submit" class="bg-[#4CAF50] text-white font-bold px-4 py-2.5 rounded-full shadow">Simpan Penugasan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleUserModal(show) {
            document.getElementById('userModal').classList.toggle('hidden', !show);
        }
        function toggleAssignModal(show) {
            document.getElementById('assignModal').classList.toggle('hidden', !show);
        }

        function openCreateUserModal() {
            document.getElementById('user-modal-title').innerText = 'Tambah Akun Pengguna';
            document.getElementById('user-form').action = "{{ route('super-admin.users.store') }}";
            
            document.getElementById('usr-pwd').required = true;
            document.getElementById('pwd-required-text').innerText = 'Wajib';

            document.getElementById('usr-name').value = '';
            document.getElementById('usr-email').value = '';
            document.getElementById('usr-pwd').value = '';
            document.getElementById('usr-org').value = '';
            document.getElementById('usr-phone').value = '';
            document.getElementById('usr-role').value = 'volunteer';

            toggleUserModal(true);
        }

        function openEditUserModal(user) {
            document.getElementById('user-modal-title').innerText = 'Edit Akun Pengguna';
            document.getElementById('user-form').action = `/super-admin/users/update/${user.id}`;
            
            document.getElementById('usr-pwd').required = false;
            document.getElementById('pwd-required-text').innerText = 'Isi jika ingin ganti';

            document.getElementById('usr-name').value = user.name || '';
            document.getElementById('usr-email').value = user.email || '';
            document.getElementById('usr-pwd').value = '';
            document.getElementById('usr-org').value = user.organisasi || '';
            document.getElementById('usr-phone').value = user.phone || '';
            document.getElementById('usr-role').value = user.role || 'volunteer';

            toggleUserModal(true);
        }
    </script>
</x-app-layout>
