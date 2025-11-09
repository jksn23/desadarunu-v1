<?php

namespace App\Livewire;

use App\Models\Category;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalKategori extends Component
{
    public bool $open = false;

    public ?int $categoryId = null;
    public string $name = '';
    public string $type = 'pemasukan';

    #[On('open-kategori-modal')]
    public function open(?int $categoryId = null): void
    {
        $this->resetForm();

        if ($categoryId) {
            $this->loadCategory($categoryId);
        }

        $this->open = true;
    }

    #[On('edit-kategori')]
    public function edit(int $categoryId): void
    {
        $this->open($categoryId);
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules(), [], $this->attributes());

        Category::updateOrCreate(
            [
                'id' => $this->categoryId,
                'user_id' => auth()->id(),
            ],
            [
                'name' => $validated['name'],
                'type' => $validated['type'],
            ]
        );

        $this->dispatch('kategori-disimpan');
        $this->close();
        $this->resetForm();
    }

    public function delete(int $categoryId): void
    {
        Category::where('user_id', auth()->id())
            ->whereKey($categoryId)
            ->delete();

        $this->dispatch('kategori-disimpan');
    }

    public function render()
    {
        $categories = Category::where('user_id', auth()->id())
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('livewire.modal-kategori', [
            'categories' => $categories,
            'isEditing' => filled($this->categoryId),
        ]);
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(['pemasukan', 'pengeluaran'])],
        ];
    }

    private function attributes(): array
    {
        return [
            'name' => 'nama kategori',
            'type' => 'jenis kategori',
        ];
    }

    private function resetForm(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->categoryId = null;
        $this->name = '';
        $this->type = 'pemasukan';
    }

    private function loadCategory(int $categoryId): void
    {
        $category = Category::where('user_id', auth()->id())
            ->whereKey($categoryId)
            ->firstOrFail();

        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->type = $category->type;
    }
}
