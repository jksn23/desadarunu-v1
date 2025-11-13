@php
    $formatCurrency = fn (float $value) => 'Rp ' . number_format($value, 0, ',', '.');
    $role = auth()->user()?->role;
    $canManageTransactions = in_array($role, ['admin_web', 'admin_desa']);
    $canCreateTransactions = $role === 'operator';
@endphp

<div class="space-y-6">
    <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Pemasukan</span>
                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                    + kas
                </span>
            </div>
            <p class="mt-4 text-3xl font-semibold text-gray-900">{{ $formatCurrency($totalIncome) }}</p>
            <p class="mt-2 text-sm text-gray-500">Periode: {{ $dateRangeLabel }}</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Pengeluaran</span>
                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700">
                    - kas
                </span>
            </div>
            <p class="mt-4 text-3xl font-semibold text-gray-900">{{ $formatCurrency($totalExpense) }}</p>
            <p class="mt-2 text-sm text-gray-500">Periode: {{ $dateRangeLabel }}</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-r from-blue-600 to-blue-500 p-6 shadow-lg text-white">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium uppercase tracking-wide">Saldo Kas</span>
                <span class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-1 text-xs font-semibold">
                    Real-time
                </span>
            </div>
            <p class="mt-4 text-3xl font-semibold">{{ $formatCurrency($balance) }}</p>
            <p class="mt-2 text-sm text-blue-100">
                {{ $balance >= 0 ? 'Kondisi kas sehat' : 'Perhatikan defisit kas' }}
            </p>
        </div>
    </section>

    <section class="rounded-2xl bg-white p-6 shadow-sm border border-gray-100">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="flex flex-col gap-2 md:flex-row md:items-center">
                <div class="relative md:w-72">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </span>
                    <input
                        type="search"
                        wire:model.live.debounce.400ms="search"
                        placeholder="Cari transaksi atau kategori..."
                        class="w-full rounded-full border border-gray-200 bg-gray-50 py-2.5 pl-10 pr-4 text-sm text-gray-700 focus:border-blue-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-500">Jenis</label>
                    <select
                        wire:model.live="typeFilter"
                        class="rounded-full border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                        <option value="semua">Semua</option>
                        <option value="pemasukan">Pemasukan</option>
                        <option value="pengeluaran">Pengeluaran</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-500">Periode</label>
                    <select
                        wire:model.live="periodFilter"
                        class="rounded-full border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                        <option value="today">Hari Ini</option>
                        <option value="this_week">Minggu Ini</option>
                        <option value="last_week">Minggu Lalu</option>
                        <option value="this_month">Bulan Ini</option>
                        <option value="last_month">Bulan Lalu</option>
                        <option value="all">Semua Waktu</option>
                    </select>
                </div>
            </div>
            @if ($canCreateTransactions)
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full border border-blue-600 px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 transition-colors"
                        onclick="window.dispatchEvent(new CustomEvent('open-kategori-modal'))"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 6h16" />
                            <path d="M4 12h16" />
                            <path d="M4 18h12" />
                        </svg>
                        Kelola Kategori
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 transition-colors"
                        onclick="window.dispatchEvent(new CustomEvent('open-transaksi-modal'))"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                        Catat Transaksi
                    </button>
                </div>
            @endif
        </div>

        <div class="mt-6 overflow-hidden rounded-2xl border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 bg-white">
                <thead class="bg-gray-50">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                        <th class="px-5 py-3">Tanggal</th>
                        <th class="px-5 py-3">Deskripsi</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3 text-right">Jumlah</th>
                        <th class="px-5 py-3 text-center">Jenis</th>
                        @if ($canManageTransactions)
                            <th class="px-5 py-3 text-right w-24">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse ($transactions as $transaction)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-5 py-4 text-gray-700">
                                {{ optional($transaction->transaction_date)->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-gray-900">
                                    {{ \Illuminate\Support\Str::limit($transaction->description, 60) }}
                                </p>
                                <p class="text-xs text-gray-500">Dibuat {{ optional($transaction->created_at)->diffForHumans() }}</p>
                            </td>
                            <td class="px-5 py-4 text-gray-700">
                                {{ $transaction->category?->name ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-right font-semibold {{ $transaction->type === 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $formatCurrency($transaction->amount) }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $transaction->type === 'pemasukan' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($transaction->type) }}
                                </span>
                            </td>
                            @if ($canManageTransactions)
                                <td class="px-5 py-4 text-right">
                                    <button
                                        type="button"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:border-red-500 hover:text-red-600 transition-colors"
                                        wire:click="deleteTransaction({{ $transaction->id }})"
                                        onclick="return confirm('Hapus transaksi ini?')"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 6h18" />
                                            <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                            <path d="M10 11v6" />
                                            <path d="M14 11v6" />
                                        </svg>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canManageTransactions ? 6 : 5 }}" class="px-5 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 7h16" />
                                            <path d="M4 11h16" />
                                            <path d="M4 15h16" />
                                            <path d="M4 19h16" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-600">Belum ada transaksi pada periode ini.</p>
                                    @if ($canCreateTransactions)
                                        <p class="text-xs text-gray-400">Catat transaksi pertama Anda dengan tombol "Catat Transaksi".</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($transactions->hasPages())
                <div class="border-t border-gray-100 bg-gray-50 px-5 py-4">
                    {{ $transactions->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h3>
                <span class="text-xs font-medium uppercase tracking-wide text-gray-400">5 Transaksi terakhir</span>
            </div>
            <div class="mt-5 space-y-4">
                @forelse ($latestTransactions as $item)
                    <div class="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full {{ $item->type === 'pemasukan' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 3v18" />
                                    <path d="{{ $item->type === 'pemasukan' ? 'M16 7l-4-4-4 4' : 'M8 17l4 4 4-4' }}" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ \Illuminate\Support\Str::limit($item->description, 42) }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $item->transaction_date?->format('d M Y') }} • {{ $item->category?->name ?? 'Tanpa kategori' }}
                                </p>
                            </div>
                        </div>
                        <span class="text-sm font-semibold {{ $item->type === 'pemasukan' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $formatCurrency($item->amount) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada aktivitas terbaru.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-blue-100 bg-blue-50 p-6 shadow-inner">
            <h3 class="text-lg font-semibold text-blue-900">Tips Manajemen Kas</h3>
            <ul class="mt-4 space-y-3 text-sm text-blue-800">
                <li class="flex gap-2">
                    <span class="mt-1 h-2 w-2 rounded-full bg-blue-400"></span>
                    Catat transaksi harian untuk memantau arus kas secara real-time.
                </li>
                <li class="flex gap-2">
                    <span class="mt-1 h-2 w-2 rounded-full bg-blue-400"></span>
                    Kelompokkan kategori pemasukan dan pengeluaran agar laporan lebih akurat.
                </li>
                <li class="flex gap-2">
                    <span class="mt-1 h-2 w-2 rounded-full bg-blue-400"></span>
                    Manfaatkan fitur laporan untuk evaluasi bulanan bersama perangkat desa.
                </li>
            </ul>
        </div>
    </section>
</div>
