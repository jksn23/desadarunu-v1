<div class="space-y-8">
    <section class="rounded-2xl bg-white p-6 shadow">
        <h1 class="text-2xl font-semibold text-gray-900">Pusat Backup & Export</h1>
        <p class="text-sm text-gray-500">Unduh laporan periode tertentu atau backup lengkap sistem.</p>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <div>
                <label class="text-xs font-semibold uppercase tracking-wide text-gray-400">Tanggal Mulai</label>
                <input type="date" wire:model="startDate" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
            <div>
                <label class="text-xs font-semibold uppercase tracking-wide text-gray-400">Tanggal Selesai</label>
                <input type="date" wire:model="endDate" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <button wire:click="downloadExcel" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h16"/><path d="M12 4v16"/><path d="m6 16 6 6 6-6"/></svg>
                Export Excel (CSV)
            </button>
            <button wire:click="downloadPdf" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-red-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M12 4h9"/><path d="M5 4h3v16H5z"/></svg>
                Export PDF
            </button>
        </div>
    </section>

    <section class="rounded-2xl border border-gray-100 bg-white px-6 py-5 shadow">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Backup Lengkap Sistem</h2>
                <p class="text-sm text-gray-500">Snapshot seluruh data (user, kategori, transaksi, log) untuk kebutuhan restore total.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button wire:click="downloadFullExcel" class="inline-flex items-center gap-2 rounded-xl bg-indigo-300 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16v16H4z"/>
                        <path d="m8 8 3 3-3 3"/>
                        <path d="m13 8 3 3-3 3"/>
                    </svg>
                    Excel (.xlsx)
                </button>
                <button wire:click="downloadSqlBackup" class="inline-flex items-center gap-2 rounded-xl bg-slate-700 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16v16H4z"/>
                        <path d="M8 9h8"/>
                        <path d="M8 12h8"/>
                        <path d="M8 15h8"/>
                    </svg>
                    SQL Dump
                </button>
            </div>
        </div>
    </section>

    <div class="mt-10 rounded-2xl border border-gray-100 bg-gray-50 p-6">
        <h2 class="text-lg font-semibold text-gray-900">Restore Data</h2>
        <p class="text-sm text-gray-500">Unggah file backup (.xlsx hasil export atau .sql dump) untuk mengembalikan seluruh data sistem.</p>

        <div class="mt-4 space-y-3">
            <input
                type="file"
                wire:model="restoreFile"
                accept=".xlsx,.sql"
                class="w-full rounded-xl border border-dashed border-gray-300 bg-white px-4 py-3 text-sm text-gray-700 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
            >
            @error('restoreFile')
                <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-500">Catatan: gunakan file yang dihasilkan dari fitur backup lengkap. Ukuran file maksimal 15MB.</p>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                wire:click="restoreData"
                wire:loading.attr="disabled"
                wire:target="restoreData"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12a9 9 0 1 1 9 9" />
                    <path d="M12 7v5l3 3" />
                </svg>
                <span wire:loading.remove wire:target="restoreData">Restore Data</span>
                <span wire:loading wire:target="restoreData">Memproses...</span>
            </button>
            @if ($restoreStatus)
                <span class="text-sm font-medium text-emerald-600">{{ $restoreStatus }}</span>
            @endif
            @if ($restoreError)
                <span class="text-sm font-medium text-red-600">{{ $restoreError }}</span>
            @endif
        </div>
    </div>
</div>
