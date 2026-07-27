@extends('layouts.owner')

@section('title', 'Beranda')
@section('page-title', 'Beranda')

@section('content')
    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        {{-- Revenue Today --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-label-sm text-on-surface-variant">Pendapatan Hari Ini</span>
                <span class="material-symbols-outlined text-primary text-[24px] bg-primary/10 p-2 rounded-xl" style="font-variation-settings: 'FILL' 1;">payments</span>
            </div>
            <p class="text-2xl font-bold text-primary">Rp{{ number_format($todayRevenue, 0, ',', '.') }}</p>
        </div>

        {{-- Orders Today --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-label-sm text-on-surface-variant">Pesanan Hari Ini</span>
                <span class="material-symbols-outlined text-blue-600 text-[24px] bg-blue-50 p-2 rounded-xl" style="font-variation-settings: 'FILL' 1;">receipt_long</span>
            </div>
            <p class="text-2xl font-bold text-primary">{{ $todayOrders }}</p>
        </div>

        {{-- Customers Today --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-label-sm text-on-surface-variant">Pelanggan Hari Ini</span>
                <span class="material-symbols-outlined text-emerald-600 text-[24px] bg-emerald-50 p-2 rounded-xl" style="font-variation-settings: 'FILL' 1;">group</span>
            </div>
            <p class="text-2xl font-bold text-primary">{{ $todayCustomers }}</p>
        </div>

        {{-- Active Tables --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-label-sm text-on-surface-variant">Meja Aktif</span>
                <span class="material-symbols-outlined text-amber-600 text-[24px] bg-amber-50 p-2 rounded-xl" style="font-variation-settings: 'FILL' 1;">table_restaurant</span>
            </div>
            <p class="text-2xl font-bold text-primary">{{ $activeTables }}<span class="text-sm font-normal text-on-surface-variant"> / {{ $totalTables }}</span></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Best Selling Menu --}}
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between">
                <h3 class="text-headline-md text-primary">Menu Terlaris</h3>
                <a href="{{ route('owner.reports.index') }}" class="text-label-sm text-primary hover:underline">Lihat Semua →</a>
            </div>
            <div class="divide-y divide-outline-variant">
                @forelse ($bestSelling as $index => $item)
                    <div class="flex items-center gap-4 px-5 py-3 hover:bg-surface-container-low transition-colors">
                        <span class="text-label-sm text-on-surface-variant w-6 text-center font-bold">{{ $index + 1 }}</span>
                        <img src="{{ $item->image }}" alt="{{ $item->name }}" class="w-12 h-12 rounded-xl object-cover flex-shrink-0" />
                        <div class="flex-1 min-w-0">
                            <p class="text-label-md text-primary truncate">{{ $item->name }}</p>
                            <p class="text-label-sm text-on-surface-variant">{{ $item->total_sold }} terjual</p>
                        </div>
                        <span class="text-label-md text-primary font-semibold">Rp{{ number_format($item->total_revenue, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <span class="material-symbols-outlined text-[48px] text-outline-variant mb-2 block">restaurant_menu</span>
                        <p class="text-body-md text-on-surface-variant">Belum ada data penjualan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-outline-variant">
                <h3 class="text-headline-md text-primary">Aktivitas Terbaru</h3>
            </div>
            <div class="divide-y divide-outline-variant max-h-[400px] overflow-y-auto">
                @forelse ($recentOrders as $order)
                    <div class="px-5 py-3 hover:bg-surface-container-low transition-colors">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-label-md text-primary">{{ $order->invoice_number }}</span>
                            <span class="text-[11px] px-2 py-0.5 rounded-full font-medium
                                {{ match($order->status) {
                                    'pending'   => 'bg-yellow-50 text-yellow-700',
                                    'preparing' => 'bg-blue-50 text-blue-700',
                                    'completed' => 'bg-primary/10 text-primary',
                                    'cancelled' => 'bg-red-50 text-red-700',
                                    default     => 'bg-surface-container text-on-surface-variant',
                                } }}
                            ">{{ $order->status_label }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-label-sm text-on-surface-variant">Meja {{ $order->table_number }} · {{ $order->items->count() }} item</span>
                            <span class="text-label-sm text-on-surface-variant">{{ $order->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <span class="material-symbols-outlined text-[48px] text-outline-variant mb-2 block">receipt_long</span>
                        <p class="text-body-md text-on-surface-variant">Belum ada pesanan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
