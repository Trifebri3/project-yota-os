<x-app-layout>
    <div class="space-y-8 animate-fade-in" x-data="visualFormBuilder()">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-3xl border border-gray-150 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900 font-serif">Visual Form Builder</h1>
                <p class="text-xs text-slate-400 mt-1">Buat formulir jejak karbon secara visual mirip Google Forms tanpa perlu menulis file JSON secara manual.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Visual Form Fields Creator (GForm Style) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white border-t-8 border-t-purple-600 border rounded-3xl p-6 shadow-sm space-y-4">
                    <div class="border-b pb-3 flex justify-between items-center">
                        <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pertanyaan & Input Karbon</h3>
                        <button type="button" @click="addField()" class="bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-bold px-4 py-2 rounded-full transition">
                            + Tambah Input Baru
                        </button>
                    </div>

                    <!-- Visual Fields list -->
                    <div class="space-y-4">
                        <template x-for="(field, index) in fields" :key="index">
                            <div class="p-5 bg-slate-50 border rounded-2xl space-y-3 relative">
                                <button type="button" @click="removeField(index)" class="absolute top-4 right-4 text-red-400 hover:text-red-600 font-bold text-sm">
                                    &times; Hapus
                                </button>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-400 uppercase">Label Pertanyaan</label>
                                        <input type="text" x-model="field.label" required placeholder="e.g. Jarak Naik Motor (Km)" class="w-full mt-1 px-3 py-2 border rounded-xl bg-white text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-400 uppercase">Key / Nama Database (e.g. motor_km)</label>
                                        <input type="text" x-model="field.name" required placeholder="e.g. motor_km" class="w-full mt-1 px-3 py-2 border rounded-xl bg-white text-xs">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-xs items-center">
                                    <div>
                                        <label class="block text-[9px] font-bold text-slate-400 uppercase">Tipe Data</label>
                                        <select x-model="field.type" class="w-full mt-1 px-3 py-2 border rounded-xl bg-white text-xs">
                                            <option value="number">Angka (Number)</option>
                                            <option value="text">Teks Singkat (Text)</option>
                                            <option value="textarea">Uraian (Textarea)</option>
                                            <option value="date">Tanggal (Date)</option>
                                        </select>
                                    </div>
                                    <div class="pt-4">
                                        <label class="inline-flex items-center text-xs font-semibold cursor-pointer">
                                            <input type="checkbox" x-model="field.required" class="rounded text-purple-600 focus:ring-purple-500">
                                            <span class="ml-1">Wajib diisi (Required)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="pt-2 text-center">
                        <button type="button" @click="addField()" class="w-full border-2 border-dashed border-purple-200 hover:border-purple-400 text-purple-600 font-bold py-3 rounded-2xl transition">
                            + Tambah Kolom Pertanyaan Baru
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right: Publish Settings -->
            <div class="bg-white border rounded-3xl p-6 shadow-sm h-fit space-y-4">
                <form method="POST" action="{{ route('super-admin.forms.store') }}" @submit="compileJson($event)" class="space-y-4 text-xs">
                    @csrf
                    <h3 class="text-sm font-bold text-slate-900 uppercase">Publish Form</h3>

                    <!-- Hidden JSON field -->
                    <input type="hidden" name="fields_json" x-model="compiledJson">

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Hubungkan Ke Program Kerja</label>
                        <select name="target_type" required class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                            @foreach($programs as $p)
                                <option value="program-{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Nama Formulir</label>
                        <input type="text" name="name" required placeholder="e.g. Form Karbon Purbayani" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Kode Form (Slug)</label>
                        <input type="text" name="code" required placeholder="e.g. carbon-purbayani" class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Deskripsi Formulir</label>
                        <textarea name="description" placeholder="Penjelasan instruksi pengisian..." class="w-full mt-1 px-4 py-2.5 border rounded-xl bg-slate-50" rows="3"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold py-3 rounded-full shadow transition">
                        Publish Form Karbon
                    </button>
                </form>
            </div>
        </div>

        <!-- Custom Forms list -->
        <div class="bg-white border rounded-3xl shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <h3 class="text-sm font-bold text-slate-900 uppercase font-serif">Form Karbon Terpublish</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Nama Form</th>
                            <th class="p-4">Kode Form</th>
                            <th class="p-4">Jumlah Pertanyaan</th>
                            <th class="p-4">Dibuat Pada</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($forms as $form)
                            <tr>
                                <td class="p-4 font-semibold text-slate-800">{{ $form->name }}</td>
                                <td class="p-4 font-mono text-slate-500">{{ $form->code }}</td>
                                <td class="p-4 font-mono font-bold text-[#4CAF50]">{{ count($form->fields) }} kolom</td>
                                <td class="p-4 text-gray-400">{{ $form->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                        @if($forms->isEmpty())
                            <tr><td colspan="4" class="p-8 text-center text-gray-400">Belum ada form terpublish.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Form Builder Alpine.js Script -->
    <script>
        function visualFormBuilder() {
            return {
                fields: [
                    { name: 'motor_km', label: 'Jarak Naik Sepeda Motor (Km)', type: 'number', required: true },
                    { name: 'mobil_km', label: 'Jarak Naik Mobil Pribadi (Km)', type: 'number', required: false },
                    { name: 'listrik_jam', label: 'Durasi Penggunaan Listrik (Jam)', type: 'number', required: false },
                    { name: 'sampah_organik_kg', label: 'Berat Sampah Organik Diolah (Kg)', type: 'number', required: false },
                    { name: 'pohon_ditanam', label: 'Jumlah Pohon Ditanam (Batang)', type: 'number', required: false }
                ],
                compiledJson: '',

                addField() {
                    this.fields.push({
                        name: '',
                        label: '',
                        type: 'number',
                        required: false
                    });
                },

                removeField(index) {
                    this.fields.splice(index, 1);
                },

                compileJson(e) {
                    if (this.fields.length === 0) {
                        alert('Formulir harus memiliki minimal 1 pertanyaan!');
                        e.preventDefault();
                        return;
                    }
                    this.compiledJson = JSON.stringify(this.fields);
                }
            }
        }
    </script>
</x-app-layout>
