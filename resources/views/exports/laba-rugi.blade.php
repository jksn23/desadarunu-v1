<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        h2 { font-size: 14px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; }
        th { background: #f3f4f6; text-align: left; }
    </style>
</head>
<body>
    <h1>Laporan Laba Rugi</h1>
    <p>Periode: {{ $filters['start_date'] ?? '-' }} s/d {{ $filters['end_date'] ?? '-' }}</p>

    @php
        $revenues = $transactions->where('type', 'pemasukan')->sum('amount');
        $expenses = $transactions->where('type', 'pengeluaran')->sum('amount');
        $net = $revenues - $expenses;
    @endphp

    <table>
        <tbody>
            <tr>
                <th>Total Pendapatan</th>
                <td>Rp {{ number_format($revenues, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Total Beban</th>
                <td>Rp {{ number_format($expenses, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Laba Bersih</th>
                <td>Rp {{ number_format($net, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Ringkasan Periode</h2>
    <table>
        <thead>
            <tr>
                <th>Periode</th>
                <th>Pendapatan</th>
                <th>Beban</th>
                <th>Laba Bersih</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions->groupBy(fn ($item) => optional($item->transaction_date)?->format('Y-m')) as $period => $items)
                @php
                    $label = $period ? \Illuminate\Support\Carbon::createFromFormat('Y-m', $period)->format('F Y') : 'Tidak diketahui';
                    $rev = $items->where('type', 'pemasukan')->sum('amount');
                    $exp = $items->where('type', 'pengeluaran')->sum('amount');
                @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td>Rp {{ number_format($rev, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($exp, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($rev - $exp, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
