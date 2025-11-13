<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;
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

        $monthlyTransactions = collect(range(0, 5))->map(function ($index) {
            $month = Carbon::now()->copy()->subMonths(5 - $index);

            return [
                'label' => $month->format('M'),
                'total' => Transaction::whereBetween('transaction_date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                    ->count(),
            ];
        });

        $activityByRole = ActivityLog::select('role')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role')
            ->toArray();

        return view('livewire.admin.site-dashboard', [
            'adminCount' => $adminCount,
            'operatorCount' => $operatorCount,
            'transactionCount' => $transactionCount,
            'logCount' => $logCount,
            'recentAdmins' => $recentAdmins,
            'monthlyTransactions' => $monthlyTransactions,
            'activityByRole' => $activityByRole,
        ]);
    }
}
