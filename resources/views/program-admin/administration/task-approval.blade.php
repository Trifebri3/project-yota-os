<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Verifikasi Laporan Tugas</h1>
                <p class="text-xs text-slate-400 mt-1">Daftar laporan output/submission tugas relawan yang memerlukan review dan verifikasi dampak.</p>
            </div>
        </div>

        <!-- Submissions Approval List -->
        <div class="space-y-6">
            @foreach($submissions as $sub)
                <div class="bg-white border rounded-2xl shadow-sm p-6 space-y-4">
                    <div class="flex justify-between items-start border-b pb-3">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Tugas: {{ $sub->task?->title }}</span>
                            <h3 class="text-base font-bold text-slate-800 mt-1">Submitted by: {{ $sub->user?->name }}</h3>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            {{ $sub->status }}
                        </span>
                    </div>

                    <!-- Submission dynamic data content -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs text-gray-650">
                        <div class="space-y-3">
                            <h4 class="font-bold text-slate-900 uppercase text-[10px]">Data Form Lapangan (JSON Output)</h4>
                            <div class="bg-slate-50 p-4 rounded-xl space-y-2 border border-slate-100">
                                @if(is_array($sub->content_data))
                                    @foreach($sub->content_data as $key => $val)
                                        <div class="flex justify-between border-b border-gray-100 pb-1">
                                            <span class="font-semibold text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                            <span class="text-slate-800">
                                                @if(is_array($val))
                                                    {{ json_encode($val) }}
                                                @else
                                                    {{ $val }}
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-gray-400 italic">Tidak ada data form lapangan.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Attachments (Selfie and Signatures) -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-bold text-slate-900 uppercase text-[10px] mb-2">Foto Lapangan (Selfie)</h4>
                                @if($sub->selfie_path)
                                    <a href="{{ $sub->selfie_path ? (str_starts_with($sub->selfie_path, 'http') ? $sub->selfie_path : (str_starts_with($sub->selfie_path, '/storage/') ? $sub->selfie_path : (str_starts_with($sub->selfie_path, 'storage/') ? '/' . $sub->selfie_path : '/storage/' . ltrim($sub->selfie_path, '/')))) : '' }}" target="_blank" class="block border rounded-xl overflow-hidden h-28 bg-slate-50 relative group">
                                        <img src="{{ $sub->selfie_path ? (str_starts_with($sub->selfie_path, 'http') ? $sub->selfie_path : (str_starts_with($sub->selfie_path, '/storage/') ? $sub->selfie_path : (str_starts_with($sub->selfie_path, 'storage/') ? '/' . $sub->selfie_path : '/storage/' . ltrim($sub->selfie_path, '/')))) : '' }}" alt="Selfie" class="w-full h-full object-cover group-hover:scale-105 transition">
                                    </a>
                                @else
                                    <div class="border border-dashed h-28 rounded-xl flex items-center justify-center text-gray-300 italic">No photo</div>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 uppercase text-[10px] mb-2">Tanda Tangan (Signature)</h4>
                                @if($sub->signature_path)
                                    <div class="border rounded-xl p-2 bg-white h-28 flex items-center justify-center">
                                        <img src="{{ $sub->signature_path ? (str_starts_with($sub->signature_path, 'http') ? $sub->signature_path : (str_starts_with($sub->signature_path, '/storage/') ? $sub->signature_path : (str_starts_with($sub->signature_path, 'storage/') ? '/' . $sub->signature_path : '/storage/' . ltrim($sub->signature_path, '/')))) : '' }}" alt="Signature" class="max-h-full max-w-full object-contain">
                                    </div>
                                @else
                                    <div class="border border-dashed h-28 rounded-xl flex items-center justify-center text-gray-300 italic">No signature</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Verification form -->
                    <form method="POST" action="{{ route('program-admin.tasks.approve', $sub->id) }}" class="border-t pt-4 flex flex-col md:flex-row items-end md:items-center justify-between gap-4">
                        @csrf
                        <div class="flex-1 w-full">
                            <input type="text" name="remarks" placeholder="Berikan catatan verifikasi (e.g. Data valid)..." class="w-full px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" name="status" value="rejected" class="bg-red-50 hover:bg-red-100 text-red-650 text-xs font-bold px-5 py-2.5 rounded-full transition">
                                Tolak Laporan
                            </button>
                            <button type="submit" name="status" value="approved" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-6 py-2.5 rounded-full shadow transition">
                                Setujui Laporan
                            </button>
                        </div>
                    </form>
                </div>
            @endforeach

            @if($submissions->isEmpty())
                <div class="text-center py-12 bg-white border border-dashed rounded-2xl text-gray-400 font-medium">
                    Belum ada laporan tugas yang memerlukan verifikasi.
                </div>
            @endif
        </div>
    </div>
</x-program-admin-layout>
