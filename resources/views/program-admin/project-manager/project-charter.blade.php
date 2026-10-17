<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <a href="{{ route('program-admin.project-manager.index') }}" class="text-xs font-bold text-[#4CAF50] hover:underline">&larr; Kembali ke Dashboard</a>
                <h1 class="text-xl font-bold text-slate-900 mt-2">Project Charter: {{ $program->name }}</h1>
                <p class="text-xs text-slate-400 mt-1">Ubah visi, misi, latar belakang, ruang lingkup, dan anggaran utama program.</p>
            </div>
        </div>

        <!-- Project Charter Form -->
        <div class="bg-white border rounded-2xl p-6 shadow-sm">
            <form method="POST" action="{{ route('program-admin.charter.update', $program->id) }}" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Visi Program Kerja</label>
                        <textarea name="vision" required class="w-full px-4 py-3 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50] focus:ring-1 focus:ring-[#4CAF50]" rows="3">{{ $program->projectCharter?->vision }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Misi Program Kerja</label>
                        <textarea name="mission" required class="w-full px-4 py-3 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50] focus:ring-1 focus:ring-[#4CAF50]" rows="3">{{ $program->projectCharter?->mission }}</textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Latar Belakang Proyek</label>
                        <textarea name="background" class="w-full px-4 py-3 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50] focus:ring-1 focus:ring-[#4CAF50]" rows="4">{{ $program->projectCharter?->background }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Ruang Lingkup (Scope)</label>
                        <textarea name="scope" class="w-full px-4 py-3 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50] focus:ring-1 focus:ring-[#4CAF50]" rows="4">{{ $program->projectCharter?->scope }}</textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pagu Anggaran Utama (IDR)</label>
                    <input type="number" name="budget" value="{{ $program->projectCharter?->budget ?? 0 }}" required class="w-full max-w-xs px-4 py-3 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50] focus:ring-1 focus:ring-[#4CAF50]">
                </div>

                <div class="pt-4 border-t flex justify-end">
                    <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-6 py-3 rounded-full shadow transition">
                        Simpan Project Charter
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-program-admin-layout>
