<x-app-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex justify-between items-center bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Audit Trail / Logs</h1>
                <p class="text-xs text-slate-400 mt-1">Rekam jejak aktivitas administrasi, audit model database, dan log masuk pengguna secara realtime.</p>
            </div>
        </div>

        <!-- Audit Table -->
        <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b text-gray-500 font-bold uppercase">
                            <th class="p-4">Waktu</th>
                            <th class="p-4">Nama User</th>
                            <th class="p-4">Tipe Aksi</th>
                            <th class="p-4">Target Model</th>
                            <th class="p-4">IP Address</th>
                            <th class="p-4">User Agent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-slate-700">
                        @foreach($logs as $log)
                            <tr>
                                <td class="p-4 font-semibold text-gray-400">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                <td class="p-4 font-semibold text-slate-800">{{ $log->user?->name ?? 'System' }}</td>
                                <td class="p-4">
                                    <span class="bg-slate-100 text-slate-800 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="p-4 font-mono">{{ class_basename($log->model_type) }} (ID: {{ substr($log->model_id, 0, 8) }}...)</td>
                                <td class="p-4 text-gray-500">{{ $log->ip_address }}</td>
                                <td class="p-4 text-gray-400 truncate max-w-xs" title="{{ $log->user_agent }}">{{ $log->user_agent }}</td>
                            </tr>
                        @endforeach
                        @if($logs->isEmpty())
                            <tr><td colspan="6" class="p-8 text-center text-gray-400">Belum ada catatan aktivitas.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
