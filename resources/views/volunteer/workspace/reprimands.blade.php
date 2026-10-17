<x-volunteer-layout>
    <div class="space-y-6 animate-fade-in">
        <!-- Header -->
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center text-xl shadow-inner font-bold">
                ⚠️
            </div>
            <div>
                <h1 class="text-base font-extrabold text-slate-900">Surat Teguran & Disiplin</h1>
                <p class="text-[11px] text-slate-400 mt-1">Daftar teguran resmi yang diterbitkan oleh Admin Program terkait kepatuhan dan kedisiplinan Anda.</p>
            </div>
        </div>

        <!-- Reprimands List -->
        <div class="space-y-4">
            @foreach($reprimands as $rep)
                <div class="bg-white border border-red-100 rounded-3xl p-5 shadow-sm space-y-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase">{{ $rep->date ? date('d M Y', strtotime($rep->date)) : '-' }}</span>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="bg-red-50 text-red-700 border border-red-200 px-2 py-0.5 rounded text-[9px] font-extrabold uppercase">
                                    Tingkat: {{ $rep->level }}
                                </span>
                                <h3 class="text-xs font-bold text-slate-800">Teguran Resmi Program</h3>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 bg-slate-50 p-4 rounded-2xl border leading-relaxed">{{ $rep->reason }}</p>

                    @if($rep->proof_path)
                        <div class="pt-2">
                            <span class="block text-[9px] font-bold text-slate-400 uppercase mb-2">Lampiran Bukti Dokumen:</span>
                            <a href="{{ $rep->proof_path ? (str_starts_with($rep->proof_path, 'http') ? $rep->proof_path : (str_starts_with($rep->proof_path, '/storage/') ? $rep->proof_path : (str_starts_with($rep->proof_path, 'storage/') ? '/' . $rep->proof_path : '/storage/' . ltrim($rep->proof_path, '/')))) : '' }}" target="_blank" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-[10px] font-bold px-4 py-2 rounded-xl transition">
                                📁 Lihat Lampiran
                            </a>
                        </div>
                    @endif

                    <div class="flex items-center justify-between text-[10px] text-slate-400 pt-2 border-t">
                        <span>Program: <strong>{{ $rep->program?->name }}</strong></span>
                    </div>
                </div>
            @endforeach

            @if($reprimands->isEmpty())
                <div class="text-center py-12 bg-white border border-dashed rounded-3xl text-gray-400 text-xs font-medium">
                    Tidak ada surat teguran disiplin yang diterbitkan untuk Anda. Tetap pertahankan kedisiplinan Anda!
                </div>
            @endif
        </div>
    </div>
</x-volunteer-layout>
