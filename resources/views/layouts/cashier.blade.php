<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="Zazil Bakery — Dashboard Kasir" />

    <title>@yield('title', 'Dashboard Kasir') — Zazil Bakery</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>

    @stack('styles')
</head>
<body class="bg-surface font-sans text-on-surface antialiased" x-data="{ sidebarOpen: false }">

    {{-- Mobile Overlay --}}
    <div
        x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/40 lg:hidden"
        style="display: none;"
    ></div>

    {{-- Sidebar --}}
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed top-0 left-0 z-50 h-full w-64 bg-primary-container text-on-primary transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col"
    >
        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
            <img src="{{ asset('images/logo.png') }}" alt="Zazil Bakery Logo" class="h-10 w-10 object-contain bg-white rounded-md p-1" />
            <div>
                <h1 class="text-lg font-bold leading-tight">Zazil Bakery</h1>
                <p class="text-xs text-white/60">Dashboard Kasir</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 overflow-y-auto space-y-1">



            {{-- Cashier Section --}}
            <p class="px-4 pt-1 pb-2 text-[10px] font-bold uppercase tracking-widest text-white/40">Kasir</p>

            @php
                $cashierNav = [
                    ['route' => 'cashier.dashboard',    'icon' => 'point_of_sale',    'label' => 'Dashboard Kasir',    'match' => 'cashier.dashboard'],
                    ['route' => 'cashier.orders.create','icon' => 'add_shopping_cart', 'label' => 'Pesanan Baru (POS)', 'match' => 'cashier.orders.create'],
                    ['route' => 'cashier.orders.index', 'icon' => 'receipt_long',     'label' => 'Kelola Pesanan',     'match' => ['cashier.orders.index', 'cashier.orders.show']],
                ];
            @endphp

            @foreach ($cashierNav as $item)
                @php
                    $isActive = is_array($item['match']) ? request()->routeIs(...$item['match']) : request()->routeIs($item['match']);
                @endphp
                <a
                    href="{{ route($item['route']) }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                        {{ $isActive ? 'bg-white/15 text-white shadow-sm' : 'text-white/70 hover:bg-white/10 hover:text-white' }}"
                >
                    <span class="material-symbols-outlined text-[20px]" @if($isActive) style="font-variation-settings: 'FILL' 1;" @endif>{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Bottom --}}
        <div class="px-4 py-4 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium bg-red-500 text-white hover:bg-red-600 transition-all duration-200 shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="lg:ml-64 min-h-screen flex flex-col">
        {{-- Top Header --}}
        <header class="sticky top-0 z-30 bg-surface/80 backdrop-blur-md border-b border-outline-variant">
            <div class="flex items-center justify-between px-4 lg:px-8 py-3">
                <div class="flex items-center gap-4">
                    {{-- Mobile menu toggle --}}
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden material-symbols-outlined text-on-surface hover:text-primary transition-colors">
                        menu
                    </button>
                    <h2 class="text-headline-md text-primary hidden sm:block">@yield('page-title', 'Beranda')</h2>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-label-sm text-on-surface-variant bg-surface-container px-3 py-1.5 rounded-lg">
                        {{ now()->timezone('Asia/Jakarta')->format('d M Y') }}
                    </span>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                x-transition:leave="transition-opacity ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="mx-4 lg:mx-8 mt-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-label-md flex items-center gap-2"
            >
                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                x-transition:leave="transition-opacity ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="mx-4 lg:mx-8 mt-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-label-md flex items-center gap-2"
            >
                <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">error</span>
                {{ session('error') }}
            </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 p-4 lg:p-8">
            @yield('content')
        </main>
    </div>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</body>
</html>
