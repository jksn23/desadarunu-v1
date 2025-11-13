@php
    $formatCurrency = fn (float $value) => 'Rp ' . number_format($value, 0, ',', '.');
@endphp

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Pemasukan</p>
            <p class="mt-4 text-3xl font-semibold text-green-600">{{ $formatCurrency($incomeTotal) }}</p>
            <p class="mt-2 text-xs text-gray-400">Periode laporan</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Pengeluaran</p>
            <p class="mt-4 text-3xl font-semibold text-red-600">{{ $formatCurrency($expenseTotal) }}</p>
            <p class="mt-2 text-xs text-gray-400">Periode laporan</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-r from-blue-600 to-blue-500 p-6 text-white shadow-lg">
            <p class="text-sm font-medium uppercase tracking-wide">Net Cash Flow</p>
            <p class="mt-4 text-3xl font-semibold">{{ $formatCurrency($netCashFlow) }}</p>
            <p class="mt-2 text-sm text-blue-100">
                {{ $netCashFlow >= 0 ? 'Surplus kas' : 'Defisit kas' }}
            </p>
        </div>
    </section>

    <section class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Filter Periode Laporan</h2>
                <p class="text-sm text-gray-500">Atur rentang tanggal untuk meninjau arus kas sesuai kebutuhan.</p>
            </div>
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-gray-400">Tanggal Mulai</label>
                    <input
                        type="date"
                        wire:model.live="startDate"
                        class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-700 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-gray-400">Tanggal Selesai</label>
                    <input
                        type="date"
                        wire:model.live="endDate"
                        class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-700 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-white px-4 py-2 text-xs font-semibold text-blue-600 shadow-sm transition hover:bg-blue-50 md:text-sm"
                        wire:click="generateAiSummary"
                        wire:loading.attr="disabled"
                        wire:target="generateAiSummary"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m12 2 3 7h7l-5.5 4.5L18 21l-6-4-6 4 1.5-7.5L2 9h7z" />
                        </svg>
                        <span wire:loading.remove wire:target="generateAiSummary">Ringkasan AI</span>
                        <span wire:loading wire:target="generateAiSummary">Memproses...</span>
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-white px-4 py-2 text-xs font-semibold text-blue-600 shadow-sm transition hover:bg-blue-50 md:text-sm"
                        onclick="window.print()"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M6 9V2h12v7" />
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                            <path d="M6 14h12v8H6z" />
                        </svg>
                        Cetak
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-white px-4 py-2 text-xs font-semibold text-blue-600 shadow-sm transition hover:bg-blue-50 md:text-sm"
                        wire:click="downloadExcel"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14"/><path d="m19 12-7 7-7-7"/></svg>
                        Excel
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-white px-4 py-2 text-xs font-semibold text-blue-600 shadow-sm transition hover:bg-blue-50 md:text-sm"
                        wire:click="downloadPdf"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M12 4h9"/><path d="M5 4h3v16H5z"/></svg>
                        PDF
                    </button>
                </div>
            </div>
        </div>
    </section>

    @if ($aiSummary || $aiError)
        <section class="rounded-2xl border border-blue-100 bg-blue-50 p-6 shadow-inner">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-blue-900">Ringkasan AI</h3>
                    <p class="text-sm text-blue-700">Analisis singkat berdasarkan data arus kas periode ini.</p>
                </div>
                @if ($aiSummary)
                    <span class="rounded-full bg-white/50 px-3 py-1 text-xs font-semibold text-blue-700">Terupdate</span>
                @endif
            </div>
            <div class="mt-4 rounded-xl bg-white/60 px-4 py-3 text-sm leading-relaxed text-blue-900 shadow">
                @if ($aiSummary)
                    {{ $aiSummary }}
                @else
                    <span class="text-red-600">{{ $aiError }}</span>
                @endif
            </div>
        </section>
    @endif

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Pemasukan per Kategori</h3>
                <span class="text-xs font-medium uppercase tracking-wide text-gray-400">{{ $incomeByCategory->count() }} kategori</span>
            </div>
            <div class="mt-4 divide-y divide-gray-100">
                @forelse ($incomeByCategory as $entry)
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $entry['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $entry['count'] }} transaksi</p>
                        </div>
                        <span class="text-sm font-semibold text-green-600">{{ $formatCurrency($entry['total']) }}</span>
                    </div>
                @empty
                    <p class="py-6 text-sm text-gray-500">Tidak ada pemasukan pada periode ini.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Pengeluaran per Kategori</h3>
                <span class="text-xs font-medium uppercase tracking-wide text-gray-400">{{ $expenseByCategory->count() }} kategori</span>
            </div>
            <div class="mt-4 divide-y divide-gray-100">
                @forelse ($expenseByCategory as $entry)
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $entry['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $entry['count'] }} transaksi</p>
                        </div>
                        <span class="text-sm font-semibold text-red-600">{{ $formatCurrency($entry['total']) }}</span>
                    </div>
                @empty
                    <p class="py-6 text-sm text-gray-500">Tidak ada pengeluaran pada periode ini.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Tren Harian</h3>
            <span class="text-xs font-medium uppercase tracking-wide text-gray-400">Rekap per hari</span>
        </div>
        <div class="mt-4">
            @if ($dailyTrend->isEmpty())
                <p class="py-6 text-sm text-gray-500">Belum ada data transaksi pada rentang tanggal yang dipilih.</p>
            @else
                <div class="overflow-hidden rounded-xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Tanggal</th>
                                <th class="px-4 py-3 text-right">Pemasukan</th>
                                <th class="px-4 py-3 text-right">Pengeluaran</th>
                                <th class="px-4 py-3 text-right">Saldo Harian</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach ($dailyTrend as $row)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $row['date'] }}</td>
                                    <td class="px-4 py-3 text-right text-green-600">{{ $formatCurrency($row['pemasukan']) }}</td>
                                    <td class="px-4 py-3 text-right text-red-600">{{ $formatCurrency($row['pengeluaran']) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold {{ ($row['pemasukan'] - $row['pengeluaran']) >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                                        {{ $formatCurrency($row['pemasukan'] - $row['pengeluaran']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
</div>
