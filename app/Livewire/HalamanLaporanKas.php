<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Services\OpenAIService;
use App\Services\ReportExportService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class HalamanLaporanKas extends Component
{
    public string $startDate;
    public string $endDate;
    public ?string $aiSummary = null;
    public ?string $aiError = null;
    public bool $aiProcessing = false;

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

    public function downloadExcel(ReportExportService $exporter)
    {
        return $exporter->exportArusKasCsv([
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }

    public function downloadPdf(ReportExportService $exporter)
    {
        return $exporter->exportArusKasPdf([
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }

    public function updatedEndDate(): void
    {
        if ($this->endDate < $this->startDate) {
            $this->startDate = $this->endDate;
        }
    }

    public function render()
    {
        $report = $this->calculateReport();

        return view('livewire.halaman-laporan-kas', [
            'incomeTotal' => $report['incomeTotal'],
            'expenseTotal' => $report['expenseTotal'],
            'netCashFlow' => $report['netCashFlow'],
            'incomeByCategory' => $report['incomeByCategory'],
            'expenseByCategory' => $report['expenseByCategory'],
            'dailyTrend' => $report['dailyTrend'],
            'aiSummary' => $this->aiSummary,
            'aiError' => $this->aiError,
        ]);
    }

    public function generateAiSummary(OpenAIService $openai): void
    {
        $this->aiProcessing = true;
        $this->aiError = null;

        $report = $this->calculateReport();

        try {
            $prompt = sprintf(
                "Anda adalah analis keuangan desa. Buat ringkasan 2-3 kalimat untuk laporan arus kas berikut.\nPeriode: %s s/d %s.\nTotal pemasukan: Rp %s.\nTotal pengeluaran: Rp %s.\nNet cash flow: Rp %s.\nSoroti kondisi kas dan rekomendasi singkat.",
                Carbon::parse($this->startDate)->translatedFormat('d F Y'),
                Carbon::parse($this->endDate)->translatedFormat('d F Y'),
                number_format($report['incomeTotal'], 0, ',', '.'),
                number_format($report['expenseTotal'], 0, ',', '.'),
                number_format($report['netCashFlow'], 0, ',', '.'),
            );

            $summary = $openai->summarizeReport($prompt);

            if (! $summary) {
                $this->aiError = 'AI belum bisa menyusun ringkasan. Coba lagi nanti.';
                $this->aiSummary = null;

                return;
            }

            $this->aiSummary = trim($summary);
        } finally {
            $this->aiProcessing = false;
        }
    }

    private function calculateReport(): array
    {
        $transactions = Transaction::with('category')
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
            ->get();

        $incomeTotal = $transactions->where('type', 'pemasukan')->sum('amount');
        $expenseTotal = $transactions->where('type', 'pengeluaran')->sum('amount');
        $netCashFlow = $incomeTotal - $expenseTotal;

        $incomeByCategory = $this->groupByCategory($transactions, 'pemasukan');
        $expenseByCategory = $this->groupByCategory($transactions, 'pengeluaran');
        $dailyTrend = $this->dailyTrend($transactions);

        return compact(
            'incomeTotal',
            'expenseTotal',
            'netCashFlow',
            'incomeByCategory',
            'expenseByCategory',
            'dailyTrend',
        );
    }

    private function groupByCategory(Collection $transactions, string $type): Collection
    {
        return $transactions
            ->where('type', $type)
            ->groupBy('category_id')
            ->map(function (Collection $items) use ($type) {
                $category = $items->first()->category;

                return [
                    'name' => $category?->name ?? 'Tanpa kategori',
                    'type' => $type,
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();
    }

    private function dailyTrend(Collection $transactions): Collection
    {
        return $transactions
            ->groupBy(fn ($item) => $item->transaction_date?->format('Y-m-d'))
            ->map(function (Collection $items, ?string $date) {
                $carbonDate = $date ? Carbon::parse($date) : null;

                return [
                    'date' => $carbonDate?->format('d M Y') ?? 'Tidak diketahui',
                    'pemasukan' => $items->where('type', 'pemasukan')->sum('amount'),
                    'pengeluaran' => $items->where('type', 'pengeluaran')->sum('amount'),
                ];
            })
            ->sortBy(fn ($item, $key) => $key)
            ->values();
    }
}
