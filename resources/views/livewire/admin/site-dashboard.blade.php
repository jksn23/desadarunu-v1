<div class="space-y-6">
    <div class="rounded-2xl bg-white p-6 shadow">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard Admin Website</h1>
        <p class="text-sm text-gray-500">Ringkasan status pengguna dan aktivitas sistem.</p>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-blue-100 bg-blue-50 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Admin Desa</p>
                <p class="mt-2 text-2xl font-semibold text-blue-900">{{ $adminCount }}</p>
            </div>
            <div class="rounded-2xl border border-indigo-100 bg-indigo-50 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-indigo-600">Operator</p>
                <p class="mt-2 text-2xl font-semibold text-indigo-900">{{ $operatorCount }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Total Transaksi</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ number_format($transactionCount) }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Log Aktivitas</p>
                <p class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($logCount) }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white p-6 shadow">
            <h2 class="text-lg font-semibold text-gray-900">Tren Aktivitas 6 Bulan</h2>
            <canvas id="site-dashboard-line" class="mt-4 h-64"></canvas>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow">
            <h2 class="text-lg font-semibold text-gray-900">Distribusi Log Berdasarkan Role</h2>
            <div class="mt-4 space-y-3">
                @forelse ($activityByRole as $role => $total)
                    <div class="flex items-center justify-between rounded-xl border border-gray-100 px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ str_replace('_', ' ', ucfirst($role ?? 'unknown')) }}</p>
                        </div>
                        <span class="text-sm font-semibold text-blue-600">{{ number_format($total) }} log</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada data log yang tercatat.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow">
        <h2 class="text-lg font-semibold text-gray-900">Pengguna Terbaru</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-left">Tanggal Dibuat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($recentAdmins as $user)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600">
                                    {{ str_replace('_', ' ', ucfirst($user->role)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const siteLabels = @json($monthlyTransactions->pluck('label'));
        const siteTotals = @json($monthlyTransactions->pluck('total'));

        new Chart(document.getElementById('site-dashboard-line'), {
            type: 'line',
            data: {
                labels: siteLabels,
                datasets: [{
                    label: 'Total Transaksi',
                    data: siteTotals,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79,70,229,0.1)',
                    tension: 0.4,
                    fill: true,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
            },
        });
    </script>
@endpush
