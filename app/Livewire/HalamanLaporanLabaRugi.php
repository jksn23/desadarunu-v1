<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class HalamanLaporanLabaRugi extends Component
{
    public string $startDate;
    public string $endDate;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedStartDate(): void
    {
        if ($this->startDate > $this->endDate) {
            $this->endDate = $this->startDate;
        }
    }

    public function updatedEndDate(): void
    {
        if ($this->endDate < $this->startDate) {
            $this->startDate = $this->endDate;
        }
    }

    public function render()
    {
        $transactions = Transaction::with('category')
            ->where('user_id', auth()->id())
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
            ->get();

        $revenues = $transactions->where('type', 'pemasukan')->sum('amount');
        $expenses = $transactions->where('type', 'pengeluaran')->sum('amount');
        $netIncome = $revenues - $expenses;

        $topRevenues = $this->topCategories($transactions, 'pemasukan');
        $topExpenses = $this->topCategories($transactions, 'pengeluaran');
        $periodSummary = $this->periodSummary($transactions);

        return view('livewire.halaman-laporan-laba-rugi', [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'netIncome' => $netIncome,
            'topRevenues' => $topRevenues,
            'topExpenses' => $topExpenses,
            'periodSummary' => $periodSummary,
        ]);
    }

    private function topCategories(Collection $transactions, string $type): Collection
    {
        return $transactions
            ->where('type', $type)
            ->groupBy('category_id')
            ->map(function (Collection $items) {
                $category = $items->first()->category;

                return [
                    'name' => $category?->name ?? 'Tanpa kategori',
                    'total' => $items->sum('amount'),
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->take(5);
    }

    private function periodSummary(Collection $transactions): Collection
    {
        return $transactions
            ->groupBy(fn ($item) => optional($item->transaction_date)?->format('Y-m'))
            ->map(function (Collection $items, ?string $period) {
                $label = $period
                    ? \Illuminate\Support\Carbon::createFromFormat('Y-m', $period)->translatedFormat('F Y')
                    : 'Tidak diketahui';

                $revenues = $items->where('type', 'pemasukan')->sum('amount');
                $expenses = $items->where('type', 'pengeluaran')->sum('amount');

                return [
                    'label' => $label,
                    'revenues' => $revenues,
                    'expenses' => $expenses,
                    'net' => $revenues - $expenses,
                ];
            })
            ->sortKeys()
            ->values();
    }
}
