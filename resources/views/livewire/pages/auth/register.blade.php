<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.auth-guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('transaksi', absolute: false), navigate: true);
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
                            <h2 class="text-2xl font-semibold">Bangun Kepercayaan Warga</h2>
                        </div>
                    </div>
                    <p class="text-sm text-blue-100 leading-relaxed">
                        Ajak perangkat desa lain bergabung untuk kolaborasi transparansi keuangan. Setiap akun tercatat aman dan siap mendukung musyawarah maupun laporan warga.
                    </p>
                    <ul class="space-y-3 text-sm text-blue-100">
                        <li class="flex items-start gap-2">
                            <span class="mt-1 flex h-5 w-5 items-center justify-center rounded-full bg-white/15">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5" />
                                </svg>
                            </span>
                            Hak akses berbasis peran untuk aparatur desa.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 flex h-5 w-5 items-center justify-center rounded-full bg-white/15">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5" />
                                </svg>
                            </span>
                            Sinkron dengan laporan kas, laba rugi, dan neraca.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 flex h-5 w-5 items-center justify-center rounded-full bg-white/15">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5" />
                                </svg>
                            </span>
                            Autentikasi Laravel menjamin keamanan aplikasi.
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
                            <h2 class="text-xl font-semibold">Bangun Kepercayaan Warga</h2>
                        </div>
                    </div>
                    <p class="mt-3 text-sm text-blue-100 leading-relaxed">
                        Daftarkan akun aparatur desa untuk menjaga pencatatan kas tetap transparan dan terkoordinasi.
                    </p>
                </div>

                <div class="mx-auto w-full max-w-xl space-y-8">
                    <header class="space-y-4">
                        <div class="inline-flex items-center gap-3">
                            <img src="{{ asset('images/logopemkab.png') }}" alt="Logo Pemkab" class="h-12 w-12 rounded-2xl bg-blue-100 object-contain p-1">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-600">Akun Baru Perangkat Desa</p>
                                <h1 class="text-2xl font-bold text-slate-900 sm:text-3xl">Daftar untuk Mulai Mengelola Kas</h1>
                            </div>
                        </div>
                        <p class="text-sm text-slate-500 leading-relaxed sm:text-base">
                            Isi identitas Anda dengan benar agar akses ke dasbor keuangan desa tetap terjaga dan akurat.
                        </p>
                    </header>

                    <form wire:submit="register" class="space-y-6">
                        <div class="space-y-2">
                            <label for="name" class="text-sm font-medium text-slate-700">Nama Lengkap</label>
                            <input
                                wire:model="name"
                                id="name"
                                type="text"
                                name="name"
                                autocomplete="name"
                                required
                                autofocus
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                placeholder="contoh: Budi Santoso"
                            >
                            <x-input-error :messages="$errors->get('name')" class="text-xs text-red-600" />
                        </div>

                        <div class="space-y-2">
                            <label for="email" class="text-sm font-medium text-slate-700">Email Perangkat Desa</label>
                            <input
                                wire:model="email"
                                id="email"
                                type="email"
                                name="email"
                                autocomplete="username"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                placeholder="contoh: bendahara@darunu.id"
                            >
                            <x-input-error :messages="$errors->get('email')" class="text-xs text-red-600" />
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="text-sm font-medium text-slate-700">Kata Sandi</label>
                            <input
                                wire:model="password"
                                id="password"
                                type="password"
                                name="password"
                                autocomplete="new-password"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                placeholder="minimal 8 karakter, kombinasi huruf & angka"
                            >
                            <x-input-error :messages="$errors->get('password')" class="text-xs text-red-600" />
                        </div>

                        <div class="space-y-2">
                            <label for="password_confirmation" class="text-sm font-medium text-slate-700">Konfirmasi Kata Sandi</label>
                            <input
                                wire:model="password_confirmation"
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                autocomplete="new-password"
                                required
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                placeholder="ulangi kata sandi Anda"
                            >
                            <x-input-error :messages="$errors->get('password_confirmation')" class="text-xs text-red-600" />
                        </div>

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed disabled:opacity-80"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="register">Daftarkan Akun Baru</span>
                            <span wire:loading wire:target="register" class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4l5-5-5-5v4a12 12 0 0 0-12 12h4z"></path>
                                </svg>
                                Memproses...
                            </span>
                        </button>

                        <p class="text-xs text-slate-500 text-center">
                            Sudah memiliki akun? <a href="{{ route('login') }}" wire:navigate class="font-semibold text-blue-600 hover:text-blue-700">Masuk di sini</a>.
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
