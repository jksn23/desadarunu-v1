<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Transaction;
use App\Services\GeminiService;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Str;

class ModalTransaksi extends Component
{
    public bool $open = false;

    public ?int $transactionId = null;
    public string $type = 'pemasukan';
    public ?int $category_id = null;
    public string $description = '';
    public string $transaction_date;
    public string $amount = '';

    public bool $aiProcessing = false;
    public ?string $aiMessage = null;
    public bool $aiError = false;

    public function mount(): void
    {
        $this->transaction_date = now()->format('Y-m-d');
    }

    #[On('open-transaksi-modal')]
    public function open(?int $transactionId = null): void
    {
        $this->ensureOperator();
        $this->resetForm();

        if ($transactionId) {
            $this->loadTransaction($transactionId);
        }

        $this->open = true;
    }

    #[On('edit-transaksi')]
    public function edit(int $transactionId): void
    {
        $this->ensureOperator();
        $this->open($transactionId);
    }

    #[On('kategori-disimpan')]
    public function syncCategories(): void
    {
        // Trigger re-render to refresh daftar kategori.
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function save(): void
    {
        $this->ensureOperator();
        $validated = $this->validate($this->rules(), [], $this->attributes());

        $amount = $this->normalizeAmount($validated['amount']);

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Jumlah harus lebih besar dari 0.',
            ]);
        }

        $transaction = Transaction::updateOrCreate(
            [
                'id' => $this->transactionId,
                'user_id' => auth()->id(),
            ],
            [
                'category_id' => $validated['category_id'],
                'description' => $validated['description'],
                'amount' => $amount,
                'type' => $validated['type'],
                'transaction_date' => $validated['transaction_date'],
            ]
        );

        $this->dispatch('transaksi-disimpan', id: $transaction->id);
        $this->close();
        $this->resetForm();
    }

    public function delete(int $transactionId): void
    {
        $this->ensureOperator();
        Transaction::where('user_id', auth()->id())
            ->whereKey($transactionId)
            ->delete();

        $this->dispatch('transaksi-disimpan');
        $this->close();
    }

    public function suggestCategoryWithAi(GeminiService $gemini): void
    {
        $this->ensureOperator();
        $this->resetAiState();

        if (blank($this->description)) {
            $this->aiError = true;
            $this->aiMessage = 'Isi deskripsi transaksi sebelum meminta saran AI.';
            $this->addError('description', 'Deskripsi diperlukan untuk analisis AI.');

            return;
        }

        $categories = Category::where('user_id', auth()->id())
            ->orderBy('name')
            ->pluck('name', 'id');

        if ($categories->isEmpty()) {
            $this->aiError = true;
            $this->aiMessage = 'Belum ada kategori. Tambah kategori terlebih dahulu.';

            return;
        }

        $this->aiProcessing = true;

        try {
            $suggestion = $gemini->suggestCategory($this->description, $categories->values()->all());

            if (! $suggestion) {
                $this->aiError = true;
                $this->aiMessage = 'AI belum bisa memberikan saran kategori.';

                return;
            }

            $suggestion = Str::of($suggestion)->trim();

            $matchedId = null;
            foreach ($categories as $id => $name) {
                if (Str::lower($name) === Str::lower($suggestion)) {
                    $matchedId = $id;
                    break;
                }
            }

            if (! $matchedId) {
                // fallback fuzzy contains
                foreach ($categories as $id => $name) {
                    if (Str::contains(Str::lower($name), Str::lower($suggestion))) {
                        $matchedId = $id;
                        break;
                    }
                }
            }

            if (! $matchedId) {
                $this->aiError = true;
                $this->aiMessage = sprintf(
                    'AI menyarankan "%s" tetapi tidak ditemukan pada daftar kategori.',
                    $suggestion
                );

                return;
            }

            $this->category_id = $matchedId;
            $this->aiMessage = sprintf('Kategori disarankan: %s', $categories[$matchedId]);
        } finally {
            $this->aiProcessing = false;
        }
    }

    public function render()
    {
        $categories = Category::where('user_id', auth()->id())
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('livewire.modal-transaksi', [
            'categories' => $categories,
            'isEditing' => filled($this->transactionId),
        ]);
    }

    private function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['pemasukan', 'pengeluaran'])],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('user_id', auth()->id()),
            ],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'string'],
            'transaction_date' => ['required', 'date'],
        ];
    }

    private function attributes(): array
    {
        return [
            'type' => 'jenis transaksi',
            'category_id' => 'kategori',
            'description' => 'deskripsi',
            'amount' => 'jumlah',
            'transaction_date' => 'tanggal transaksi',
        ];
    }

    private function resetForm(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->transactionId = null;
        $this->type = 'pemasukan';
        $this->category_id = null;
        $this->description = '';
        $this->transaction_date = now()->format('Y-m-d');
        $this->amount = '';
        $this->resetAiState();
    }

    private function loadTransaction(int $transactionId): void
    {
        $transaction = Transaction::where('user_id', auth()->id())
            ->whereKey($transactionId)
            ->firstOrFail();

        $this->transactionId = $transaction->id;
        $this->type = $transaction->type;
        $this->category_id = $transaction->category_id;
        $this->description = $transaction->description;
        $this->transaction_date = $transaction->transaction_date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $this->amount = number_format($transaction->amount, 2, ',', '.');
    }

    private function normalizeAmount(string $value): float
    {
        $normalized = preg_replace('/[^\d,.-]/', '', $value) ?? '0';
        $normalized = str_replace(['.', ','], ['', '.'], $normalized);

        return (float) $normalized;
    }

    private function resetAiState(): void
    {
        $this->aiProcessing = false;
        $this->aiMessage = null;
        $this->aiError = false;
    }

    private function ensureOperator(): void
    {
        abort_unless(auth()->user()?->role === 'operator', 403);
    }
}
