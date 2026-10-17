<x-volunteer-layout>
    <div class="space-y-6 animate-fade-in">
        <!-- Header -->
        <div>
            <h1 class="text-lg font-bold text-slate-900">Sertifikat Otoritas</h1>
            <p class="text-xs text-slate-400 mt-1">Daftar sertifikasi apresiasi digital atas kontribusi program sosial Anda.</p>
        </div>

        <!-- Certificates List -->
        <div class="space-y-3">
            @if(!$certificates->isEmpty() || !$uploadedDocuments->isEmpty())
                <!-- Auto-generated Certificates -->
                @foreach($certificates as $cert)
                    <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4 hover:shadow-md transition">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-lime-100 rounded-2xl flex items-center justify-center text-green-700 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                            </div>
                            <div>
                                <strong class="block text-xs text-slate-800">{{ $cert->program?->name }}</strong>
                                <span class="block text-[9px] text-gray-400 font-mono mt-0.5">No: {{ $cert->certificate_number }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center text-[10px] text-gray-500 pt-3 border-t">
                            <span>Diterbitkan: {{ $cert->issue_date?->format('d M Y') ?? '-' }}</span>
                            <a href="{{ route('program-admin.certificate.print', ['programId' => $cert->program_id, 'userId' => $cert->user_id]) }}" target="_blank" class="text-[#4CAF50] font-bold hover:underline">
                                Cetak Sertifikat &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach

                <!-- Uploaded Custom Documents (Sertifikat / Piagam) -->
                @foreach($uploadedDocuments as $doc)
                    <div class="bg-white border rounded-3xl p-5 shadow-sm space-y-4 hover:shadow-md transition">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-750 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <strong class="block text-xs text-slate-800">{{ $doc->name }}</strong>
                                <span class="block text-[9px] text-gray-400 mt-0.5">Program: {{ $doc->program?->name }}</span>
                                <span class="inline-block mt-1 bg-blue-50 text-blue-800 text-[8px] font-extrabold px-2 py-0.5 rounded uppercase tracking-wider">{{ $doc->type }}</span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center text-[10px] text-gray-500 pt-3 border-t">
                            <span>Diterbitkan: {{ $doc->created_at->format('d M Y') }}</span>
                            <a href="{{ $doc->file_path }}" target="_blank" class="text-[#4CAF50] font-bold hover:underline">
                                Unduh / Buka Dokumen &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-12 bg-white border border-dashed rounded-3xl text-gray-400 text-xs font-medium">
                    Belum ada sertifikat atau piagam diterbitkan. Selesaikan program kerja sosial untuk mendapatkan apresiasi.
                </div>
            @endif
        </div>
    </div>
</x-volunteer-layout>
