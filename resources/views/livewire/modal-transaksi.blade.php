<div wire:keydown.escape.window="close">
    @if ($open)
        <div class="fixed inset-0 z-50 flex items-start justify-center bg-slate-900/50 px-4 py-8 backdrop-blur-sm">
            <div
                class="absolute inset-0"
                wire:click="close"
            ></div>
            <div
                class="relative z-10 w-full max-w-xl rounded-3xl bg-white shadow-2xl"
                wire:transition
            >
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ $isEditing ? 'Edit Transaksi' : 'Catat Transaksi Baru' }}
                        </h2>
                        <p class="text-sm text-gray-500">
                            Pastikan data sudah sesuai sebelum disimpan.
                        </p>
                    </div>
                    <button
                        type="button"
                        class="rounded-full p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600"
                        wire:click="close"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-6 px-6 py-6">
                    <div class="flex gap-3 rounded-2xl bg-gray-50 p-3">
                        <label class="flex-1 cursor-pointer">
                            <input
                                type="radio"
                                class="peer hidden"
                                value="pemasukan"
                                wire:model.live="type"
                            >
                            <div class="flex items-center justify-between rounded-xl border border-transparent bg-white px-4 py-3 transition-all peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700">
                                <div>
                                    <p class="text-sm font-semibold">Pemasukan</p>
                                    <p class="text-xs text-gray-500">Iuran, retribusi, bantuan.</p>
                                </div>
                                <span class="rounded-full bg-green-500/10 px-3 py-1 text-xs font-semibold text-green-600">+</span>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input
                                type="radio"
                                class="peer hidden"
                                value="pengeluaran"
                                wire:model.live="type"
                            >
                            <div class="flex items-center justify-between rounded-xl border border-transparent bg-white px-4 py-3 transition-all peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700">
                                <div>
                                    <p class="text-sm font-semibold">Pengeluaran</p>
                                    <p class="text-xs text-gray-500">Operasional, belanja modal.</p>
                                </div>
                                <span class="rounded-full bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-600">-</span>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <p class="text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Tanggal Transaksi</label>
                            <input
                                type="date"
                                wire:model.live="transaction_date"
                                class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >
                            @error('transaction_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Jumlah</label>
                            <div class="mt-1 flex items-center rounded-xl border border-gray-200 px-3 focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-100">
                                <span class="text-sm font-semibold text-gray-400">Rp</span>
                                <input
                                    type="text"
                                    wire:model.live="amount"
                                    placeholder="0,00"
                                    inputmode="decimal"
                                    class="w-full bg-transparent px-2 py-2 text-right text-lg font-semibold text-gray-900 focus:outline-none"
                                >
                            </div>
                            @error('amount')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Kategori</label>
                        @if ($categories->isEmpty())
                            <div class="mt-2 rounded-xl border border-dashed border-blue-300 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                                Belum ada kategori. <button type="button" class="font-semibold underline" onclick="window.dispatchEvent(new CustomEvent('open-kategori-modal'))">Tambah kategori sekarang.</button>
                            </div>
                        @else
                            <select
                                wire:model.live="category_id"
                                class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >
                                <option value="">Pilih kategori...</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ ucfirst($category->type) }} • {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        @endif
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-600">Deskripsi</label>
                        <textarea
                            rows="3"
                            wire:model.live="description"
                            placeholder="Contoh: Pembayaran listrik kantor desa bulan Oktober."
                            class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        ></textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-2xl bg-gray-50 px-4 py-3 text-xs text-gray-500">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p>Gunakan deskripsi yang jelas agar mudah ditinjau perangkat desa.</p>
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-full border border-blue-600 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-wide text-blue-600 hover:bg-blue-50"
                                wire:click="suggestCategoryWithAi"
                                wire:loading.attr="disabled"
                                wire:target="suggestCategoryWithAi"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m12 2 7 4v8c0 5-3 7-7 8-4-1-7-3-7-8V6z" />
                                </svg>
                                <span wire:loading.remove wire:target="suggestCategoryWithAi">Sarankan AI</span>
                                <span wire:loading wire:target="suggestCategoryWithAi">Menganalisis...</span>
                            </button>
                        </div>
                        @if ($aiMessage)
                            <div class="mt-3 flex items-start gap-2 text-[11px] {{ $aiError ? 'text-red-600' : 'text-blue-600' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    @if ($aiError)
                                        <path d="M12 9v4" />
                                        <path d="M12 17h.01" />
                                        <path d="m10 3-7 7 9 11 9-11-7-7" />
                                    @else
                                        <path d="M20 6 9 17l-5-5" />
                                    @endif
                                </svg>
                                <span>{{ $aiMessage }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                        <button
                            type="button"
                            class="rounded-full border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors"
                            wire:click="close"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 transition-colors disabled:opacity-70"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="save">
                                {{ $isEditing ? 'Simpan Perubahan' : 'Simpan Transaksi' }}
                            </span>
                            <span class="inline-flex items-center gap-2" wire:loading wire:target="save">
                                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4l5-5-5-5v4a12 12 0 0 0-12 12h4z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
