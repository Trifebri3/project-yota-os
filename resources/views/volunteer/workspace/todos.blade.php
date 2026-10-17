<x-volunteer-layout>
    <div class="space-y-6 animate-fade-in">
        <!-- Header -->
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-slate-50 text-slate-700 rounded-2xl flex items-center justify-center font-bold">
                    📋
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-800">Daftar To-Do List</h1>
                    <p class="text-[10px] text-slate-400 mt-0.5">Pantau, kolaborasikan, dan laporkan penyelesaian tugas program.</p>
                </div>
            </div>
            <button onclick="toggleCreateModal(true)" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full transition active:scale-95">
                + Baru
            </button>
        </div>

        <!-- Task List -->
        <div class="space-y-4">
            @foreach($todos as $todo)
                <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                                {{ $todo->type === 'admin' ? 'TUGAS ADMIN' : ($todo->type === 'collaborative' ? 'KOLABORATIF' : 'INDIVIDU') }}
                            </span>
                            <h3 class="text-sm font-bold text-slate-800 mt-1">{{ $todo->title }}</h3>
                            @if($todo->description)
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $todo->description }}</p>
                            @endif
                        </div>
                        
                        <!-- Badges -->
                        <div class="text-right">
                            @if($todo->status === 'completed' && $todo->verification_status === 'approved')
                                <span class="text-[8px] font-bold bg-green-50 text-green-700 border border-green-200 px-2.5 py-1 rounded-full uppercase">
                                    Selesai & Logbook
                                </span>
                            @elseif($todo->status === 'pending' && $todo->verification_status === 'pending')
                                <span class="text-[8px] font-bold bg-yellow-50 text-yellow-700 border border-yellow-200 px-2.5 py-1 rounded-full uppercase">
                                    Menunggu Verifikasi
                                </span>
                            @elseif($todo->verification_status === 'rejected')
                                <span class="text-[8px] font-bold bg-red-50 text-red-700 border border-red-200 px-2.5 py-1 rounded-full uppercase">
                                    Laporan Ditolak
                                </span>
                            @else
                                <span class="text-[8px] font-bold bg-slate-100 text-slate-700 border border-slate-200 px-2.5 py-1 rounded-full uppercase">
                                    Belum Selesai
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Collaborators list if collaborative -->
                    @if($todo->type === 'collaborative' && is_array($todo->collaborators))
                        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 text-[10px] text-slate-500 space-y-1">
                            <span class="font-bold text-slate-400 uppercase tracking-wider block">Anggota Kelompok:</span>
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                <span class="bg-white px-2 py-0.5 rounded border">{{ $todo->creator?->name }} (Pembuat)</span>
                                @foreach($programMembers->whereIn('id', $todo->collaborators) as $collab)
                                    <span class="bg-white px-2 py-0.5 rounded border">{{ $collab->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Submitted photo & notes preview if completed or pending verification -->
                    @if($todo->photo_path)
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex gap-3 items-center">
                            <a href="{{ $todo->photo_path ? (str_starts_with($todo->photo_path, 'http') ? $todo->photo_path : (str_starts_with($todo->photo_path, '/storage/') ? $todo->photo_path : (str_starts_with($todo->photo_path, 'storage/') ? '/' . $todo->photo_path : '/storage/' . ltrim($todo->photo_path, '/')))) : '' }}" target="_blank" class="w-14 h-14 rounded-xl overflow-hidden shrink-0 border bg-white">
                                <img src="{{ $todo->photo_path ? (str_starts_with($todo->photo_path, 'http') ? $todo->photo_path : (str_starts_with($todo->photo_path, '/storage/') ? $todo->photo_path : (str_starts_with($todo->photo_path, 'storage/') ? '/' . $todo->photo_path : '/storage/' . ltrim($todo->photo_path, '/')))) : '' }}" class="w-full h-full object-cover">
                            </a>
                            <div class="text-[10px] text-slate-600">
                                <strong class="text-slate-700 block mb-0.5">Catatan Laporan:</strong>
                                {{ $todo->notes }}
                                <span class="block text-[8px] text-slate-400 mt-1">Oleh: {{ $todo->completedBy?->name ?? $todo->creator?->name }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Submit verification form -->
                    @if(($todo->status === 'pending' || $todo->verification_status === 'rejected') && !$todo->photo_path)
                        <form method="POST" action="{{ route('volunteer.workspace.todos.submit', $todo->id) }}" enctype="multipart/form-data" x-data="{ isSubmitting: false }" @submit="isSubmitting = true" class="pt-3 border-t space-y-3">
                            @csrf
                            <h4 class="text-[10px] font-bold text-slate-750 uppercase tracking-wider">Lapor Penyelesaian Tugas</h4>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-450 uppercase mb-1">Bukti Foto (Maks 2MB)</label>
                                    <x-universal-upload id="todo_photo_{{ $todo->id }}" name="photo" :programId="$programId" :required="true" />
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold text-slate-450 uppercase mb-1">Catatan Singkat</label>
                                    <input type="text" name="notes" required placeholder="e.g. Kegiatan berjalan lancar..." class="w-full text-xs px-3 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50] focus:ring-0">
                                </div>
                            </div>
                            <button type="submit" :disabled="isSubmitting" class="w-full bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-[10px] font-bold py-2 rounded-xl transition flex items-center justify-center gap-2">
                                <template x-if="isSubmitting">
                                    <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </template>
                                <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim Laporan Penyelesaian'"></span>
                            </button>
                        </form>
                    @endif

                    <!-- Footer Details -->
                    <div class="flex justify-between items-center text-[9px] text-slate-400 pt-2 border-t">
                        <span>Dibuat: <strong>{{ $todo->creator?->name }}</strong></span>
                        @if($todo->created_by === Auth::id())
                            <form method="POST" action="{{ route('volunteer.workspace.todos.delete', $todo->id) }}" onsubmit="return confirm('Hapus tugas To-Do ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline font-bold">Hapus</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach

            @if($todos->isEmpty())
                <div class="text-center py-12 bg-white border border-dashed rounded-3xl text-slate-400 text-xs font-medium">
                    Belum ada To-Do list. Klik tombol "Baru" di atas untuk membuat.
                </div>
            @endif
        </div>

        <!-- Create Modal -->
        <div id="createModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
            <div class="bg-white w-full max-w-sm rounded-3xl shadow-xl overflow-y-auto max-h-[90vh] animate-scale-up">
                <div class="bg-slate-50 px-5 py-4 border-b flex justify-between items-center">
                    <h3 class="text-xs font-bold text-slate-800">Buat Tugas To-Do Baru</h3>
                    <button onclick="toggleCreateModal(false)" class="text-slate-400 hover:text-slate-600 text-sm font-bold">✕</button>
                </div>
                <form method="POST" action="{{ route('volunteer.workspace.todos.store') }}" x-data="{ isSubmitting: false }" @submit="isSubmitting = true" class="p-5 space-y-4">
                    @csrf
                    <input type="hidden" name="program_id" value="{{ $programId }}">

                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Nama Tugas / Judul</label>
                        <input type="text" name="title" required placeholder="e.g. Survei Kebutuhan Masyarakat" class="w-full text-xs px-3.5 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50] focus:ring-0">
                    </div>

                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Deskripsi Detail</label>
                        <textarea name="description" rows="2" placeholder="Detail apa yang perlu dilakukan..." class="w-full text-xs px-3.5 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50] focus:ring-0"></textarea>
                    </div>

                    <div>
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Jenis Kolaborasi</label>
                        <select name="type" id="todo-type" onchange="toggleCollabSelection()" class="w-full text-xs px-3.5 py-2.5 border rounded-xl bg-slate-50 focus:border-[#4CAF50] focus:ring-0">
                            <option value="individual">Individu (Sendiri)</option>
                            <option value="collaborative">Kelompok (Kolaborasi)</option>
                        </select>
                    </div>

                    <!-- Collaborator selections -->
                    <div id="collab-box" class="hidden">
                        <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Pilih Anggota Kelompok</label>
                        <div class="max-h-28 overflow-y-auto border rounded-xl p-3 bg-slate-50 space-y-2">
                            @foreach($programMembers as $mem)
                                <label class="flex items-center gap-2 text-xs text-slate-700">
                                    <input type="checkbox" name="collaborators[]" value="{{ $mem->id }}" class="rounded text-[#4CAF50] focus:ring-0">
                                    <span>{{ $mem->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end gap-2 border-t">
                        <button type="button" onclick="toggleCreateModal(false); isSubmitting = false" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold px-4 py-2.5 rounded-full transition">
                            Cancel
                        </button>
                        <button type="submit" :disabled="isSubmitting" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-[10px] font-bold px-4 py-2.5 rounded-full transition flex items-center gap-2">
                            <template x-if="isSubmitting">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Tugas'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleCreateModal(show) {
            document.getElementById('createModal').classList.toggle('hidden', !show);
        }

        function toggleCollabSelection() {
            const type = document.getElementById('todo-type').value;
            document.getElementById('collab-box').classList.toggle('hidden', type !== 'collaborative');
        }
    </script>
</x-volunteer-layout>
