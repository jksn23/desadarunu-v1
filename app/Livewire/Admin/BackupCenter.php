<?php

namespace App\Livewire\Admin;

use App\Services\DataBackupService;
use App\Services\ReportExportService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class BackupCenter extends Component
{
    use WithFileUploads;

    public string $startDate;
    public string $endDate;
    public $restoreFile = null;
    public ?string $restoreStatus = null;
    public ?string $restoreError = null;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function downloadExcel(ReportExportService $service)
    {
        return $service->exportCsv([
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }

    public function downloadPdf(ReportExportService $service)
    {
        return $service->exportPdf([
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }

    public function downloadFullExcel(DataBackupService $service)
    {
        return $service->exportExcel();
    }

    public function downloadSqlBackup(DataBackupService $service)
    {
        return $service->exportSql();
    }

    public function restoreData(DataBackupService $service): void
    {
        $this->validate([
            'restoreFile' => ['required', 'file', 'max:15360', 'mimes:sql,xlsx'],
        ], [
            'restoreFile.required' => 'Pilih file backup terlebih dahulu.',
            'restoreFile.mimes' => 'Format file harus .sql atau .xlsx.',
            'restoreFile.max' => 'Ukuran file maksimal 15MB.',
        ]);

        $storedPath = $this->restoreFile->storeAs(
            'backup-imports',
            now()->format('Ymd_His').'.'.$this->restoreFile->getClientOriginalExtension()
        );

        $absolutePath = Storage::path($storedPath);

        try {
            if (Str::endsWith(Str::lower($absolutePath), '.sql')) {
                $service->restoreFromSql($absolutePath);
            } else {
                $service->restoreFromExcel($absolutePath);
            }

            $this->restoreStatus = 'Restore berhasil dijalankan. Data terbaru telah dimuat ulang.';
            $this->restoreError = null;
        } catch (\Throwable $exception) {
            report($exception);
            $this->restoreError = 'Restore gagal: '.$exception->getMessage();
            $this->restoreStatus = null;
        } finally {
            $this->reset('restoreFile');
        }
    }

    public function render()
    {
        return view('livewire.admin.backup-center');
    }
}
