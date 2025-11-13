<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth-guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: $this->redirectPath(), navigate: true);
    }

    private function redirectPath(): string
    {
        return match (auth()->user()?->role) {
            'admin_web' => route('admin.dashboard', absolute: false),
            'admin_desa' => route('admin-desa.dashboard', absolute: false),
            default => route('transaksi', absolute: false),
        };
    }
}; ?>

<div class="min-h-screen bg-slate-100 flex items-center justify-center px-4 py-10 sm:px-6 lg:px-12 lg:py-16">
    <section class="w-full max-w-6xl xl:max-w-[1200px] bg-white rounded-3xl shadow-2xl overflow-hidden">
        <div class="flex flex-col md:flex-row">
            <aside class="hidden md:flex w-full md:w-5/12 xl:w-[430px] bg-gradient-to-br from-blue-700 via-blue-600 to-blue-500 text-white px-10 py-14 xl:px-16 xl:py-16">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-3">
                        <img src="{{ asset('images/logopemkab.png') }}" alt="Logo Pemkab" class="h-12 w-12 rounded-2xl bg-white/20 object-contain p-1">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/70">Buku Kas Desa</p>
                            <h2 class="text-2xl font-semibold">Keuangan Darunu</h2>
                        </div>
                    </div>
                    <p class="text-sm text-blue-100 leading-relaxed">
                        Seluruh pemasukan dan pengeluaran desa terdokumentasi rapi, siap dipresentasikan dalam musyawarah maupun laporan warga. Akses cepat, aman, dan responsif di berbagai perangkat.
                    </p>
                    <ul class="space-y-3 text-sm text-blue-100">
                        <li class="flex items-start gap-2">
                            <span class="mt-1 flex h-5 w-5 items-center justify-center rounded-full bg-white/15">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5" />
                                </svg>
                            </span>
                            Sinkronisasi laporan kas, laba rugi, dan neraca otomatis.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 flex h-5 w-5 items-center justify-center rounded-full bg-white/15">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5" />
                                </svg>
                            </span>
                            Rekomendasi kategori dan ringkasan cerdas melalui Gemini AI.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 flex h-5 w-5 items-center justify-center rounded-full bg-white/15">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5" />
                                </svg>
                            </span>
                            Autentikasi Laravel menjaga akses perangkat desa tetap aman.
                        </li>
                    </ul>
                </div>
            </aside>

            <div class="w-full md:flex-1 px-6 py-8 sm:px-10 sm:py-12 lg:px-14 xl:px-16 xl:py-16">
                <div class="md:hidden mb-8 rounded-3xl bg-gradient-to-br from-blue-700 via-blue-600 to-blue-500 p-6 text-white shadow-lg">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logopemkab.png') }}" alt="Logo Pemkab" class="h-10 w-10 rounded-2xl bg-white/20 object-contain p-1">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/80">Buku Kas Desa</p>
                            <h2 class="text-xl font-semibold">Keuangan Desa Darunu</h2>
                        </div>
                    </div>
                    <p class="mt-3 text-sm text-blue-100 leading-relaxed">
                        Catat transaksi, pantau arus kas, dan siapkan laporan komprehensif dalam satu aplikasi modern.
                    </p>
                </div>

                <div class="mx-auto w-full max-w-xl space-y-8">
                    <header class="space-y-4">
                        <div class="inline-flex items-center gap-3">
                            <img src="{{ asset('images/logopemkab.png') }}" alt="Logo Pemkab" class="h-12 w-12 rounded-2xl bg-blue-100 object-contain p-1">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-600">Buku Kas Desa Darunu</p>
                                <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">Masuk ke Dasbor Keuangan</h1>
                            </div>
                        </div>
                        <p class="text-sm text-slate-500 leading-relaxed sm:text-base">
                            Catat transaksi, pantau arus kas, dan kelola laporan keuangan desa dalam satu aplikasi yang rapi dan aman.
                        </p>
                    </header>

                    <x-auth-session-status class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700" :status="session('status')" />

                    <form wire:submit="login" class="space-y-6">
                        <div class="space-y-2">
                            <label for="email" class="text-sm font-medium text-slate-700">Email</label>
                            <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 5h18v14H3z" />
                                    <path d="m3 7 9 6 9-6" />
                                </svg>
                                <input
                                    wire:model="form.email"
                                    id="email"
                                    type="email"
                                    name="email"
                                    autocomplete="username"
                                    required
                                    autofocus
                                    class="w-full border-none bg-transparent text-sm text-slate-800 focus:outline-none focus:ring-0"
                                    placeholder="masukkan email aparatur"
                                >
                            </div>
                            <x-input-error :messages="$errors->get('form.email')" class="text-xs text-red-600" />
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="text-sm font-medium text-slate-700">Kata Sandi</label>
                            <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                                <input
                                    wire:model="form.password"
                                    id="password"
                                    type="password"
                                    name="password"
                                    autocomplete="current-password"
                                    required
                                    class="w-full border-none bg-transparent text-sm text-slate-800 focus:outline-none focus:ring-0"
                                    placeholder="masukkan kata sandi"
                                >
                            </div>
                            <x-input-error :messages="$errors->get('form.password')" class="text-xs text-red-600" />
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <label for="remember" class="inline-flex items-center gap-2 text-xs font-medium text-slate-600">
                                <input
                                    wire:model="form.remember"
                                    id="remember"
                                    type="checkbox"
                                    name="remember"
                                    class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                >
                                Ingat saya di perangkat ini
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" wire:navigate class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                                    Lupa kata sandi?
                                </a>
                            @endif
                        </div>

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed disabled:opacity-80"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="login">Masuk ke Aplikasi</span>
                            <span wire:loading wire:target="login" class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4l5-5-5-5v4a12 12 0 0 0-12 12h4z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>

                        <p class="text-xs text-slate-500 text-center">
                            Tidak memiliki akses? Hubungi admin desa atau admin website.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
