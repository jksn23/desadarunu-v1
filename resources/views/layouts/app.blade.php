<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Buku Kas Desa') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <livewire:styles />
    </head>
    <body class="font-sans antialiased bg-gray-100 text-gray-800">

        @php
            $navItems = [
                [
                    'label' => 'Transaksi',
                    'route' => 'transaksi',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2" /><rect width="7" height="7" x="3" y="3" /><rect width="7" height="7" x="14" y="3" /><rect width="7" height="7" x="14" y="14" /><rect width="7" height="7" x="3" y="14" /></svg>',
                ],
                [
                    'label' => 'Lap. Arus Kas',
                    'route' => 'laporan.kas',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>',
                ],
                [
                    'label' => 'Lap. Laba Rugi',
                    'route' => 'laporan.laba-rugi',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>',
                ],
                [
                    'label' => 'Lap. Neraca',
                    'route' => 'laporan.neraca',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m16 16 3-8 3 8c.3.8-.1 1.6-.9 1.8-1 .3-1.9-.4-2.1-1.4l-1-4.6-1 4.6c-.2 1-.9 1.7-2 1.4-.7-.2-1.1-1-1-1.8Z"/><path d="m2 16 3-8 3 8c.3.8-.1 1.6-.9 1.8-1 .3-1.9-.4-2.1-1.4l-1-4.6-1 4.6c-.2 1-.9 1.7-2 1.4-.7-.2-1.1-1-1-1.8Z"/><path d="M7 21h10"/><path d="M12 3v18"/></svg>',
                ],
            ];
        @endphp
        <div class="min-h-screen flex bg-gray-100">
            <aside class="fixed inset-y-0 left-0 hidden w-64 bg-white shadow-lg md:flex md:flex-col z-40">
                <div class="flex items-center justify-center h-20 border-b border-gray-200">
                    <span class="text-2xl font-bold text-blue-600">Desa Darunu</span>
                </div>
                @if (auth()->user()?->role === 'operator')
                    <div class="p-4">
                        <button
                            type="button"
                            class="w-full flex items-center justify-center gap-2 bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors"
                            onclick="Livewire.dispatch('open-transaksi-modal')"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            Catat Transaksi
                        </button>
                    </div>
                @endif
                <nav class="flex-1 px-4 space-y-2 overflow-y-auto">
                    @foreach ($navItems as $item)
                        @php
                            $isActive = request()->routeIs($item['route']);
                        @endphp
                        <a
                            href="{{ route($item['route']) }}"
                            wire:navigate
                            class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors {{ $isActive ? 'bg-blue-600 text-white shadow' : 'text-gray-600 hover:bg-gray-100' }}"
                        >
                            {!! $item['icon'] !!}
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endforeach

                    @php $role = auth()->user()?->role; @endphp

                    @if ($role === 'admin_desa')
                        <div class="pt-6">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">Menu Admin Desa</p>
                            <a
                                href="{{ route('admin-desa.dashboard') }}"
                                wire:navigate
                                class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin-desa.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 13h2l4-8 4 16 4-8 2 4h2"/>
                                </svg>
                                Dashboard Admin Desa
                            </a>
                            <a
                                href="{{ route('admin-desa.operators') }}"
                                wire:navigate
                                class="mt-2 flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin-desa.operators') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="7" r="4"/>
                                    <path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/>
                                </svg>
                                Kelola Operator
                            </a>
                        </div>
                    @elseif ($role === 'admin_web')
                        <div class="pt-6">
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">Menu Admin Web</p>
                            <a
                                href="{{ route('admin.dashboard') }}"
                                wire:navigate
                                class="flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 3h18v6H3z"/><path d="M16 21H8l-5-8h18z"/>
                                </svg>
                                Dashboard Admin Web
                            </a>
                            <a
                                href="{{ route('admin.users') }}"
                                wire:navigate
                                class="mt-2 flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.users') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="8.5" cy="7" r="4"/><path d="M17 11v-1a4 4 0 0 0-3-3.87"/><path d="M4 21v-2a4 4 0 0 1 4-4h2"/>
                                </svg>
                                Kelola Pengguna
                            </a>
                            <a
                                href="{{ route('admin.logs') }}"
                                wire:navigate
                                class="mt-2 flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.logs') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 3h18v4H3z"/><path d="M7 7v14"/><path d="M17 7v14"/><path d="M3 11h18"/>
                                </svg>
                                Log Aktivitas
                            </a>
                            <a
                                href="{{ route('admin.backup') }}"
                                wire:navigate
                                class="mt-2 flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium {{ request()->routeIs('admin.backup') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 5v14"/><path d="m19 12-7 7-7-7"/>
                                </svg>
                                Backup & Restore
                            </a>
                        </div>
                    @endif
                </nav>
                <div class="p-4 border-t border-gray-200 text-sm text-gray-600">
                    <p class="font-semibold text-gray-800">Hai, {{ auth()->user()->name ?? 'Pengguna' }}</p>
                    <p class="mt-1">Buku Kas Desa Darunu</p>
                </div>
            </aside>

            <div class="flex flex-col flex-1 min-h-screen w-full md:ml-64">
                <header class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-gray-200">
                    <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 md:px-10">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Buku Kas Desa</p>
                            <h1 class="text-lg font-semibold text-gray-900 md:text-2xl">Desa Darunu</h1>
                        </div>
                        <div class="flex items-center gap-2 md:gap-3">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 rounded-full border border-red-200 bg-white px-4 py-2 text-xs font-semibold text-red-600 shadow-sm transition md:text-sm hover:bg-red-50"
                                onclick="document.getElementById('logout-form').submit()"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                    <path d="M10 17l5-5-5-5" />
                                    <path d="M15 12H3" />
                                </svg>
                                Keluar
                            </button>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                            @if (auth()->user()?->role === 'operator')
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-full bg-blue-600 text-white text-sm font-medium px-4 py-2 shadow hover:bg-blue-700 transition-colors md:hidden"
                                    onclick="Livewire.dispatch('open-transaksi-modal')"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                    Baru
                                </button>
                            @endif
                        </div>
                    </div>
                </header>

                <main class="flex-1 px-4 pt-6 pb-24 md:px-10 md:py-10">
                    @if (isset($header))
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endif
                    {{ $slot }}
                </main>
            </div>
        </div>

        <nav class="fixed bottom-0 inset-x-0 z-40 bg-white border-t border-gray-200 shadow-lg md:hidden">
            <div class="grid grid-cols-4">
                @foreach ($navItems as $item)
                    @php
                        $isActive = request()->routeIs($item['route']);
                    @endphp
                    <a
                        href="{{ route($item['route']) }}"
                        wire:navigate
                        class="flex flex-col items-center justify-center py-2 text-xs font-medium {{ $isActive ? 'text-blue-600' : 'text-gray-500' }}"
                    >
                        <span class="mb-1">{!! $item['icon'] !!}</span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </nav>

        @livewire('modal-transaksi')
        @livewire('modal-kategori')
        <livewire:scripts />
        @stack('modals')
        @stack('scripts')
    </body>
</html>
