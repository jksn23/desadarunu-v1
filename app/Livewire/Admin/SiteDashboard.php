<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SiteDashboard extends Component
{
    public function render()
    {
        $adminCount = User::where('role', 'admin_desa')->count();
        $operatorCount = User::where('role', 'operator')->count();
        $transactionCount = Transaction::count();
        $logCount = ActivityLog::count();

        $recentAdmins = User::whereIn('role', ['admin_desa', 'operator'])
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.admin.site-dashboard', [
            'adminCount' => $adminCount,
            'operatorCount' => $operatorCount,
            'transactionCount' => $transactionCount,
            'logCount' => $logCount,
            'recentAdmins' => $recentAdmins,
        ]);
    }
}
