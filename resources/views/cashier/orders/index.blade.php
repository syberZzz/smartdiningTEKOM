@extends('layouts.cashier')

@section('title', 'Kelola Pesanan')
@section('page-title', 'Kelola Pesanan')

@section('content')

    {{-- ── Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-headline-lg-mobile lg:text-headline-lg text-primary">Kelola Pesanan</h1>
            <p class="text-body-md text-on-surface-variant mt-1">Lihat dan kelola semua pesanan</p>
        </div>
        <a href="{{ route('cashier.orders.create') }}"
           class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-xl text-label-md hover:opacity-90 transition-opacity shadow-sm">
            <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
            Pesanan Baru (POS)
        </a>
    </div>

    {{-- ── Filters ──────────────────────────────────────────────────────── --}}
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('cashier.orders.index') }}" class="space-y-3">
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Search --}}
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari invoice, meja, pelanggan…"
                           class="w-full pl-10 pr-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors" />
                </div>

                {{-- Date Period --}}
                <select name="period" class="px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors">
                    <option value="today" {{ request('period', 'today') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="week"  {{ request('period') === 'week'  ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value=""      {{ request('period') === ''      ? 'selected' : '' }}>Semua Waktu</option>
                </select>

                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary/10 text-primary rounded-xl text-label-md hover:bg-primary/20 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    Filter
                </button>
            </div>

            {{-- Secondary Filters --}}
            <div class="flex flex-wrap gap-2">
                {{-- Type --}}
                <select name="type" class="px-3 py-2 border border-outline-variant rounded-xl bg-surface text-label-sm text-on-surface focus:outline-none focus:border-primary transition-colors">
                    <option value="">Semua Tipe</option>
                    <option value="qr"     {{ request('type') === 'qr'     ? 'selected' : '' }}>Pesanan QR</option>
                    <option value="manual" {{ request('type') === 'manual' ? 'selected' : '' }}>Pesanan Manual</option>
                </select>

                {{-- Status --}}
                <select name="status" class="px-3 py-2 border border-outline-variant rounded-xl bg-surface text-label-sm text-on-surface focus:outline-none focus:border-primary transition-colors">
                    <option value="">Semua Status</option>
                    @foreach(['pending' => 'Tunggu Konfirmasi', 'preparing' => 'Disiapkan', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $val => $lbl)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>

                {{-- Payment Status --}}
                <select name="payment_status" class="px-3 py-2 border border-outline-variant rounded-xl bg-surface text-label-sm text-on-surface focus:outline-none focus:border-primary transition-colors">
                    <option value="">Semua Pembayaran</option>
                    <option value="paid"   {{ request('payment_status') === 'paid'   ? 'selected' : '' }}>Sudah Bayar</option>
                    <option value="pending"{{ request('payment_status') === 'pending'? 'selected' : '' }}>Menunggu</option>
                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                </select>

                @if(request()->hasAny(['search', 'type', 'status', 'payment_status']) || (request('period') && request('period') !== 'today'))
                    <a href="{{ route('cashier.orders.index') }}"
                       class="px-3 py-2 border border-red-200 bg-red-50 text-red-600 rounded-xl text-label-sm hover:bg-red-100 transition-colors">
                        Hapus Filter
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── Orders Table ─────────────────────────────────────────────────── --}}
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-outline-variant flex items-center justify-between bg-surface-container-low">
            <span class="text-label-md text-primary">{{ $orders->total() }} orders</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-outline-variant bg-surface-container-low/50">
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Invoice</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold hidden sm:table-cell">Tipe</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Meja</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold hidden md:table-cell">Pelanggan</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Total</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold hidden lg:table-cell">Pembayaran</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Status</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold hidden lg:table-cell">Tanggal</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="px-5 py-3">
                                <span class="text-label-md text-primary font-semibold">{{ $order->invoice_number }}</span>
                            </td>
                            <td class="px-5 py-3 hidden sm:table-cell">
                                <span class="text-[11px] px-2 py-1 rounded-full font-medium
                                    {{ $order->order_type === 'self' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700' }}">
                                    {{ $order->order_type_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-label-sm text-on-surface font-medium">{{ $order->table_number }}</span>
                            </td>
                            <td class="px-5 py-3 hidden md:table-cell">
                                <span class="text-label-sm text-on-surface-variant">{{ $order->customer_name ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-label-md text-primary font-semibold">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-3 hidden lg:table-cell">
                                @if($order->payment)
                                    <div class="flex flex-col gap-0.5">
                                        <span class="text-label-sm text-on-surface capitalize">{{ $order->payment->method }}</span>
                                        <span class="text-[11px] px-2 py-0.5 rounded-full font-medium inline-block w-fit
                                            {{ $order->payment->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                            {{ ucfirst($order->payment->status ?? 'pending') }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-label-sm text-on-surface-variant">Belum ada pembayaran</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @php
                                    $sc = match($order->status) {
                                        'pending'   => 'bg-amber-50 text-amber-700',
                                        'preparing' => 'bg-blue-50 text-blue-700',
                                        'completed' => 'bg-primary/10 text-primary',
                                        'cancelled' => 'bg-red-50 text-red-700',
                                        default     => 'bg-surface-container text-on-surface-variant',
                                    };
                                @endphp
                                <span class="text-[11px] px-2 py-1 rounded-full font-medium {{ $sc }} whitespace-nowrap">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 hidden lg:table-cell">
                                <p class="text-label-sm text-on-surface-variant">{{ $order->created_at->format('d M Y') }}</p>
                                <p class="text-[11px] text-on-surface-variant">{{ $order->created_at->format('H:i') }}</p>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('cashier.orders.show', $order) }}"
                                       class="p-1.5 rounded-lg bg-surface-container border border-outline-variant text-on-surface-variant hover:text-primary hover:border-primary transition-colors"
                                       title="View Detail">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    <a href="{{ route('cashier.receipt', $order) }}" target="_blank"
                                       class="p-1.5 rounded-lg bg-surface-container border border-outline-variant text-on-surface-variant hover:text-primary hover:border-primary transition-colors"
                                       title="Print Receipt">
                                        <span class="material-symbols-outlined text-[18px]">print</span>
                                    </a>
                                    @if(!in_array($order->status, ['completed', 'cancelled']))
                                        <form method="POST" action="{{ route('cashier.orders.cancel', $order) }}"
                                              onsubmit="return confirm('Batalkan pesanan {{ $order->invoice_number }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg bg-red-50 border border-red-200 text-red-600 hover:bg-red-600 hover:text-white transition-colors"
                                                title="Cancel Order">
                                                <span class="material-symbols-outlined text-[18px]">close</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-16 text-center">
                                <span class="material-symbols-outlined text-[56px] text-outline-variant mb-3 block">receipt_long</span>
                                <p class="text-body-md text-on-surface-variant">Tidak ada pesanan ditemukan.</p>
                                <p class="text-label-sm text-on-surface-variant mt-1">Coba sesuaikan filter Anda atau buat pesanan baru.</p>
                                <a href="{{ route('cashier.orders.create') }}"
                                   class="mt-4 inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-xl text-label-md hover:opacity-90 transition-opacity shadow-sm">
                                    <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                                    Pesanan Baru
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($orders->hasPages())
            <div class="px-5 py-4 border-t border-outline-variant">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

@endsection
