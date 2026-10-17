<x-program-admin-layout>
    <div class="max-w-xl mx-auto space-y-6 animate-fade-in" x-data="groupCarbonCalculator()">
        <!-- Google Form Accent Header -->
        <div class="bg-white border border-t-8 border-t-emerald-600 rounded-2xl shadow-sm overflow-hidden border-slate-150">
            <div class="p-6 space-y-2">
                <h1 class="text-xl font-extrabold text-slate-900">Formulir Laporan Karbon Kelompok</h1>
                <p class="text-xs text-slate-400">Gunakan form ini untuk merekam total emisi perjalanan, penggunaan energi, dan penanaman pohon kolektif program.</p>
                <div class="border-t pt-3 text-[10px] text-red-500 font-bold uppercase tracking-wide">
                    * Diisi harian oleh Program Admin / Koordinator Lapangan
                </div>
            </div>
        </div>

        <!-- Forms Inputs Card (GForm Style) -->
        <form method="POST" action="{{ route('program-admin.carbon.store') }}" class="space-y-4 text-xs">
            @csrf

            <!-- Program Selector -->
            <div class="bg-white border rounded-2xl p-5 shadow-sm space-y-3 border-slate-150">
                <label class="block font-bold text-slate-800 uppercase text-[10px]">Pilih Program Kerja</label>
                <select name="program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-emerald-500">
                    @foreach($programs as $prog)
                        <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Selector -->
            <div class="bg-white border rounded-2xl p-5 shadow-sm space-y-3 border-slate-150">
                <label class="block font-bold text-slate-800 uppercase text-[10px]">Tanggal Laporan Harian</label>
                <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-emerald-500">
            </div>

            <!-- Input fields -->
            <div class="bg-white border rounded-2xl p-5 shadow-sm space-y-3 border-slate-150">
                <label class="block font-bold text-slate-800 uppercase text-[10px]">Total Jarak Naik Motor Kelompok (Km)</label>
                <input type="number" name="motor_km" x-model.number="inputs.motor_km" placeholder="Total kilometer..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-emerald-500">
            </div>

            <div class="bg-white border rounded-2xl p-5 shadow-sm space-y-3 border-slate-150">
                <label class="block font-bold text-slate-800 uppercase text-[10px]">Total Jarak Naik Mobil Kelompok (Km)</label>
                <input type="number" name="mobil_km" x-model.number="inputs.mobil_km" placeholder="Total kilometer..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-emerald-500">
            </div>

            <div class="bg-white border rounded-2xl p-5 shadow-sm space-y-3 border-slate-150">
                <label class="block font-bold text-slate-800 uppercase text-[10px]">Total Listrik Berjalan Kelompok (Jam)</label>
                <input type="number" name="listrik_jam" x-model.number="inputs.listrik_jam" placeholder="Total jam..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-emerald-500">
            </div>

            <div class="bg-white border rounded-2xl p-5 shadow-sm space-y-3 border-slate-150">
                <label class="block font-bold text-slate-800 uppercase text-[10px]">Total Sampah Organik Diolah (Kg)</label>
                <input type="number" name="sampah_organik_kg" x-model.number="inputs.sampah_organik_kg" placeholder="Total berat..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-emerald-500">
            </div>

            <div class="bg-white border rounded-2xl p-5 shadow-sm space-y-3 border-slate-150">
                <label class="block font-bold text-slate-800 uppercase text-[10px]">Total Pohon Mangrove Ditanam (Batang)</label>
                <input type="number" name="pohon_ditanam" x-model.number="inputs.pohon_ditanam" placeholder="Total batang..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50 focus:border-emerald-500">
            </div>

            <!-- Dynamic Live Calculations Card -->
            <div class="bg-slate-900 text-white rounded-3xl p-5 shadow-lg space-y-4">
                <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest">Kalkulator Dampak Kelompok (Live)</span>
                <div class="grid grid-cols-3 gap-3 text-center border-b border-white/10 pb-4 text-xs">
                    <div>
                        <span class="block text-[8px] text-gray-400 uppercase">Emisi</span>
                        <strong class="block text-red-400 font-bold mt-1" x-text="formatKg(emissions)">0 Kg</strong>
                    </div>
                    <div>
                        <span class="block text-[8px] text-gray-400 uppercase">Reduksi</span>
                        <strong class="block text-green-400 font-bold mt-1" x-text="formatKg(offsets)">0 Kg</strong>
                    </div>
                    <div>
                        <span class="block text-[8px] text-gray-400 uppercase">Net Balance</span>
                        <strong class="block font-extrabold mt-1" 
                                :class="balance <= 0 ? 'text-green-400' : 'text-red-400'"
                                x-text="formatKg(balance)">0 Kg</strong>
                    </div>
                </div>
                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-bold transition">
                    Kirim Laporan Karbon Kelompok
                </button>
            </div>
        </form>
    </div>

    <!-- Live Calculation JS script -->
    <script>
        function groupCarbonCalculator() {
            return {
                inputs: {
                    motor_km: 0,
                    mobil_km: 0,
                    listrik_jam: 0,
                    sampah_organik_kg: 0,
                    pohon_ditanam: 0
                },

                get emissions() {
                    let total = 0.0;
                    total += (Number(this.inputs.motor_km) || 0) * 0.1;
                    total += (Number(this.inputs.mobil_km) || 0) * 0.2;
                    total += (Number(this.inputs.listrik_jam) || 0) * 0.5;
                    return total;
                },

                get offsets() {
                    let total = 0.0;
                    total += (Number(this.inputs.sampah_organik_kg) || 0) * 2.0;
                    total += (Number(this.inputs.pohon_ditanam) || 0) * 50.0;
                    return total;
                },

                get balance() {
                    return this.emissions - this.offsets;
                },

                formatKg(val) {
                    return val.toFixed(2) + ' Kg';
                }
            }
        }
    </script>
</x-program-admin-layout>
