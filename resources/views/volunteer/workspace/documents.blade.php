<x-volunteer-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div>
            <h1 class="text-xl font-bold text-slate-900 font-serif">Dokumen Saya & Sertifikat</h1>
            <p class="text-xs text-slate-400 mt-1">Unduh piagam penghargaan, sertifikat kompetensi, dan surat keterangan kontribusi digital Anda.</p>
        </div>

        <!-- Documents list -->
        <div class="bg-white border rounded-3xl shadow-sm overflow-hidden text-xs">
            <div class="p-6 border-b">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Sertifikat & Piagam Resmi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Nama Dokumen</th>
                            <th class="p-4">Jenis</th>
                            <th class="p-4">Tanggal Terbit</th>
                            <th class="p-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($documents as $doc)
                            <tr>
                                <td class="p-4 font-bold text-slate-900">{{ $doc->name }}</td>
                                <td class="p-4 uppercase font-semibold text-slate-500">{{ $doc->type }}</td>
                                <td class="p-4 text-slate-400">{{ $doc->created_at->format('d M Y') }}</td>
                                <td class="p-4">
                                    <a href="{{ $doc->file_path }}" target="_blank" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white font-bold px-3 py-1.5 rounded-xl transition">
                                        Download &darr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        @if($documents->isEmpty())
                            <tr><td colspan="4" class="p-8 text-center text-gray-400 italic">Belum ada sertifikat / dokumen resmi diterbitkan untuk Anda.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-volunteer-layout>
