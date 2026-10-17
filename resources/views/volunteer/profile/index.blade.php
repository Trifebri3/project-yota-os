<x-volunteer-layout>
    <div class="space-y-6 animate-fade-in text-xs">
        <!-- Header -->
        <div class="flex items-center gap-3 bg-white p-5 rounded-3xl border shadow-sm">
            <div class="w-12 h-12 rounded-full overflow-hidden bg-slate-100 border shrink-0">
                @if($user->photo_profil)
                    <img src="{{ $user->photo_profil }}" class="w-full h-full object-cover">
                @elseif($user->avatar)
                    <img src="{{ $user->avatar }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-400 text-base font-bold bg-slate-50">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div>
                <h1 class="text-sm font-extrabold text-slate-900 leading-snug">{{ $user->name }}</h1>
                <span class="bg-[#4CAF50]/15 text-[#2E7D32] text-[8px] font-extrabold px-2 py-0.5 rounded uppercase block mt-1 w-max">
                    {{ $user->role ?? 'Volunteer' }}
                </span>
            </div>
        </div>

        <!-- Personal Info details card -->
        <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-3.5">
            <div class="flex justify-between items-center border-b pb-2">
                <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider">Informasi Profil</span>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-slate-400">Email:</span>
                    <strong class="font-medium text-slate-750">{{ $user->email }}</strong>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-slate-400">Nomor Telepon:</span>
                    <strong class="font-medium text-slate-750">{{ $user->phone ?? '-' }}</strong>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-slate-400">Organisasi:</span>
                    <strong class="font-medium text-slate-750">{{ $user->organisasi ?? 'Yota Organization' }}</strong>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-slate-400">Status Keanggotaan:</span>
                    <strong class="font-bold text-green-600 uppercase">{{ $user->status ?? 'ACTIVE' }}</strong>
                </div>
                <div class="flex justify-between pb-1 text-amber-800 font-semibold">
                    <span>🏆 Poin Penghargaan:</span>
                    <strong class="font-extrabold text-amber-900">{{ $totalAchievementPoints }} Poin</strong>
                </div>
            </div>
        </div>

        <!-- QR CODES SECTION -->
        <div class="space-y-4">
            <div class="border-b border-slate-100 pb-2">
                <span class="text-[10px] font-extrabold text-slate-800 uppercase tracking-wider block">ID Anggota QR Code</span>
                <p class="text-[9px] text-slate-400 mt-0.5">Tunjukkan QR Code ini kepada Admin Program untuk absensi atau identifikasi fisik lapangan.</p>
            </div>

            <!-- QR: ID Anggota / User QR -->
            <div class="bg-white border rounded-3xl p-5 shadow-sm flex flex-col items-center text-center space-y-4">
                <div>
                    <span class="bg-slate-100 text-slate-750 text-[8px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Identitas Unik Relawan</span>
                    <h3 class="text-xs font-extrabold text-slate-850 mt-1.5">{{ $user->name }}</h3>
                </div>

                <div class="p-3 bg-white border rounded-2xl shadow-inner">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode('USER:' . $user->id) }}" alt="User QR" class="w-32 h-32">
                </div>

                <div class="text-[9px] text-slate-400 leading-normal max-w-xs">
                    <span class="font-mono font-bold text-slate-650 block text-[10px] mb-1">ID: {{ $user->id }}</span>
                    QR Code ini menyimpan identifikasi unik Anda. Tunjukkan kode ini untuk presensi kehadiran lapangan cepat oleh Admin Program.
                </div>
            </div>

            <!-- QR: Quick Login via Signed URL -->
            <div class="bg-white border rounded-3xl p-5 shadow-sm flex flex-col items-center text-center space-y-4">
                <div>
                    <span class="bg-blue-50 text-blue-800 text-[8px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">PWA Quick Login QR</span>
                    <h3 class="text-xs font-extrabold text-slate-850 mt-1.5">Login Cepat Auto-Auth</h3>
                </div>

                <div class="p-3 bg-white border rounded-2xl shadow-inner">
                    @php
                        $signedLoginUrl = \Illuminate\Support\Facades\URL::signedRoute('quick-login', ['user' => $user->id]);
                    @endphp
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($signedLoginUrl) }}" alt="Quick Login QR" class="w-32 h-32">
                </div>

                <div class="text-[9px] text-slate-400 leading-normal max-w-xs">
                    Simpan atau screenshot QR Code ini. Pindai menggunakan kamera handphone Anda saat posisi keluar (logout) untuk otomatis masuk tanpa memasukkan email & password.
                </div>
            </div>
        </div>
    </div>
</x-volunteer-layout>
