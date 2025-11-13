<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DataBackupService
{
    /**
     * Tables included in the backup along with their column order.
     */
    private array $tables = [
        'users' => [
            'id',
            'name',
            'email',
            'email_verified_at',
            'password',
            'role',
            'remember_token',
            'created_at',
            'updated_at',
        ],
        'categories' => [
            'id',
            'user_id',
            'name',
            'type',
            'created_at',
            'updated_at',
        ],
        'transactions' => [
            'id',
            'user_id',
            'category_id',
            'description',
            'amount',
            'type',
            'transaction_date',
            'created_at',
            'updated_at',
        ],
        'activity_logs' => [
            'id',
            'user_id',
            'role',
            'action',
            'module',
            'description',
            'metadata',
            'ip_address',
            'user_agent',
            'created_at',
            'updated_at',
        ],
    ];

    public function exportExcel(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheetIndex = 0;

        foreach ($this->tables as $table => $columns) {
            $sheet = $sheetIndex === 0
                ? $spreadsheet->getActiveSheet()
                : $spreadsheet->createSheet($sheetIndex);

            $sheet->setTitle(Str::limit($table, 31));
            $this->writeSheet($sheet, $table, $columns);
            $sheetIndex++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'backup-full-'.now()->format('Ymd_His').'.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportSql(): StreamedResponse
    {
        $filename = 'backup-full-'.now()->format('Ymd_His').'.sql';
        $sql = $this->buildSqlDump();

        return response()->streamDownload(function () use ($sql) {
            echo $sql;
        }, $filename, ['Content-Type' => 'application/sql']);
    }

    public function restoreFromSql(string $path): void
    {
        DB::transaction(function () use ($path) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::unprepared(file_get_contents($path));
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        });
    }

    public function restoreFromExcel(string $path): void
    {
        $spreadsheet = IOFactory::load($path);
        $dataset = [];

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $table = Str::snake(Str::lower($sheet->getTitle()));

            if (! isset($this->tables[$table])) {
                continue;
            }

            $rows = $sheet->toArray(null, true, true, false);

            if (empty($rows)) {
                $dataset[$table] = collect();
                continue;
            }

            $headers = array_map(fn ($value) => trim((string) $value), array_shift($rows));

            $records = Collection::make($rows)
                ->filter(fn ($row) => array_filter($row, fn ($value) => $value !== null && $value !== ''))
                ->map(function ($row) use ($headers) {
                    $item = [];

                    foreach ($headers as $index => $column) {
                        if ($column === '') {
                            continue;
                        }

                        $item[$column] = $row[$index] ?? null;
                    }

                    return $item;
                })
                ->values();

            $dataset[$table] = $records;
        }

        DB::transaction(function () use ($dataset) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($this->tables as $table => $columns) {
                DB::table($table)->truncate();

                $records = $dataset[$table] ?? collect();

                if ($records->isEmpty()) {
                    continue;
                }

                $chunks = $records->chunk(500);

                foreach ($chunks as $chunk) {
                    DB::table($table)->insert(
                        $chunk->map(function ($record) use ($columns) {
                            $payload = [];

                            foreach ($columns as $column) {
                                $payload[$column] = $record[$column] ?? null;
                            }

                            return $payload;
                        })->toArray()
                    );
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        });
    }

    private function writeSheet($sheet, string $table, array $columns): void
    {
        foreach ($columns as $index => $column) {
            $sheet->setCellValue($this->columnName($index + 1).'1', $column);
        }

        $rows = DB::table($table)
            ->select($columns)
            ->orderBy('id')
            ->get();

        $rowNumber = 2;
        foreach ($rows as $row) {
            foreach ($columns as $columnIndex => $columnName) {
                $value = $row->{$columnName} ?? null;

                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value);
                }

                $sheet->setCellValue($this->columnName($columnIndex + 1).$rowNumber, $value);
            }
            $rowNumber++;
        }
    }

    private function columnName(int $index): string
    {
        $result = '';
        while ($index > 0) {
            $index--;
            $result = chr(65 + ($index % 26)) . $result;
            $index = intdiv($index, 26);
        }

        return $result;
    }

    private function buildSqlDump(): string
    {
        $lines = [];
        $lines[] = 'SET FOREIGN_KEY_CHECKS=0;';

        foreach ($this->tables as $table => $columns) {
            $lines[] = "TRUNCATE TABLE `{$table}`;";

            $rows = DB::table($table)->select($columns)->orderBy('id')->get();

            if ($rows->isEmpty()) {
                continue;
            }

            foreach ($rows as $row) {
                $values = array_map(function ($column) use ($row) {
                    $value = $row->{$column} ?? null;

                    if (is_null($value)) {
                        return 'NULL';
                    }

                    if (is_bool($value)) {
                        return $value ? '1' : '0';
                    }

                    if ($value instanceof \DateTimeInterface) {
                        $value = $value->format('Y-m-d H:i:s');
                    }

                    if (is_array($value) || is_object($value)) {
                        $value = json_encode($value);
                    }

                    return "'".str_replace("'", "''", (string) $value)."'";
                }, $columns);

                $lines[] = sprintf(
                    "INSERT INTO `%s` (%s) VALUES (%s);",
                    $table,
                    collect($columns)->map(fn ($col) => "`{$col}`")->implode(', '),
                    implode(', ', $values)
                );
            }
        }

        $lines[] = 'SET FOREIGN_KEY_CHECKS=1;';

        return implode(PHP_EOL, $lines);
    }
}
