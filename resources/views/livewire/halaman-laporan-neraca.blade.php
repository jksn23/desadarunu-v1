@php
    $formatCurrency = fn (float $value) => 'Rp ' . number_format($value, 0, ',', '.');
@endphp

<div class="space-y-6">
    <section class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Neraca Buku Kas Desa</h2>
                <p class="text-sm text-gray-500">Ringkasan posisi keuangan desa hingga tanggal yang dipilih.</p>
            </div>
            <div class="flex flex-col gap-3 md:flex-row md:items-end">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-gray-400">Per Tanggal</label>
                    <input
                        type="date"
                        wire:model.live="asOfDate"
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
                    Cetak Neraca
                </button>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Aset</h3>
                <span class="text-xs font-medium uppercase tracking-wide text-gray-400">{{ count($assets) }} akun</span>
            </div>
            <div class="mt-4 space-y-3">
                @foreach ($assets as $item)
                    <div class="rounded-xl bg-green-50/70 px-4 py-3">
                        <p class="text-sm font-semibold text-gray-900">{{ $item['name'] }}</p>
                        <p class="text-sm font-semibold text-green-600">{{ $formatCurrency($item['amount']) }}</p>
                        <p class="text-xs text-gray-500">{{ $item['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Kewajiban</h3>
                <span class="text-xs font-medium uppercase tracking-wide text-gray-400">{{ count($liabilities) }} akun</span>
            </div>
            <div class="mt-4 space-y-3">
                @foreach ($liabilities as $item)
                    <div class="rounded-xl bg-yellow-50/70 px-4 py-3">
                        <p class="text-sm font-semibold text-gray-900">{{ $item['name'] }}</p>
                        <p class="text-sm font-semibold text-yellow-600">{{ $formatCurrency($item['amount']) }}</p>
                        <p class="text-xs text-gray-500">{{ $item['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Ekuitas</h3>
                <span class="text-xs font-medium uppercase tracking-wide text-gray-400">{{ count($equity) }} akun</span>
            </div>
            <div class="mt-4 space-y-3">
                @foreach ($equity as $item)
                    <div class="rounded-xl bg-blue-50/70 px-4 py-3">
                        <p class="text-sm font-semibold text-gray-900">{{ $item['name'] }}</p>
                        <p class="text-sm font-semibold text-blue-600">{{ $formatCurrency($item['amount']) }}</p>
                        <p class="text-xs text-gray-500">{{ $item['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="rounded-2xl bg-gradient-to-r from-blue-600 to-blue-500 p-6 text-white shadow-lg">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-blue-200">Saldo Kas Desa</p>
                <p class="text-3xl font-semibold">{{ $formatCurrency($cashBalance) }}</p>
            </div>
            <div class="text-sm text-blue-100">
                <p>
                    Neraca mengikuti prinsip bahwa total aset harus sama dengan total kewajiban dan ekuitas.
                    Gunakan halaman ini untuk berdiskusi dengan bendahara desa mengenai posisi kas terkini.
                </p>
            </div>
        </div>
    </section>
</div>
