<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in print:bg-white print:p-0">
        <!-- Header (Hidden on print) -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm print:hidden">
            <div>
                <a href="{{ route('program-admin.administration.index') }}" class="text-xs font-bold text-[#4CAF50] hover:underline">&larr; Kembali ke Administrasi</a>
                <h1 class="text-xl font-bold text-slate-900 mt-2">Preview LPJ Otomatis</h1>
                <p class="text-xs text-slate-400 mt-1">Laporan Pertanggungjawaban dihasilkan secara instan dari database keuangan dan logbook lapangan.</p>
            </div>
            <button onclick="window.print()" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-5 py-2.5 rounded-full shadow transition">
                Cetak LPJ / Ekspor PDF
            </button>
        </div>

        <!-- Printable Document Output Container -->
        <div class="bg-slate-100 p-8 rounded-3xl border print:p-0 print:border-0 print:bg-white">
            {!! $reportHtml !!}
        </div>
    </div>
</x-program-admin-layout>
