<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { font-size: 18px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #d1d5db; padding: 6px; }
        th { background: #f3f4f6; text-align: left; }
    </style>
</head>
<body>
    <h1>Laporan Kas Desa Darunu</h1>
    <p>Periode: {{ $filters['start_date'] ?? '-' }} s/d {{ $filters['end_date'] ?? '-' }}</p>

    <h2>Transaksi</h2>
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

    <h2>Log Aktivitas (50 terbaru)</h2>
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Pengguna</th>
                <th>Aksi</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
