<div class="space-y-6">
    <div class="rounded-2xl bg-white p-6 shadow">
        <h1 class="text-2xl font-semibold text-gray-900">Log Aktivitas</h1>
        <p class="text-sm text-gray-500">Pantau seluruh aktivitas pengguna berdasarkan role, jenis aksi, dan rentang waktu.</p>

        <div class="mt-4 grid gap-4 md:grid-cols-4">
            <div>
                <label class="text-xs font-semibold uppercase text-gray-400">Role</label>
                <select wire:model="role" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="all">Semua Role</option>
                    @foreach ($availableRoles as $roleOption)
                        <option value="{{ $roleOption }}">{{ str_replace('_', ' ', ucfirst($roleOption)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase text-gray-400">Aksi</label>
                <select wire:model="action" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="all">Semua Aksi</option>
                    @foreach ($availableActions as $actionOption)
                        <option value="{{ $actionOption }}">{{ str_replace('_', ' ', ucfirst($actionOption)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase text-gray-400">Modul</label>
                <select wire:model="module" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="all">Semua Modul</option>
                    @foreach ($availableModules as $moduleOption)
                        <option value="{{ $moduleOption }}">{{ ucfirst($moduleOption) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-400">Dari</label>
                    <input type="date" wire:model="startDate" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-400">Sampai</label>
                    <input type="date" wire:model="endDate" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Daftar Log</h2>
            <span class="text-xs text-gray-500">Menampilkan {{ $logs->total() }} catatan</span>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">Pengguna</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                        <th class="px-4 py-3 text-left">Deskripsi</th>
                        <th class="px-4 py-3 text-left">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($logs as $log)
                        <tr>
                            <td class="px-4 py-3 text-gray-600">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $log->user?->name ?? 'System' }}</div>
                                <div class="text-xs text-gray-400">{{ $log->role }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600">{{ $log->action }}</span>
                                <div class="text-xs text-gray-400 mt-1">{{ $log->module ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $log->description ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada log untuk filter yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
