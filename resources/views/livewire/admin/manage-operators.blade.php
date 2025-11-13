<div class="space-y-6">
    <div class="rounded-2xl bg-white p-6 shadow">
        <h1 class="text-2xl font-semibold text-gray-900">Manajemen Operator</h1>
        <p class="text-sm text-gray-500">Admin desa dapat menambah atau menghapus operator lapangan.</p>

        <form wire:submit.prevent="createOperator" class="mt-6 space-y-4">
            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif
            <div class="grid gap-4 md:grid-cols-3">
                <div class="md:col-span-1">
                    <label class="text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" wire:model="name" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-1">
                    <label class="text-sm font-medium text-gray-700">Email</label>
                    <input type="email" wire:model="email" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-1">
                    <label class="text-sm font-medium text-gray-700">Password</label>
                    <input type="password" wire:model="password" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                    Tambah Operator
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Daftar Operator</h2>
            <p class="text-sm text-gray-500">{{ $operators->count() }} operator aktif</p>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($operators as $operator)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $operator->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $operator->email }}</td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="removeOperator({{ $operator->id }})" class="text-xs font-semibold text-red-600 hover:text-red-700">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-gray-500">Belum ada operator.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
