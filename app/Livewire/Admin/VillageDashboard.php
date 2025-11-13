<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class VillageDashboard extends Component
{
    public function render()
    {
        $transactions = Transaction::whereBetween('transaction_date', [
            now()->startOfYear(),
            now()->endOfYear(),
        ])->get();

        $totalIncome = $transactions->where('type', 'pemasukan')->sum('amount');
        $totalExpense = $transactions->where('type', 'pengeluaran')->sum('amount');
        $transactionCount = $transactions->count();
        $currentMonthIncome = $transactions
            ->where('type', 'pemasukan')
            ->whereBetween('transaction_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');
        $currentMonthExpense = $transactions
            ->where('type', 'pengeluaran')
            ->whereBetween('transaction_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        $monthly = collect(range(0, 5))->map(function ($index) use ($transactions) {
            $month = now()->copy()->subMonths(5 - $index);
            return [
                'label' => $month->format('M'),
                'income' => $transactions
                    ->where('type', 'pemasukan')
                    ->whereBetween('transaction_date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                    ->sum('amount'),
                'expense' => $transactions
                    ->where('type', 'pengeluaran')
                    ->whereBetween('transaction_date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                    ->sum('amount'),
            ];
        });

        $categories = $transactions
            ->groupBy('category_id')
            ->map(fn ($items) => [
                'name' => optional($items->first()->category)->name ?? 'Tanpa kategori',
                'total' => $items->sum('amount'),
            ])
            ->values();
        $topCategories = $categories->sortByDesc('total')->take(5)->values();

        $recentLogs = ActivityLog::with('user')
            ->whereIn('action', ['create_transaction', 'update_transaction', 'delete_transaction'])
            ->latest()
            ->limit(5)
            ->get();

        $operators = User::where('role', 'operator')->count();
        $latestTransactions = Transaction::with('category')
            ->latest('transaction_date')
            ->limit(5)
            ->get();

        return view('livewire.admin.village-dashboard', [
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netCash' => $totalIncome - $totalExpense,
            'operatorCount' => $operators,
            'transactionCount' => $transactionCount,
            'currentMonthIncome' => $currentMonthIncome,
            'currentMonthExpense' => $currentMonthExpense,
            'monthly' => $monthly,
            'categories' => $categories,
            'topCategories' => $topCategories,
            'recentLogs' => $recentLogs,
            'latestTransactions' => $latestTransactions,
        ]);
    }
}
