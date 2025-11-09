<div wire:keydown.escape.window="close">
    @if ($open)
        <div class="fixed inset-0 z-50 flex items-start justify-center bg-slate-900/40 px-4 py-10 backdrop-blur-sm">
            <div class="absolute inset-0" wire:click="close"></div>
            <div class="relative z-10 w-full max-w-2xl rounded-3xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ $isEditing ? 'Edit Kategori' : 'Kelola Kategori Kas' }}
                        </h2>
                        <p class="text-sm text-gray-500">Pengelompokan yang rapi memudahkan analisis laporan.</p>
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

                <div class="space-y-6 px-6 py-6">
                    <form wire:submit.prevent="save" class="space-y-4 rounded-2xl border border-gray-100 bg-gray-50 px-4 py-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <div class="md:col-span-2">
                                <label class="text-sm font-medium text-gray-600">Nama Kategori</label>
                                <input
                                    type="text"
                                    wire:model.live="name"
                                    placeholder="Contoh: Iuran Warga, Belanja ATK"
                                    class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                >
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Jenis</label>
                                <select
                                    wire:model.live="type"
                                    class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-800 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                >
                                    <option value="pemasukan">Pemasukan</option>
                                    <option value="pengeluaran">Pengeluaran</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3">
                            <button
                                type="button"
                                class="rounded-full border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 transition-colors"
                                wire:click="close"
                            >
                                Tutup
                            </button>
                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700 transition-colors disabled:opacity-70"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove wire:target="save">
                                    {{ $isEditing ? 'Simpan Perubahan' : 'Tambah Kategori' }}
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

                    <div class="rounded-2xl border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <tr>
                                    <th class="px-4 py-3 text-left">Nama</th>
                                    <th class="px-4 py-3 text-left">Jenis</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @forelse ($categories as $category)
                                    <tr class="hover:bg-gray-50/80">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $category->name }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold {{ $category->type === 'pemasukan' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ ucfirst($category->type) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="inline-flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:border-blue-500 hover:text-blue-600 transition-colors"
                                                    wire:click="edit({{ $category->id }})"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M12 20h9" />
                                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-red-100 text-red-500 hover:bg-red-50 hover:text-red-600 transition-colors"
                                                    wire:click="delete({{ $category->id }})"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M3 6h18" />
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                                        <path d="M10 11v6" />
                                                        <path d="M14 11v6" />
                                                        <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500">
                                            Belum ada kategori terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
