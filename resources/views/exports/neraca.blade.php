<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; }
        th { background: #f3f4f6; text-align: left; }
    </style>
</head>
<body>
    <h1>Laporan Neraca</h1>
    <p>Per {{ $filters['end_date'] ?? now()->format('Y-m-d') }}</p>

    @php
        $totalIncome = $transactions->where('type', 'pemasukan')->sum('amount');
        $totalExpense = $transactions->where('type', 'pengeluaran')->sum('amount');
        $cashBalance = $totalIncome - $totalExpense;
    @endphp

    <table>
        <thead>
            <tr>
                <th>Akun</th>
                <th>Nilai</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Kas dan Setara Kas</td>
                <td>Rp {{ number_format($cashBalance, 0, ',', '.') }}</td>
                <td>Saldo kas desa hingga tanggal laporan.</td>
            </tr>
            <tr>
                <td>Kewajiban Jangka Pendek</td>
                <td>Rp 0</td>
                <td>Belum ada kewajiban tercatat.</td>
            </tr>
            <tr>
                <td>Saldo Anggaran</td>
                <td>Rp {{ number_format($cashBalance, 0, ',', '.') }}</td>
                <td>Surplus/defisit akumulasi.</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
