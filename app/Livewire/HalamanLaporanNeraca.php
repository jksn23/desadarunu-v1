<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Services\ReportExportService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class HalamanLaporanNeraca extends Component
{
    public string $asOfDate;

    public function mount(): void
    {
        $this->asOfDate = now()->format('Y-m-d');
    }

    public function render()
    {
        $transactions = Transaction::whereDate('transaction_date', '<=', $this->asOfDate)
            ->get();

        $totalIncome = $transactions->where('type', 'pemasukan')->sum('amount');
        $totalExpense = $transactions->where('type', 'pengeluaran')->sum('amount');
        $cashBalance = $totalIncome - $totalExpense;

        $assets = [
            [
                'name' => 'Kas dan Setara Kas',
                'amount' => $cashBalance,
                'description' => 'Saldo kas desa hingga tanggal laporan.',
            ],
        ];

        $liabilities = [
            [
                'name' => 'Kewajiban Jangka Pendek',
                'amount' => 0,
                'description' => 'Belum ada kewajiban yang tercatat.',
            ],
        ];

        $equity = [
            [
                'name' => 'Saldo Anggaran',
                'amount' => $cashBalance,
                'description' => 'Surplus/defisit akumulasi dari transaksi yang tercatat.',
            ],
        ];

        return view('livewire.halaman-laporan-neraca', [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'cashBalance' => $cashBalance,
        ]);
    }

    public function downloadExcel(ReportExportService $exporter)
    {
        return $exporter->exportNeracaCsv([
            'end_date' => $this->asOfDate,
        ]);
    }

    public function downloadPdf(ReportExportService $exporter)
    {
        return $exporter->exportNeracaPdf([
            'end_date' => $this->asOfDate,
        ]);
    }
}
