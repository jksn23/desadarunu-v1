@php
    $formatCurrency = fn (float $value) => 'Rp ' . number_format($value, 0, ',', '.');
@endphp

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Pendapatan</p>
            <p class="mt-4 text-3xl font-semibold text-green-600">{{ $formatCurrency($revenues) }}</p>
            <p class="mt-2 text-xs text-gray-400">Semua pemasukan dalam periode ini.</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Beban</p>
            <p class="mt-4 text-3xl font-semibold text-red-600">{{ $formatCurrency($expenses) }}</p>
            <p class="mt-2 text-xs text-gray-400">Akumulasi pengeluaran selama periode yang dipilih.</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-r from-blue-600 to-blue-500 p-6 text-white shadow-lg">
            <p class="text-sm font-medium uppercase tracking-wide">Laba Bersih</p>
            <p class="mt-4 text-3xl font-semibold">{{ $formatCurrency($netIncome) }}</p>
            <p class="mt-2 text-sm text-blue-100">
                {{ $netIncome >= 0 ? 'Surplus anggaran' : 'Defisit, perlu evaluasi pengeluaran.' }}
            </p>
        </div>
    </section>

    <section class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Filter Periode</h2>
                <p class="text-sm text-gray-500">Laporan laba rugi otomatis menyesuaikan dengan rentang tanggal yang Anda pilih.</p>
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
                    Cetak Laba Rugi
                </button>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Top 5 Pendapatan</h3>
                <span class="text-xs font-medium uppercase tracking-wide text-gray-400">{{ $topRevenues->count() }} kategori</span>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($topRevenues as $entry)
                    <div class="flex items-center justify-between rounded-xl bg-green-50/60 px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $entry['name'] }}</p>
                        </div>
                        <span class="text-sm font-semibold text-green-600">{{ $formatCurrency($entry['total']) }}</span>
                    </div>
                @empty
                    <p class="py-6 text-sm text-gray-500">Belum ada pendapatan pada periode ini.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Top 5 Beban</h3>
                <span class="text-xs font-medium uppercase tracking-wide text-gray-400">{{ $topExpenses->count() }} kategori</span>
            </div>
            <div class="mt-4 space-y-3">
                @forelse ($topExpenses as $entry)
                    <div class="flex items-center justify-between rounded-xl bg-red-50/60 px-4 py-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $entry['name'] }}</p>
                        </div>
                        <span class="text-sm font-semibold text-red-600">{{ $formatCurrency($entry['total']) }}</span>
                    </div>
                @empty
                    <p class="py-6 text-sm text-gray-500">Belum ada pengeluaran pada periode ini.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Ringkasan Periode</h3>
            <span class="text-xs font-medium uppercase tracking-wide text-gray-400">Rincian per bulan</span>
        </div>
        <div class="mt-4">
            @if ($periodSummary->isEmpty())
                <p class="py-6 text-sm text-gray-500">Tidak ada data untuk periode yang dipilih.</p>
            @else
                <div class="overflow-hidden rounded-xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Periode</th>
                                <th class="px-4 py-3 text-right">Pendapatan</th>
                                <th class="px-4 py-3 text-right">Beban</th>
                                <th class="px-4 py-3 text-right">Laba Bersih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach ($periodSummary as $row)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $row['label'] }}</td>
                                    <td class="px-4 py-3 text-right text-green-600">{{ $formatCurrency($row['revenues']) }}</td>
                                    <td class="px-4 py-3 text-right text-red-600">{{ $formatCurrency($row['expenses']) }}</td>
                                    <td class="px-4 py-3 text-right font-semibold {{ $row['net'] >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                                        {{ $formatCurrency($row['net']) }}
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
