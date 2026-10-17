<x-program-admin-layout>
    <div class="space-y-8 animate-fade-in text-xs">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Workspace Keuangan & SPJ</h1>
                <p class="text-xs text-slate-405 mt-1">Kelola pagu anggaran, transaksi pengeluaran/pemasukan program kerja, serta validasi kuitansi SPJ.</p>
            </div>
            <div class="flex gap-2">
                <button onclick="togglePaguModal(true)" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition">
                    + Tambah Pagu Anggaran
                </button>
                <button onclick="toggleFinanceModal(true)" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow transition">
                    + Catat Transaksi
                </button>
            </div>
        </div>

        <!-- Statistics Widget Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Pagu Alokasi -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-1">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Total Alokasi Pagu</span>
                <strong class="text-lg font-black text-slate-800">IDR {{ number_format($totalAlokasi, 0, ',', '.') }}</strong>
            </div>

            <!-- Realisasi Pengeluaran -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-1">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Realisasi Pengeluaran</span>
                <strong class="text-lg font-black text-red-650">IDR {{ number_format($totalPengeluaran, 0, ',', '.') }}</strong>
            </div>

            <!-- Pemasukan -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-1">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Pemasukan Tambahan</span>
                <strong class="text-lg font-black text-[#2E7D32]">IDR {{ number_format($totalPemasukan, 0, ',', '.') }}</strong>
            </div>

            <!-- Sisa Anggaran Kas -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm space-y-1">
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Sisa Anggaran Kas</span>
                <strong class="text-lg font-black text-slate-900">IDR {{ number_format($sisaAnggaran, 0, ',', '.') }}</strong>
            </div>

            <!-- Sisa Bersih Estimasi -->
            <div class="bg-emerald-50 border border-emerald-100 p-5 rounded-2xl shadow-sm space-y-1">
                <span class="text-[9px] font-bold text-emerald-700 uppercase tracking-wider block">Sisa Bersih Estimasi</span>
                <strong class="text-lg font-black text-[#1b5e20]">IDR {{ number_format($sisaBersihEstimasi, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Left: Pagu Anggaran List -->
            <div class="bg-white border rounded-2xl shadow-sm overflow-hidden xl:col-span-1">
                <div class="p-5 border-b">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Daftar Pagu Anggaran (Sumber Dana)</h3>
                </div>
                <div class="divide-y text-slate-700">
                    @foreach($ceilings as $ceil)
                        <div class="p-4 flex justify-between items-center hover:bg-slate-50 transition">
                            <div>
                                <strong class="text-slate-900 block text-xs">{{ $ceil->source }}</strong>
                                <span class="text-[10px] text-slate-400">Program: {{ $ceil->program?->name }}</span>
                                @if($ceil->notes)
                                    <p class="text-[10px] text-slate-500 italic mt-0.5">{{ $ceil->notes }}</p>
                                @endif
                            </div>
                            <div class="text-right flex items-center gap-3">
                                <div>
                                    <strong class="text-slate-800 text-xs">IDR {{ number_format($ceil->amount, 0, ',', '.') }}</strong>
                                    <span class="block text-[9px] text-slate-400">Sisa: IDR {{ number_format($ceil->amount - $ceil->transactions->where('type', 'expense')->sum('amount'), 0, ',', '.') }}</span>
                                </div>
                                <form method="POST" action="{{ route('program-admin.finance.pagu.delete', $ceil->id) }}" onsubmit="return confirm('Hapus pagu anggaran ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold">&times;</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                    @if($ceilings->isEmpty())
                        <div class="p-8 text-center text-slate-400 italic">Belum ada pagu anggaran ditambahkan. Silakan tambahkan pagu terlebih dahulu.</div>
                    @endif
                </div>
            </div>

            <!-- Right: Buku Kas / Transactions -->
            <div class="bg-white border rounded-2xl shadow-sm overflow-hidden xl:col-span-2">
                <div class="p-5 border-b flex justify-between items-center">
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Buku Kas Transaksi Keuangan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                                <th class="p-4">Tanggal / Keterangan</th>
                                <th class="p-4">Program / Sumber Pagu</th>
                                <th class="p-4">Kategori / Tipe</th>
                                <th class="p-4">Jumlah (IDR)</th>
                                <th class="p-4">Bukti / Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-slate-700">
                            @foreach($transactions as $tx)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="p-4">
                                        <span class="text-gray-400 block text-[10px]">{{ $tx->created_at->format('d M Y H:i') }}</span>
                                        <strong class="text-slate-800">{{ $tx->title }}</strong>
                                    </td>
                                    <td class="p-4">
                                        <span class="text-slate-600 block font-medium">{{ $tx->program?->name }}</span>
                                        <span class="text-[10px] text-slate-400">Pagu: {{ $tx->budgetCeiling?->source ?? 'Umum' }}</span>
                                    </td>
                                    <td class="p-4">
                                        <span class="bg-slate-100 text-slate-700 text-[10px] font-bold px-2 py-0.5 rounded uppercase">{{ $tx->category }}</span>
                                        <span class="block text-[10px] font-bold uppercase mt-1 {{ $tx->type === 'income' ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $tx->type }}
                                        </span>
                                    </td>
                                    <td class="p-4 font-bold text-slate-900">IDR {{ number_format($tx->amount, 0, ',', '.') }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            @if($tx->receipt_path)
                                                <a href="{{ $tx->receipt_path ? (str_starts_with($tx->receipt_path, 'http') ? $tx->receipt_path : (str_starts_with($tx->receipt_path, '/storage/') ? $tx->receipt_path : (str_starts_with($tx->receipt_path, 'storage/') ? '/' . $tx->receipt_path : '/storage/' . ltrim($tx->receipt_path, '/')))) : '' }}" target="_blank" class="text-blue-500 font-bold hover:underline">Kuitansi</a>
                                            @else
                                                <span class="text-gray-300">Tanpa Bukti</span>
                                            @endif
                                            <button onclick="openEditFinanceModal('{{ $tx->id }}')" class="text-blue-600 hover:text-blue-800 font-bold">Edit</button>
                                            <form method="POST" action="{{ route('program-admin.finance.transaction.delete', $tx->id) }}" onsubmit="return confirm('Hapus transaksi kas ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if($transactions->isEmpty())
                                <tr><td colspan="5" class="p-8 text-center text-gray-400">Belum ada catatan transaksi keuangan.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal: Add Budget Ceiling -->
        <div id="paguModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-sm w-full shadow-2xl border border-gray-100 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xs font-bold text-slate-900 uppercase">Tambah Pagu Anggaran (Sumber Dana)</h3>
                    <button onclick="togglePaguModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form method="POST" action="{{ route('program-admin.finance.pagu.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Hubungkan Ke Program</label>
                        <select name="program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                            @foreach($programs as $prog)
                                <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Sumber Anggaran / Dana</label>
                        <input type="text" name="source" required placeholder="e.g. CSR Sponsor PT. Merdeka" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Nominal Pagu (IDR)</label>
                        <input type="number" name="amount" required placeholder="e.g. 50000000" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Catatan Tambahan</label>
                        <textarea name="notes" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]" rows="2"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="togglePaguModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full transition">Simpan Pagu</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Add Transaction -->
        <div id="financeModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-gray-100 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xs font-bold text-slate-900 uppercase">Catat Transaksi Baru</h3>
                    <button onclick="toggleFinanceModal(false)" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
                </div>
                <form method="POST" action="{{ route('program-admin.finance.transaction.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Hubungkan Ke Program</label>
                        <select name="program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                            @foreach($programs as $prog)
                                <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Potong dari Pagu Anggaran (Wajib)</label>
                        <select name="budget_ceiling_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                            @foreach($ceilings as $ceil)
                                <option value="{{ $ceil->id }}">
                                    {{ $ceil->program?->name }} - {{ $ceil->source }} (Sisa: IDR {{ number_format($ceil->amount - $ceil->transactions->where('type', 'expense')->sum('amount'), 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Keterangan / Judul Transaksi</label>
                        <input type="text" name="title" required placeholder="e.g. Konsumsi Rapat Koordinasi Relawan" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Deskripsi</label>
                        <textarea name="description" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]" rows="2"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tipe Kas</label>
                            <select name="type" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                                <option value="expense">Pengeluaran (Expense)</option>
                                <option value="income">Pemasukan (Income)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Kategori</label>
                            <select name="category" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                                <option value="operational">Operasional</option>
                                <option value="logistics">Logistik & Transport</option>
                                <option value="catering">Konsumsi</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Jumlah Nominal (IDR)</label>
                        <input type="number" name="amount" required placeholder="e.g. 750000" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Unggah Bukti Kuitansi (Opsional)</label>
                        <input type="file" name="receipt" accept="image/*" class="w-full mt-1 text-xs">
                    </div>

                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="toggleFinanceModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full transition">Simpan Transaksi</button>
                    </div>
                </form>
        <!-- Modal: Edit Transaction -->
        <div id="editFinanceModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-6">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-gray-100 overflow-y-auto max-h-[90vh]">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xs font-bold text-slate-900 uppercase">Edit Transaksi</h3>
                    <button onclick="toggleEditFinanceModal(false)" class="text-slate-400 hover:text-slate-650 font-bold text-lg">&times;</button>
                </div>
                <form id="editFinanceForm" method="POST" action="" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Hubungkan Ke Program</label>
                        <select name="program_id" id="edit_tx_program_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                            @foreach($programs as $prog)
                                <option value="{{ $prog->id }}">{{ $prog->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Potong dari Pagu Anggaran (Wajib)</label>
                        <select name="budget_ceiling_id" id="edit_tx_budget_ceiling_id" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                            @foreach($ceilings as $ceil)
                                <option value="{{ $ceil->id }}">
                                    {{ $ceil->program?->name }} - {{ $ceil->source }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Keterangan / Judul Transaksi</label>
                        <input type="text" name="title" id="edit_tx_title" required placeholder="e.g. Konsumsi Rapat Koordinasi Relawan" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Deskripsi</label>
                        <textarea name="description" id="edit_tx_description" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]" rows="2"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Tipe Kas</label>
                            <select name="type" id="edit_tx_type" required class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                                <option value="expense">Pengeluaran (Expense)</option>
                                <option value="income">Pemasukan (Income)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase">Kategori</label>
                            <select name="category" id="edit_tx_category" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                                <option value="operational">Operasional</option>
                                <option value="logistics">Logistik & Transport</option>
                                <option value="catering">Konsumsi</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase">Jumlah Nominal (IDR)</label>
                        <input type="number" name="amount" id="edit_tx_amount" required placeholder="e.g. 750000" class="w-full mt-1 px-4 py-2.5 border rounded-xl text-xs bg-slate-50 focus:border-[#4CAF50]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-450 uppercase font-bold">Bukti Kuitansi (Optional)</label>
                        <div class="mt-1 flex items-center gap-3">
                            <input type="file" name="receipt" accept="image/*" class="text-xs">
                            <span id="edit_tx_receipt_status" class="text-[10px] text-gray-400"></span>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end gap-3 border-t">
                        <button type="button" onclick="toggleEditFinanceModal(false)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-full transition">Cancel</button>
                        <button type="submit" class="bg-[#4CAF50] hover:bg-[#2E7D32] text-white text-xs font-bold px-4 py-2.5 rounded-full transition shadow">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.financeTransactions = @json($transactions);

        function toggleFinanceModal(show) {
            document.getElementById('financeModal').classList.toggle('hidden', !show);
        }
        function togglePaguModal(show) {
            document.getElementById('paguModal').classList.toggle('hidden', !show);
        }
        function toggleEditFinanceModal(show) {
            document.getElementById('editFinanceModal').classList.toggle('hidden', !show);
        }
        function openEditFinanceModal(id) {
            const tx = window.financeTransactions.find(t => t.id === id);
            if (!tx) return;

            document.getElementById('editFinanceForm').action = "/program-admin/finance/transaction/update/" + tx.id;
            
            document.getElementById('edit_tx_program_id').value = tx.program_id;
            document.getElementById('edit_tx_budget_ceiling_id').value = tx.budget_ceiling_id;
            document.getElementById('edit_tx_title').value = tx.title;
            document.getElementById('edit_tx_description').value = tx.description || '';
            document.getElementById('edit_tx_type').value = tx.type;
            document.getElementById('edit_tx_category').value = tx.category;
            document.getElementById('edit_tx_amount').value = Math.round(parseFloat(tx.amount));
            
            const receiptStatus = document.getElementById('edit_tx_receipt_status');
            if (tx.receipt_path) {
                receiptStatus.textContent = '(Sudah ada kuitansi)';
            } else {
                receiptStatus.textContent = '(Tanpa kuitansi)';
            }

            toggleEditFinanceModal(true);
        }
    </script>
</x-program-admin-layout>
