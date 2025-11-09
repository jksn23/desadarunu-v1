<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class HalamanTransaksi extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url(as: 'type')]
    public string $typeFilter = 'semua';

    #[Url(as: 'period')]
    public string $periodFilter = 'this_month';

    protected $listeners = [
        'transaksi-disimpan' => '$refresh',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPeriodFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $userId = auth()->id();

        $baseQuery = Transaction::with('category')
            ->where('user_id', $userId);

        $transactionsQuery = clone $baseQuery;
        $this->applyFilters($transactionsQuery);

        $transactions = $transactionsQuery
            ->latest('transaction_date')
            ->paginate(10);

        $summaryQuery = clone $baseQuery;
        $this->applyDateFilter($summaryQuery);

        $totalIncome = (clone $summaryQuery)
            ->where('type', 'pemasukan')
            ->sum('amount');

        $totalExpense = (clone $summaryQuery)
            ->where('type', 'pengeluaran')
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        $latestTransactions = (clone $baseQuery)
            ->latest('transaction_date')
            ->limit(5)
            ->get();

        return view('livewire.halaman-transaksi', [
            'transactions' => $transactions,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'balance' => $balance,
            'latestTransactions' => $latestTransactions,
            'dateRangeLabel' => $this->dateRangeLabel(),
        ]);
    }

    private function applyFilters(Builder $query): void
    {
        $this->applyDateFilter($query);

        if ($this->typeFilter !== 'semua') {
            $query->where('type', $this->typeFilter);
        }

        if (filled($this->search)) {
            $query->where(function (Builder $builder) {
                $builder->where('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('category', function (Builder $categoryQuery) {
                        $categoryQuery->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }
    }

    private function applyDateFilter(Builder $query): void
    {
        [$start, $end] = $this->resolveDateRange();

        if ($start && $end) {
            $query->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()]);
        }
    }

    private function resolveDateRange(): array
    {
        $today = Carbon::today();

        return match ($this->periodFilter) {
            'today' => [$today, $today],
            'this_week' => [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()],
            'last_week' => [
                $today->copy()->subWeek()->startOfWeek(),
                $today->copy()->subWeek()->endOfWeek(),
            ],
            'this_month' => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
            'last_month' => [
                $today->copy()->subMonth()->startOfMonth(),
                $today->copy()->subMonth()->endOfMonth(),
            ],
            default => [null, null],
        };
    }

    private function dateRangeLabel(): string
    {
        return match ($this->periodFilter) {
            'today' => 'Hari Ini',
            'this_week' => 'Minggu Ini',
            'last_week' => 'Minggu Lalu',
            'this_month' => 'Bulan Ini',
            'last_month' => 'Bulan Lalu',
            default => 'Semua Waktu',
        };
    }
}
