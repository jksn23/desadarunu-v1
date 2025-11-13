<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ActivityLogs extends Component
{
    use WithPagination;

    public string $role = 'all';
    public string $action = 'all';
    public string $module = 'all';
    public ?string $startDate = null;
    public ?string $endDate = null;

    protected $queryString = [
        'role' => ['except' => 'all'],
        'action' => ['except' => 'all'],
        'module' => ['except' => 'all'],
    ];

    public function mount(): void
    {
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function updating($name): void
    {
        if (in_array($name, ['role', 'action', 'module', 'startDate', 'endDate'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $logs = ActivityLog::with('user')
            ->when($this->role !== 'all', fn ($query) => $query->where('role', $this->role))
            ->when($this->action !== 'all', fn ($query) => $query->where('action', $this->action))
            ->when($this->module !== 'all', fn ($query) => $query->where('module', $this->module))
            ->when($this->startDate, fn ($query) => $query->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn ($query) => $query->whereDate('created_at', '<=', $this->endDate))
            ->latest()
            ->paginate(20);

        $availableRoles = ActivityLog::select('role')->distinct()->pluck('role');
        $availableModules = ActivityLog::select('module')->distinct()->pluck('module')->filter();
        $availableActions = ActivityLog::select('action')->distinct()->pluck('action');

        return view('livewire.admin.activity-logs', [
            'logs' => $logs,
            'availableRoles' => $availableRoles,
            'availableModules' => $availableModules,
            'availableActions' => $availableActions,
        ]);
    }
}
