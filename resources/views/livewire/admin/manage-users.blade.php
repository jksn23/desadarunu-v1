<div class="space-y-6">
    <div class="rounded-2xl bg-white p-6 shadow">
        <h1 class="text-2xl font-semibold text-gray-900">Manajemen Pengguna</h1>
        <p class="text-sm text-gray-500">Buat akun admin desa atau operator dari panel ini.</p>

        <form wire:submit.prevent="createUser" class="mt-6 space-y-4">
            @if (session('status'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <input type="text" wire:model="name" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Email</label>
                    <input type="email" wire:model="email" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700">Password</label>
                    <input type="password" wire:model="password" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Role</label>
                    <select wire:model="role" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        <option value="admin_desa">Admin Desa</option>
                        <option value="operator">Operator</option>
                    </select>
                    @error('role') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">Daftar Pengguna</h2>
            <p class="text-sm text-gray-500">{{ $users->count() }} pengguna aktif</p>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $user)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-600">
                                    {{ str_replace('_', ' ', ucfirst($user->role)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="deleteUser({{ $user->id }})" class="text-red-600 hover:text-red-700 text-xs font-semibold">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
