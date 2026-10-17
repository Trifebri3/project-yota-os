<x-volunteer-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div>
            <h1 class="text-xl font-bold text-slate-900 font-serif">Resource Center & SOP</h1>
            <p class="text-xs text-slate-400 mt-1">Akses buku panduan, SOP kerja, dokumen penugasan, dan surat tugas pribadi Anda.</p>
        </div>

        <!-- Resources list -->
        <div class="bg-white border rounded-3xl shadow-sm overflow-hidden text-xs">
            <div class="p-6 border-b">
                <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-widest">Daftar Dokumen Program Anda</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Nama Dokumen</th>
                            <th class="p-4">Jenis Dokumen</th>
                            <th class="p-4">Ditujukan Ke</th>
                            <th class="p-4">Download</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($resources as $res)
                            <tr>
                                <td class="p-4 font-bold text-slate-900">{{ $res->name }}</td>
                                <td class="p-4 uppercase font-semibold text-slate-500">{{ $res->type }}</td>
                                <td class="p-4 font-semibold text-purple-700">
                                    @if($res->distribution === 'general')
                                        Semua Peserta (Umum)
                                    @else
                                        Pribadi (Khusus)
                                    @endif
                                </td>
                                <td class="p-4">
                                    <a href="{{ $res->file_path }}" download class="text-[#4CAF50] font-bold hover:underline">Download file &darr;</a>
                                </td>
                            </tr>
                        @endforeach
                        @if($resources->isEmpty())
                            <tr><td colspan="4" class="p-8 text-center text-gray-400 italic">Belum ada resource didistribusikan.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-volunteer-layout>
