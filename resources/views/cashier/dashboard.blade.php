@extends('layouts.cashier')

@section('title', 'Dashboard Kasir')
@section('page-title', 'Dashboard Kasir')

@section('content')

    {{-- ── Summary Cards ──────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">

        {{-- New Orders --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-label-sm text-on-surface-variant">Pesanan Baru</span>
                <span class="material-symbols-outlined text-amber-600 text-[24px] bg-amber-50 p-2 rounded-xl" style="font-variation-settings: 'FILL' 1;">notification_important</span>
            </div>
            <p class="text-2xl font-bold text-amber-600">{{ $newOrders }}</p>
            <p class="text-label-sm text-on-surface-variant mt-1">Menunggu konfirmasi</p>
        </div>

        {{-- In Process --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-label-sm text-on-surface-variant">Sedang Diproses</span>
                <span class="material-symbols-outlined text-blue-600 text-[24px] bg-blue-50 p-2 rounded-xl" style="font-variation-settings: 'FILL' 1;">skillet</span>
            </div>
            <p class="text-2xl font-bold text-blue-600">{{ $inProcess }}</p>
            <p class="text-label-sm text-on-surface-variant mt-1">Sedang disiapkan</p>
        </div>

        {{-- Completed Today --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-label-sm text-on-surface-variant">Selesai Hari Ini</span>
                <span class="material-symbols-outlined text-emerald-600 text-[24px] bg-emerald-50 p-2 rounded-xl" style="font-variation-settings: 'FILL' 1;">task_alt</span>
            </div>
            <p class="text-2xl font-bold text-emerald-600">{{ $completedToday }}</p>
            <p class="text-label-sm text-on-surface-variant mt-1">
                Dari pesanan manual
            </p>
        </div>

        {{-- Today's Revenue --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-label-sm text-on-surface-variant">Pendapatan Hari Ini</span>
                <span class="material-symbols-outlined text-primary text-[24px] bg-primary/10 p-2 rounded-xl" style="font-variation-settings: 'FILL' 1;">payments</span>
            </div>
            <p class="text-xl font-bold text-primary">Rp{{ number_format($todayRevenue, 0, ',', '.') }}</p>
            <p class="text-label-sm text-on-surface-variant mt-1">Dari pesanan selesai</p>
        </div>
    </div>

    {{-- ── Quick Actions ─────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap gap-3 mb-8">
        <a href="{{ route('cashier.orders.create') }}"
           class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-xl text-label-md hover:opacity-90 transition-opacity shadow-sm">
            <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
            Pesanan Manual Baru
        </a>
        <a href="{{ route('cashier.orders.index') }}"
           class="inline-flex items-center gap-2 bg-surface-container border border-outline-variant text-on-surface px-5 py-2.5 rounded-xl text-label-md hover:bg-surface-container-high transition-colors">
            <span class="material-symbols-outlined text-[20px]">receipt_long</span>
            Semua Pesanan
        </a>
    </div>

    {{-- ── Main Grid ─────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Right Column --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Recent Manual Orders --}}
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-primary" style="font-variation-settings: 'FILL' 1;">point_of_sale</span>
                        <h3 class="text-headline-md text-primary">Pesanan Manual</h3>
                    </div>
                    <a href="{{ route('cashier.orders.create') }}" class="text-label-sm text-primary hover:underline">+ Baru</a>
                </div>
                <div class="divide-y divide-outline-variant">
                    @forelse ($recentManualOrders as $order)
                        <a href="{{ route('cashier.orders.show', $order) }}"
                           class="flex items-center gap-3 px-5 py-3 hover:bg-surface-container-low transition-colors group">
                            <div class="flex-1 min-w-0">
                                <p class="text-label-md text-primary truncate">{{ $order->invoice_number }}</p>
                                <p class="text-label-sm text-on-surface-variant">{{ $order->customer_name ?? 'Tamu' }} · {{ $order->items->count() }} item</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-label-md text-primary font-semibold">Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                                <span class="text-[11px] px-2 py-0.5 rounded-full font-medium
                                    {{ match($order->status) {
                                        'pending'   => 'bg-amber-50 text-amber-700',
                                        'preparing' => 'bg-blue-50 text-blue-700',
                                        'completed' => 'bg-primary/10 text-primary',
                                        'cancelled' => 'bg-red-50 text-red-700',
                                        default     => 'bg-surface-container text-on-surface-variant',
                                    } }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-center">
                            <span class="material-symbols-outlined text-[36px] text-outline-variant mb-2 block">receipt</span>
                            <p class="text-label-sm text-on-surface-variant">Belum ada pesanan manual hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Today's Stats breakdown --}}
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm p-5">
                <h3 class="text-headline-md text-primary mb-4">Ringkasan Hari Ini</h3>
                <div class="space-y-3">
                    @foreach([
                        ['label' => 'Pesanan Manual', 'value' => $manualOrdersToday, 'icon' => 'point_of_sale',    'color' => 'text-blue-600'],
                        ['label' => 'Selesai',        'value' => $completedToday,    'icon' => 'task_alt',         'color' => 'text-emerald-600'],
                        ['label' => 'Menunggu',       'value' => $pendingOrders,     'icon' => 'pending',          'color' => 'text-amber-500'],
                    ] as $stat)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] {{ $stat['color'] }}" style="font-variation-settings: 'FILL' 1;">{{ $stat['icon'] }}</span>
                                <span class="text-label-sm text-on-surface-variant">{{ $stat['label'] }}</span>
                            </div>
                            <span class="text-label-md {{ $stat['color'] }} font-bold">{{ $stat['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection
