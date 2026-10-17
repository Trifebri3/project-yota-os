<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Administration Workspace</h1>
                <p class="text-xs text-slate-400 mt-1">Buat berkas pertanggungjawaban program (LPJ) dan cetak sertifikat kelulusan relawan secara digital.</p>
            </div>
        </div>

        <!-- Programs List for Documents -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($programs as $prog)
                <div class="bg-white border rounded-2xl p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <h3 class="text-base font-bold text-slate-800">{{ $prog->name }}</h3>
                        <p class="text-slate-400 text-xs mt-2 truncate">{{ $prog->description }}</p>
                    </div>

                    <div class="border-t pt-4 mt-6 space-y-3">
                        <div class="flex gap-3">
                            <a href="{{ route('program-admin.lpj.show', $prog->id) }}" class="flex-1 text-center bg-[#D9F99D] hover:bg-lime-300 text-lime-950 text-xs font-bold py-2.5 rounded-xl transition">
                                Generate Auto LPJ
                            </a>
                        </div>
                        
                        <!-- List volunteers for this program to print certificate -->
                        <div class="space-y-2 pt-2">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Cetak Sertifikat Relawan:</span>
                            @php
                                $volunteers = App\Models\User::where('role', 'participant')->get();
                            @endphp
                            <div class="max-h-28 overflow-y-auto divide-y border rounded-xl bg-slate-50 text-[10px]">
                                @foreach($volunteers as $v)
                                    <div class="p-2 flex justify-between items-center bg-white">
                                        <span class="font-semibold text-slate-700">{{ $v->name }}</span>
                                        <a href="{{ route('program-admin.certificate.print', ['programId' => $prog->id, 'userId' => $v->id]) }}" target="_blank" class="text-[#4CAF50] font-bold hover:underline">Cetak &rarr;</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-program-admin-layout>
