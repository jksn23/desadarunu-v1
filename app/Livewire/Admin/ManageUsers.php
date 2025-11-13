<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ManageUsers extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'admin_desa';

    public function render()
    {
        $users = User::query()
            ->whereIn('role', ['admin_desa', 'operator'])
            ->latest()
            ->get();

        return view('livewire.admin.manage-users', [
            'users' => $users,
        ]);
    }

    public function createUser(ActivityLogger $logger): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin_desa', 'operator'])],
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'email_verified_at' => now(),
        ]);

        $logger->log([
            'action' => 'create_user',
            'module' => 'users',
            'description' => "Membuat pengguna {$user->name}",
            'metadata' => [
                'user_id' => $user->id,
                'role' => $user->role,
            ],
        ]);

        $this->reset(['name', 'email', 'password', 'role']);
        $this->role = 'admin_desa';
        session()->flash('status', 'Pengguna baru berhasil dibuat.');
    }

    public function deleteUser(ActivityLogger $logger, int $userId): void
    {
        $user = User::whereIn('role', ['admin_desa', 'operator'])->findOrFail($userId);

        $user->delete();

        $logger->log([
            'action' => 'delete_user',
            'module' => 'users',
            'description' => "Menghapus pengguna {$user->name}",
            'metadata' => [
                'user_id' => $userId,
                'role' => $user->role,
            ],
        ]);

        session()->flash('status', 'Pengguna berhasil dihapus.');
    }
}
