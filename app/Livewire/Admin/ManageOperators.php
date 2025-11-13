<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ManageOperators extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';

    public function render()
    {
        $operators = User::where('role', 'operator')
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.admin.manage-operators', [
            'operators' => $operators,
        ]);
    }

    public function createOperator(ActivityLogger $logger): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $operator = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'operator',
            'email_verified_at' => now(),
        ]);

        $logger->log([
            'action' => 'create_operator',
            'module' => 'users',
            'description' => "Admin desa membuat operator {$operator->name}",
            'metadata' => ['user_id' => $operator->id],
        ]);

        $this->reset(['name', 'email', 'password']);
        session()->flash('status', 'Operator baru berhasil dibuat.');
    }

    public function removeOperator(ActivityLogger $logger, int $userId): void
    {
        $operator = User::where('role', 'operator')->findOrFail($userId);
        $operator->delete();

        $logger->log([
            'action' => 'delete_operator',
            'module' => 'users',
            'description' => "Admin desa menghapus operator {$operator->name}",
            'metadata' => ['user_id' => $userId],
        ]);

        session()->flash('status', 'Operator berhasil dihapus.');
    }
}
