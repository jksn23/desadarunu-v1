<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionDummySeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $operator = User::firstOrCreate(
                ['email' => 'operator@desadarunu.test'],
                [
                    'name' => 'Operator Desa',
                    'password' => bcrypt('Operator123!'),
                    'role' => 'operator',
                    'email_verified_at' => now(),
                ]
            );

            $categoryData = [
                ['name' => 'Iuran Warga', 'type' => 'pemasukan'],
                ['name' => 'Dana Hibah', 'type' => 'pemasukan'],
                ['name' => 'Belanja Operasional', 'type' => 'pengeluaran'],
                ['name' => 'Pemeliharaan Fasilitas', 'type' => 'pengeluaran'],
            ];

            $categories = collect($categoryData)->mapWithKeys(function ($item) use ($operator) {
                $category = Category::firstOrCreate(
                    [
                        'user_id' => $operator->id,
                        'name' => $item['name'],
                    ],
                    [
                        'type' => $item['type'],
                    ]
                );

                return [$item['name'] => $category->id];
            });

            Transaction::where('user_id', $operator->id)->delete();

            $start = now()->subMonths(6)->startOfMonth();
            $descriptionsIncome = [
                'Iuran warga RT %s bulan %s',
                'Dana hibah pemerintah tahap %s',
                'Sumbangan CSR perusahaan %s',
            ];
            $descriptionsExpense = [
                'Belanja operasional kantor desa %s',
                'Perawatan fasilitas umum %s',
                'Pembelian peralatan kegiatan %s',
            ];

            for ($i = 0; $i < 60; $i++) {
                $date = (clone $start)->addDays(rand(0, 180));
                $type = rand(0, 1) ? 'pemasukan' : 'pengeluaran';
                $categoryName = $type === 'pemasukan'
                    ? Arr::random(['Iuran Warga', 'Dana Hibah'])
                    : Arr::random(['Belanja Operasional', 'Pemeliharaan Fasilitas']);

                $description = $type === 'pemasukan'
                    ? sprintf(Arr::random($descriptionsIncome), rand(1, 12), $date->format('M Y'))
                    : sprintf(Arr::random($descriptionsExpense), strtoupper(Str::random(2)));

                Transaction::create([
                    'user_id' => $operator->id,
                    'category_id' => $categories[$categoryName],
                    'description' => $description,
                    'amount' => rand(200_000, 5_000_000),
                    'type' => $type,
                    'transaction_date' => $date->format('Y-m-d'),
                ]);
            }
        });
    }
}
