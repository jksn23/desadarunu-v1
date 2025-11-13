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
    <h1>Laporan Arus Kas Desa Darunu</h1>
    <p>Periode: {{ $filters['start_date'] ?? '-' }} s/d {{ $filters['end_date'] ?? '-' }}</p>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Kategori</th>
                <th>Deskripsi</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ optional($transaction->transaction_date)->format('d-m-Y') }}</td>
                    <td>{{ ucfirst($transaction->type) }}</td>
                    <td>{{ $transaction->category->name ?? '-' }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
