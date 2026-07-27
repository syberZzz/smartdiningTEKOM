@extends('layouts.cashier')

@section('title', 'Pesanan QR')
@section('page-title', 'Pesanan QR')

@section('content')

    {{-- ── Status Stats Bar ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        @foreach([
            ['label' => 'Menunggu',   'count' => $stats['pending'],   'color' => 'amber',  'icon' => 'schedule'],
            ['label' => 'Disiapkan',  'count' => $stats['preparing'], 'color' => 'blue',   'icon' => 'skillet'],
        ] as $s)
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-[18px] text-{{ $s['color'] }}-600" style="font-variation-settings: 'FILL' 1;">{{ $s['icon'] }}</span>
                    <span class="text-label-sm text-on-surface-variant">{{ $s['label'] }}</span>
                </div>
                <p class="text-2xl font-bold text-{{ $s['color'] }}-600">{{ $s['count'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- ── Filter Bar ────────────────────────────────────────────────────── --}}
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('cashier.qr-orders.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari invoice, meja, pelanggan…"
                       class="w-full pl-10 pr-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors" />
            </div>

            <select name="status" class="px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors">
                <option value="">Semua Status</option>
                @foreach(['pending' => 'Tunggu Konfirmasi', 'preparing' => 'Disiapkan', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $val => $lbl)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
            </select>

            <select name="date" class="px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors">
                <option value="today" {{ request('date', 'today') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                <option value="week"  {{ request('date') === 'week'  ? 'selected' : '' }}>Minggu Ini</option>
                <option value="month" {{ request('date') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                <option value=""      {{ request('date') === ''      ? 'selected' : '' }}>Semua Waktu</option>
            </select>

            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary/10 text-primary rounded-xl text-label-md hover:bg-primary/20 transition-colors">
                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                Filter
            </button>
        </form>
    </div>

    {{-- ── Orders Table ──────────────────────────────────────────────────── --}}
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden"
         x-data="qrOrderPoller()" x-init="start()">

        {{-- Auto-refresh indicator --}}
        <div class="px-5 py-3 border-b border-outline-variant flex items-center justify-between bg-surface-container-low">
            <div class="flex items-center gap-2">
                <h3 class="text-label-md text-primary">Pesanan QR</h3>
                <span class="text-label-sm text-on-surface-variant">({{ $orders->total() }} total)</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5 text-label-sm text-emerald-600">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    Live
                </div>
                <a href="{{ route('cashier.orders.create') }}"
                   class="inline-flex items-center gap-1.5 text-label-sm text-primary hover:underline">
                    <span class="material-symbols-outlined text-[16px]">add_shopping_cart</span>
                    Pesanan Manual Baru
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-outline-variant bg-surface-container-low/50">
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Invoice</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Meja</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold hidden md:table-cell">Pelanggan</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold hidden sm:table-cell">Item</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Total</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold hidden lg:table-cell">Pembayaran</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold hidden lg:table-cell">Waktu</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Status</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant" id="qr-orders-tbody">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="px-5 py-3">
                                <span class="text-label-md text-primary font-semibold">{{ $order->invoice_number }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-label-md text-on-surface font-medium">{{ $order->table_number }}</span>
                            </td>
                            <td class="px-5 py-3 hidden md:table-cell">
                                <span class="text-label-sm text-on-surface-variant">{{ $order->customer_name ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3 hidden sm:table-cell">
                                <span class="text-label-sm text-on-surface">{{ $order->total_items }} item</span>
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
                                    <span class="text-label-sm text-on-surface-variant">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 hidden lg:table-cell">
                                <span class="text-label-sm text-on-surface-variant">{{ $order->created_at->format('H:i') }}</span>
                                <p class="text-[11px] text-on-surface-variant">{{ $order->created_at->diffForHumans() }}</p>
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
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('cashier.qr-orders.show', $order) }}"
                                       class="p-1.5 rounded-lg bg-surface-container border border-outline-variant text-on-surface-variant hover:text-primary hover:border-primary transition-colors"
                                       title="View Detail">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                    @if($order->status === 'pending')
                                        <form method="POST" action="{{ route('cashier.qr-orders.confirm', $order) }}">
                                            @csrf
                                            <button type="submit"
                                                class="p-1.5 rounded-lg bg-primary/10 border border-primary/30 text-primary hover:bg-primary hover:text-on-primary transition-colors"
                                                title="Confirm Order">
                                                <span class="material-symbols-outlined text-[18px]">check</span>
                                            </button>
                                        </form>
                                    @endif
                                    @if(!in_array($order->status, ['completed', 'cancelled']))
                                        <form method="POST" action="{{ route('cashier.qr-orders.cancel', $order) }}"
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
                                <span class="material-symbols-outlined text-[56px] text-outline-variant mb-3 block">qr_code_scanner</span>
                                <p class="text-body-md text-on-surface-variant">Tidak ada pesanan QR ditemukan.</p>
                                <p class="text-label-sm text-on-surface-variant mt-1">Pesanan dari pelanggan yang memindai kode QR akan muncul di sini.</p>
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

@push('scripts')
<script>
function qrOrderPoller() {
    return {
        lastCheck: new Date().toISOString(),
        pendingCount: {{ $stats['pending'] }},

        start() {
            // Poll every 15 seconds for new pending orders
            setInterval(() => this.poll(), 15000);
        },

        async poll() {
            try {
                const res = await fetch(`{{ route('cashier.qr-orders.poll') }}?since=${encodeURIComponent(this.lastCheck)}`);
                const data = await res.json();

                this.lastCheck = data.server_time;

                // If there are new pending orders, show a notification and update the badge
                if (data.pending_count !== this.pendingCount) {
                    const diff = data.pending_count - this.pendingCount;
                    this.pendingCount = data.pending_count;

                    if (diff > 0) {
                        this.showNotification(diff);
                    }
                }
            } catch (e) {
                // Silent fail — network hiccup shouldn't interrupt cashier
            }
        },

        showNotification(count) {
            const msg = count === 1
                ? 'New QR order received!'
                : `${count} new QR orders received!`;

            // Simple toast using Alpine
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 right-4 z-50 bg-amber-500 text-white px-5 py-3 rounded-xl shadow-xl text-label-md flex items-center gap-2 animate-bounce';
            toast.innerHTML = `<span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">notification_important</span>${msg}`;
            document.body.appendChild(toast);

            setTimeout(() => toast.remove(), 5000);

            // Optionally reload after a delay so the table refreshes
            setTimeout(() => {
                if (!document.hidden) window.location.reload();
            }, 5000);
        },
    };
}
</script>
@endpush
