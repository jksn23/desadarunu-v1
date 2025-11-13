<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Transaction;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    public function exportArusKasCsv(array $filters): StreamedResponse
    {
        $filename = 'laporan-arus-kas-'.now()->format('Ymd_His').'.csv';
        $transactions = $this->transactionQuery($filters)->with('category')->get();

        $headers = ['Tanggal', 'Jenis', 'Kategori', 'Deskripsi', 'Jumlah'];
        $rows = $transactions->map(fn ($transaction) => [
            optional($transaction->transaction_date)->format('d-m-Y'),
            $transaction->type,
            $transaction->category?->name ?? '-',
            $transaction->description,
            $transaction->amount,
        ]);

        return $this->streamCsv($headers, $rows, $filename);
    }

    public function exportArusKasPdf(array $filters): StreamedResponse
    {
        $transactions = $this->transactionQuery($filters)->with('category')->get();
        $html = View::make('exports.arus-kas', [
            'transactions' => $transactions,
            'filters' => $filters,
        ])->render();

        $dompdf = new Dompdf(new Options([
            'isRemoteEnabled' => true,
        ]));

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'laporan-arus-kas-'.now()->format('Ymd_His').'.pdf';

        return $this->streamPdf($dompdf, $filename);
    }

    public function exportLabaRugiCsv(array $filters): StreamedResponse
    {
        $transactions = $this->transactionQuery($filters)->with('category')->get();
        $headers = ['Periode', 'Pendapatan', 'Beban', 'Laba Bersih'];

        $summary = $transactions
            ->groupBy(fn ($item) => optional($item->transaction_date)?->format('Y-m'))
            ->map(function ($items, $period) {
                $label = $period
                    ? \Illuminate\Support\Carbon::createFromFormat('Y-m', $period)->format('F Y')
                    : 'Tidak diketahui';

                $revenues = $items->where('type', 'pemasukan')->sum('amount');
                $expenses = $items->where('type', 'pengeluaran')->sum('amount');

                return [
                    $label,
                    $revenues,
                    $expenses,
                    $revenues - $expenses,
                ];
            })->values();

        return $this->streamCsv($headers, $summary, 'laporan-laba-rugi-'.now()->format('Ymd_His').'.csv');
    }

    public function exportLabaRugiPdf(array $filters): StreamedResponse
    {
        $transactions = $this->transactionQuery($filters)->with('category')->get();
        $html = View::make('exports.laba-rugi', [
            'transactions' => $transactions,
            'filters' => $filters,
        ])->render();

        $dompdf = new Dompdf(new Options(['isRemoteEnabled' => true]));
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->streamPdf($dompdf, 'laporan-laba-rugi-'.now()->format('Ymd_His').'.pdf');
    }

    public function exportNeracaCsv(array $filters): StreamedResponse
    {
        $transactions = Transaction::whereDate('transaction_date', '<=', $filters['end_date'] ?? now())->get();
        $balance = $transactions->where('type', 'pemasukan')->sum('amount')
            - $transactions->where('type', 'pengeluaran')->sum('amount');

        $headers = ['Kas dan Setara Kas', 'Nilai'];
        $rows = [
            ['Saldo per '.($filters['end_date'] ?? now()->format('Y-m-d')), $balance],
        ];

        return $this->streamCsv($headers, $rows, 'laporan-neraca-'.now()->format('Ymd_His').'.csv');
    }

    public function exportNeracaPdf(array $filters): StreamedResponse
    {
        $transactions = Transaction::whereDate('transaction_date', '<=', $filters['end_date'] ?? now())->get();

        $html = View::make('exports.neraca', [
            'transactions' => $transactions,
            'filters' => $filters,
        ])->render();

        $dompdf = new Dompdf(new Options(['isRemoteEnabled' => true]));
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->streamPdf($dompdf, 'laporan-neraca-'.now()->format('Ymd_His').'.pdf');
    }

    public function exportCsv(array $filters): StreamedResponse
    {
        $transactions = $this->transactionQuery($filters)->with('category')->get();
        $logs = $this->logQuery($filters)->with('user')->get();

        $transactionHeader = ['Tanggal', 'Jenis', 'Kategori', 'Deskripsi', 'Jumlah'];
        $logHeader = ['Waktu', 'Pengguna', 'Role', 'Aksi', 'Modul', 'Deskripsi'];
        $filename = 'backup-data-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($transactions, $logs, $transactionHeader, $logHeader) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Transaksi']);
            fputcsv($handle, $transactionHeader);
            foreach ($transactions as $transaction) {
                fputcsv($handle, [
                    optional($transaction->transaction_date)->format('d-m-Y'),
                    $transaction->type,
                    $transaction->category?->name ?? '-',
                    $transaction->description,
                    $transaction->amount,
                ]);
            }

            fputcsv($handle, []); // separator row
            fputcsv($handle, ['Log Aktivitas']);
            fputcsv($handle, $logHeader);
            foreach ($logs as $log) {
                fputcsv($handle, [
                    optional($log->created_at)?->format('d-m-Y H:i'),
                    $log->user->name ?? 'System',
                    $log->role,
                    $log->action,
                    $log->module,
                    $log->description,
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportPdf(array $filters): StreamedResponse
    {
        $transactions = $this->transactionQuery($filters)->with('category')->get();
        $logs = $this->logQuery($filters)->with('user')->limit(200)->get();

        $html = View::make('exports.report', [
            'transactions' => $transactions,
            'logs' => $logs,
            'filters' => $filters,
        ])->render();

        $dompdf = new Dompdf(new Options(['isRemoteEnabled' => true]));
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->streamPdf($dompdf, 'backup-data-'.now()->format('Ymd_His').'.pdf');
    }

    private function transactionQuery(array $filters): Builder
    {
        return Transaction::query()
            ->when($filters['start_date'] ?? null, fn ($query, $date) => $query->whereDate('transaction_date', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($query, $date) => $query->whereDate('transaction_date', '<=', $date));
    }

    private function logQuery(array $filters): Builder
    {
        return ActivityLog::query()
            ->latest()
            ->when($filters['start_date'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['end_date'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
    }

    private function streamCsv(array $headers, $rows, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function streamPdf(Dompdf $dompdf, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($dompdf) {
            echo $dompdf->output();
        }, $filename, ['Content-Type' => 'application/pdf']);
    }
}
