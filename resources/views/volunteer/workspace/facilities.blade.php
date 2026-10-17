<x-volunteer-layout>
    <div class="space-y-6 animate-fade-in">
        <!-- Header -->
        <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl shadow-inner font-bold">
                👕
            </div>
            <div>
                <h1 class="text-base font-extrabold text-slate-900">Atribut & Inventaris</h1>
                <p class="text-[11px] text-slate-400 mt-1">Daftar kelengkapan atribut, seragam, kaos, topi, id card, dan peralatan operasional lapangan Anda.</p>
            </div>
        </div>

        <!-- Inventory List -->
        <div class="space-y-4">
            @foreach($facilities as $item)
                @if($item->facility)
                    <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xs font-bold text-slate-800">{{ $item->facility->name }}</h3>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $item->facility->description ?? 'Tidak ada deskripsi.' }}</p>
                            </div>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full uppercase {{ $item->status === 'given' ? 'bg-green-100 text-green-950' : 'bg-yellow-100 text-yellow-950' }}">
                                {{ $item->status === 'given' ? 'Sudah Diterima' : 'Menunggu Penyerahan' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-[10px] text-slate-400 pt-2 border-t">
                            <span>Program Penyerah: <strong>{{ $item->facility->program?->name }}</strong></span>
                            <span>Tanggal Update: {{ $item->updated_at ? $item->updated_at->format('d/m/Y') : '-' }}</span>
                        </div>
                    </div>
                @endif
            @endforeach

            @if($facilities->isEmpty())
                <div class="text-center py-12 bg-white border border-dashed rounded-3xl text-gray-400 text-xs font-medium">
                    Belum ada atribut atau kelengkapan inventaris operasional yang ditugaskan kepada Anda.
                </div>
            @endif
        </div>
    </div>
</x-volunteer-layout>
