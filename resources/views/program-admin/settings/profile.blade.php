<x-program-admin-layout>
    <div class="max-w-2xl mx-auto space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-lime-100 text-lime-800 rounded-2xl flex items-center justify-center text-xl shadow-inner font-bold">
                ⚙️
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-900">Pengaturan Profil & Tanda Tangan</h1>
                <p class="text-xs text-slate-400 mt-1">Unggah Tanda Tangan Digital (TTD) Anda untuk kebutuhan validasi piagam, LPJ, dan logbook.</p>
            </div>
        </div>
        @if (session('success'))
            <div class="bg-green-50 text-green-800 border border-green-200 p-4 rounded-2xl mb-4 text-xs font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 text-red-800 border border-red-200 p-4 rounded-2xl mb-4 text-xs">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Box -->
        <form method="POST" action="{{ route('program-admin.settings.profile.update') }}" enctype="multipart/form-data" class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6 text-xs">
            @csrf

            <!-- Name Field -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                <input type="text" name="name" required value="{{ old('name', $user->name) }}" class="w-full mt-1 px-4 py-3 border rounded-2xl text-xs bg-slate-50 focus:border-[#4CAF50] outline-none">
            </div>

            <!-- Email (Disabled) -->
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Alamat Email (Login Utama)</label>
                <input type="email" disabled value="{{ $user->email }}" class="w-full mt-1 px-4 py-3 border rounded-2xl text-xs bg-slate-100 text-slate-500 cursor-not-allowed outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Phone -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nomor Telepon / WA</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 08123456789" class="w-full mt-1 px-4 py-3 border rounded-2xl text-xs bg-slate-50 focus:border-[#4CAF50] outline-none">
                </div>
                <!-- Organization -->
                <div class="space-y-1">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Organisasi / Lembaga</label>
                    <input type="text" name="organisasi" value="{{ old('organisasi', $user->organisasi) }}" placeholder="e.g. Yota Community Foundation" class="w-full mt-1 px-4 py-3 border rounded-2xl text-xs bg-slate-50 focus:border-[#4CAF50] outline-none">
                </div>
            </div>

            <!-- Signature Upload Block -->
            <div class="space-y-3 border-t pt-6">
                <h3 class="text-sm font-bold text-slate-800">Tanda Tangan Digital (TTD)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <!-- Current Signature Preview -->
                    <div class="bg-slate-50 border border-slate-100 rounded-3xl p-4 flex flex-col items-center justify-center min-h-[150px] relative text-center">
                        <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-3">TTD Aktif Saat Ini</span>
                        @if($user->signature_path)
                            <img src="{{ $user->signature_path }}" alt="Signature Preview" class="max-h-[100px] object-contain mix-blend-multiply">
                        @else
                            <div class="text-slate-300 flex flex-col items-center">
                                <span class="text-3xl">🖋️</span>
                                <span class="text-[10px] font-medium mt-2">Belum Mengunggah TTD</span>
                            </div>
                        @endif
                    </div>

                    <!-- Signature Upload Form -->
                    <div class="space-y-3">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Unggah File TTD Baru</label>
                        <input type="file" name="signature" class="w-full text-xs">
                        <p class="text-[10px] text-slate-400 leading-normal">
                            * Unggah tanda tangan Anda di atas kertas putih polos dengan format gambar (PNG/JPG).<br>
                            * Sangat disarankan berlatar belakang transparan (PNG) untuk hasil cetakan piagam & LPJ yang premium.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex justify-end">
                <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-6 py-3.5 rounded-full shadow-lg transition active:scale-95">
                    Simpan Perubahan & TTD
                </button>
            </div>
        </form>
    </div>
</x-program-admin-layout>
