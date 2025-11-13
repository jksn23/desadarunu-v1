<div class="space-y-6">
    <div class="rounded-2xl bg-white p-6 shadow">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard Admin Desa</h1>
        <p class="text-sm text-gray-500">Ikhtisar kinerja kas desa berdasarkan transaksi operator.</p>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-blue-100 bg-blue-50 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Total Pemasukan</p>
                <p class="mt-2 text-2xl font-semibold text-blue-900">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl border border-red-100 bg-red-50 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-red-600">Total Pengeluaran</p>
                <p class="mt-2 text-2xl font-semibold text-red-700">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Saldo Akhir</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-700">Rp {{ number_format($netCash, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-2xl border border-gray-200 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Transaksi</p>
                <p class="mt-2 text-2xl font-semibold text-gray-900">{{ number_format($transactionCount) }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl bg-white p-6 shadow">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Operator Aktif</p>
            <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $operatorCount }}</p>
            <p class="text-sm text-gray-500 mt-2">Jumlah operator yang siap mencatat transaksi lapangan.</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Kategori Teratas</p>
            @if ($topCategories->isNotEmpty())
                <p class="mt-2 text-lg font-semibold text-gray-900">{{ $topCategories->first()['name'] }}</p>
                <p class="text-sm text-gray-500">Rp {{ number_format($topCategories->first()['total'], 0, ',', '.') }} tercatat tahun ini.</p>
            @else
                <p class="mt-2 text-sm text-gray-500">Belum ada data kategori.</p>
            @endif
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 shadow lg:col-span-2">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Tren 6 Bulan</h2>
            </div>
            <canvas id="village-line-chart" class="mt-4 h-64"></canvas>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow">
            <h2 class="text-lg font-semibold text-gray-900">Komposisi Kategori</h2>
            <canvas id="village-donut-chart" class="mt-4 h-64"></canvas>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow">
        <h2 class="text-lg font-semibold text-gray-900">Aktivitas Operator Terbaru</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Waktu</th>
                        <th class="px-4 py-3 text-left">Operator</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                        <th class="px-4 py-3 text-left">Deskripsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($recentLogs as $log)
                        <tr>
                            <td class="px-4 py-3 text-gray-600">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ $log->user?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $log->action }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $log->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow">
        <h2 class="text-lg font-semibold text-gray-900">Transaksi Terakhir</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Deskripsi</th>
                        <th class="px-4 py-3 text-left">Kategori</th>
                        <th class="px-4 py-3 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($latestTransactions as $transaction)
                        <tr>
                            <td class="px-4 py-3 text-gray-600">{{ optional($transaction->transaction_date)->format('d M Y') }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $transaction->description }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $transaction->category?->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $transaction->type === 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada transaksi yang tercatat.</td>
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
        const months = @json($monthly->pluck('label'));
        const incomeData = @json($monthly->pluck('income'));
        const expenseData = @json($monthly->pluck('expense'));
        const categoryLabels = @json($categories->pluck('name'));
        const categoryTotals = @json($categories->pluck('total'));

        new Chart(document.getElementById('village-line-chart'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: incomeData,
                        borderColor: '#0ea5e9',
                        backgroundColor: 'rgba(14,165,233,0.1)',
                        tension: 0.4,
                        fill: true,
                    },
                    {
                        label: 'Pengeluaran',
                        data: expenseData,
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249,115,22,0.1)',
                        tension: 0.4,
                        fill: true,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
            },
        });

        new Chart(document.getElementById('village-donut-chart'), {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryTotals,
                    backgroundColor: ['#2563eb', '#f97316', '#059669', '#9333ea', '#facc15', '#14b8a6'],
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
            },
        });
    </script>
@endpush
