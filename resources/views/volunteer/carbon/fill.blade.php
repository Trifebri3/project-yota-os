<x-volunteer-layout>
    <div class="max-w-md mx-auto space-y-6 animate-fade-in" x-data="carbonCalculator()">
        
        <!-- Google Form Purple Accent Header -->
        <div class="bg-white border border-t-8 border-t-purple-600 rounded-2xl shadow-sm overflow-hidden border-slate-150">
            <div class="p-5 space-y-2">
                <h1 class="text-xl font-extrabold text-slate-900 font-sans">Formulir Karbon Harian Relawan</h1>
                <p class="text-xs text-slate-400">
                    Program: <strong class="text-slate-800">{{ $program?->name ?? 'Global' }}</strong>
                </p>
                <div class="border-t pt-3 flex justify-between items-center text-[10px] text-red-500 font-bold uppercase tracking-wide">
                    <span>* Wajib diisi setiap hari</span>
                    <span class="text-slate-400 font-mono">{{ date('d F Y') }}</span>
                </div>
            </div>
        </div>

        @if(isset($error))
            <div class="bg-red-50 border border-red-200 text-red-750 p-5 rounded-2xl text-xs text-center font-bold">
                {{ $error }}
            </div>
        @elseif($filledToday)
            <!-- Google Form Styled Completed Screen -->
            <div class="bg-white border rounded-2xl shadow-sm p-6 text-center space-y-4">
                <div class="w-12 h-12 bg-green-50 border border-green-200 text-green-600 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800 uppercase">Laporan Karbon Dikirim</h3>
                <p class="text-xs text-slate-400">Terima kasih! Anda telah menyelesaikan laporan jejak karbon Anda untuk hari ini.</p>
                <a href="{{ route('volunteer.dashboard') }}" class="inline-block bg-[#4CAF50] text-white text-xs font-bold px-6 py-2.5 rounded-full transition">
                    Kembali Ke Dashboard
                </a>
            </div>
        @else
            <!-- Mandatory warning banner -->
            <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl text-xs text-amber-900 flex items-start gap-2">
                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <strong class="block text-[10px] uppercase font-bold tracking-wider">Perhatian!</strong>
                    <span>Laporan ini wajib dikirimkan setiap hari sebelum jam 23.59 sebagai syarat compliance aktivitas proyek.</span>
                </div>
            </div>

            <!-- Forms Inputs Cards (GForm Style) -->
            <form method="POST" action="{{ route('volunteer.carbon.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="form_id" value="{{ $form->id }}">

                @foreach($form->fields as $field)
                    <div class="bg-white border rounded-2xl p-5 shadow-sm space-y-3 border-slate-150">
                        <label class="block text-xs font-bold text-slate-800">
                            {{ $field['label'] }}
                            @if(isset($field['required']) && $field['required'])
                                <span class="text-red-500">*</span>
                            @endif
                        </label>
                        <input type="number" 
                               name="{{ $field['name'] }}"
                               x-model.number="inputs.{{ $field['name'] }}"
                               placeholder="Masukkan angka..." 
                               @if(isset($field['required']) && $field['required']) required @endif
                               class="w-full px-4 py-3 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50] focus:ring-1 focus:ring-[#4CAF50] focus:bg-white transition text-slate-850">
                    </div>
                @endforeach

                <!-- Dynamic Real-Time Calculations Card -->
                <div class="bg-slate-950 text-white rounded-3xl p-5 shadow-lg border border-slate-900 space-y-4 sticky bottom-4 z-20">
                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest">Kalkulator Jejak Karbon Instan (Live)</span>
                    <div class="grid grid-cols-3 gap-3 text-center border-b border-white/10 pb-4">
                        <div>
                            <span class="block text-[8px] text-gray-400 uppercase">Emisi</span>
                            <strong class="block text-xs font-bold text-red-400 mt-1" x-text="formatKg(emissions)">0 Kg</strong>
                        </div>
                        <div>
                            <span class="block text-[8px] text-gray-400 uppercase">Reduksi</span>
                            <strong class="block text-xs font-bold text-green-400 mt-1" x-text="formatKg(offsets)">0 Kg</strong>
                        </div>
                        <div>
                            <span class="block text-[8px] text-gray-400 uppercase">Net Balance</span>
                            <strong class="block text-xs font-extrabold mt-1" 
                                    :class="balance <= 0 ? 'text-green-400' : 'text-red-400'"
                                    x-text="formatKg(balance)">0 Kg</strong>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-[#4CAF50] hover:bg-[#2E7D32] text-white py-3 rounded-xl text-xs font-bold shadow transition active:scale-95">
                        Kirim Laporan Karbon
                    </button>
                </div>
            </form>
        @endif
    </div>

    <!-- Live Footprint Calculation JavaScript -->
    <script>
        function carbonCalculator() {
            return {
                inputs: {
                    motor_km: 0,
                    mobil_km: 0,
                    umum_km: 0,
                    listrik_jam: 0,
                    ac_jam: 0,
                    sampah_organik_kg: 0,
                    sampah_anorganik_kg: 0,
                    pohon_ditanam: 0
                },

                get emissions() {
                    let total = 0.0;
                    total += (Number(this.inputs.motor_km) || 0) * 0.1;
                    total += (Number(this.inputs.mobil_km) || 0) * 0.2;
                    total += (Number(this.inputs.umum_km) || 0) * 0.05;
                    total += (Number(this.inputs.listrik_jam) || 0) * 0.5;
                    total += (Number(this.inputs.ac_jam) || 0) * 0.8;
                    return total;
                },

                get offsets() {
                    let total = 0.0;
                    total += (Number(this.inputs.sampah_organik_kg) || 0) * 2.0;
                    total += (Number(this.inputs.sampah_anorganik_kg) || 0) * 1.5;
                    total += (Number(this.inputs.pohon_ditanam) || 0) * 50.0;
                    return total;
                },

                get balance() {
                    return this.emissions - this.offsets; // positive means net emissions, negative means net offset
                },

                formatKg(val) {
                    return val.toFixed(2) + ' Kg';
                }
            }
        }
    </script>
</x-volunteer-layout>
