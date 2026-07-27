@extends('layouts.cashier')

@section('title', 'Order ' . $order->invoice_number)
@section('page-title', 'Detail Pesanan')

@section('content')

    {{-- ── Back + Header Actions ─────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ $backRoute }}"
               class="flex items-center gap-1.5 text-label-sm text-on-surface-variant hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                {{ $backLabel }}
            </a>
            <span class="text-outline-variant">/</span>
            <span class="text-label-sm text-primary font-semibold">{{ $order->invoice_number }}</span>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('cashier.receipt', $order) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-surface-container border border-outline-variant text-on-surface rounded-xl text-label-sm hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined text-[18px]">print</span>
                Cetak Struk
            </a>
            @if(!in_array($order->status, ['completed', 'cancelled']))
                <form method="POST" action="{{ route('cashier.orders.cancel', $order) }}"
                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 text-red-700 rounded-xl text-label-sm hover:bg-red-100 transition-colors">
                        <span class="material-symbols-outlined text-[18px]">cancel</span>
                        Batalkan Pesanan
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Left: Order Items ──────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Order Info Card --}}
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-outline-variant bg-surface-container-low flex items-center justify-between">
                    <h3 class="text-headline-md text-primary">Informasi Pesanan</h3>
                    @php
                        $sc = match($order->status) {
                            'pending'   => 'bg-amber-50 text-amber-700 border-amber-200',
                            'preparing' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'completed' => 'bg-primary/10 text-primary border-primary/20',
                            'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                            default     => 'bg-surface-container text-on-surface-variant border-outline-variant',
                        };
                    @endphp
                    <span class="text-label-sm px-3 py-1.5 rounded-full border font-medium {{ $sc }}">
                        <span class="material-symbols-outlined text-[16px] align-middle mr-1" style="font-variation-settings: 'FILL' 1;">{{ $order->status_icon }}</span>
                        {{ $order->status_label }}
                    </span>
                </div>
                <div class="px-5 py-4 grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <p class="text-label-sm text-on-surface-variant mb-1">Invoice</p>
                        <p class="text-label-md text-primary font-semibold">{{ $order->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-label-sm text-on-surface-variant mb-1">Meja</p>
                        <p class="text-label-md text-on-surface font-semibold">{{ $order->table_number }}</p>
                    </div>
                    <div>
                        <p class="text-label-sm text-on-surface-variant mb-1">Pelanggan</p>
                        <p class="text-label-md text-on-surface">{{ $order->customer_name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-label-sm text-on-surface-variant mb-1">Tipe Pesanan</p>
                        <span class="text-[11px] px-2 py-1 rounded-full font-medium
                            {{ $order->order_type === 'self' ? 'bg-amber-50 text-amber-700' : 'bg-blue-50 text-blue-700' }}">
                            {{ $order->order_type_label }}
                        </span>
                    </div>
                    <div>
                        <p class="text-label-sm text-on-surface-variant mb-1">Waktu Pesan</p>
                        <p class="text-label-md text-on-surface">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-label-sm text-on-surface-variant mb-1">Total Item</p>
                        <p class="text-label-md text-on-surface">{{ $order->total_items }} item</p>
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-outline-variant">
                    <h3 class="text-headline-md text-primary">Item Pesanan</h3>
                </div>
                <div class="divide-y divide-outline-variant">
                    @foreach ($order->items as $item)
                        <div class="flex gap-4 px-5 py-4">
                            {{-- Image --}}
                            @if($item->menuItem?->image)
                                <img src="{{ $item->menuItem->image }}" alt="{{ $item->menuItem->name }}"
                                     class="w-16 h-16 rounded-xl object-cover flex-shrink-0" />
                            @else
                                <div class="w-16 h-16 rounded-xl bg-surface-container flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-[28px] text-outline-variant">restaurant</span>
                                </div>
                            @endif
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-label-md text-primary font-semibold">{{ $item->menuItem?->name ?? 'Deleted Item' }}</p>
                                @if($item->notes)
                                    <p class="text-label-sm text-on-surface-variant mt-0.5 italic">Catatan: {{ $item->notes }}</p>
                                @endif
                                <p class="text-label-sm text-on-surface-variant mt-1">
                                    Rp{{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}
                                </p>
                            </div>
                            {{-- Subtotal --}}
                            <div class="flex-shrink-0 text-right">
                                <p class="text-label-md text-primary font-bold">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Right: Summary + Actions ──────────────────────────────────── --}}
        <div class="space-y-6">

            {{-- Order Summary --}}
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-outline-variant">
                    <h3 class="text-headline-md text-primary">Ringkasan Pesanan</h3>
                </div>
                <div class="px-5 py-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-label-sm text-on-surface-variant">Total Item</span>
                        <span class="text-label-md text-on-surface">{{ $order->total_items }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-label-sm text-on-surface-variant">Subtotal</span>
                        <span class="text-label-md text-on-surface">Rp{{ number_format($order->subtotal > 0 ? $order->subtotal : $order->total_price, 0, ',', '.') }}</span>
                    </div>
                    @if($order->tax_amount > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-label-sm text-on-surface-variant">PPN ({{ rtrim(rtrim(number_format($order->tax_percentage, 2), '0'), '.') }}%)</span>
                        <span class="text-label-md text-on-surface">Rp{{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="border-t border-outline-variant pt-3 flex items-center justify-between">
                        <span class="text-label-md text-on-surface font-bold">Total</span>
                        <span class="text-headline-md text-primary font-bold">Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Payment Info --}}
                @if($order->payment)
                    <div class="px-5 pb-5 pt-0 space-y-2 border-t border-outline-variant pt-4">
                        <p class="text-label-sm text-on-surface-variant font-semibold mb-2">Pembayaran</p>
                        <div class="flex items-center justify-between">
                            <span class="text-label-sm text-on-surface-variant">Metode</span>
                            <span class="text-label-sm text-on-surface font-medium uppercase">{{ $order->payment->method }}</span>
                        </div>
                        @if($order->payment->method === 'cash')
                            <div class="flex items-center justify-between">
                                <span class="text-label-sm text-on-surface-variant">Tunai Diterima</span>
                                <span class="text-label-sm text-on-surface">Rp{{ number_format($order->payment->amount, 0, ',', '.') }}</span>
                            </div>
                            @if($order->payment->change_amount)
                                <div class="flex items-center justify-between">
                                    <span class="text-label-sm text-on-surface-variant">Kembalian</span>
                                    <span class="text-label-md text-emerald-600 font-bold">Rp{{ number_format($order->payment->change_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        @endif
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-label-sm text-on-surface-variant">Status Pembayaran</span>
                            <span class="text-[11px] px-2 py-1 rounded-full font-medium
                                {{ $order->payment->status === 'paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $order->payment->status === 'paid' ? 'Lunas' : ucfirst($order->payment->status ?? 'menunggu') }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="px-5 pb-5 border-t border-outline-variant pt-4">
                        <p class="text-label-sm text-on-surface-variant italic">Belum ada pembayaran.</p>
                    </div>
                @endif
            </div>

            {{-- Update Status --}}
            @if(!in_array($order->status, ['completed', 'cancelled']))
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm p-5">
                    <h3 class="text-headline-md text-primary mb-4">Perbarui Status</h3>
                    <form method="POST" action="{{ route('cashier.orders.status', $order) }}">
                        @csrf @method('PATCH')
                        <div class="space-y-3">
                            @php
                                $transitions = match($order->status) {
                                    'pending'   => ['preparing' => 'Konfirmasi & Siapkan', 'cancelled' => 'Batalkan'],
                                    'preparing' => ['completed' => 'Tandai Selesai', 'cancelled' => 'Batalkan'],
                                    default     => [],
                                };
                            @endphp
                            @foreach($transitions as $status => $label)
                                <button type="submit" name="status" value="{{ $status }}"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-label-md transition-colors
                                        {{ $status === 'cancelled'
                                            ? 'bg-red-50 border border-red-200 text-red-700 hover:bg-red-100'
                                            : 'bg-primary text-on-primary hover:opacity-90 shadow-sm' }}">
                                    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">
                                        {{ match($status) {
                                            'preparing' => 'skillet',
                                            'completed' => 'task_alt',
                                            'cancelled' => 'cancel',
                                            default     => 'arrow_forward',
                                        } }}
                                    </span>
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </form>
                </div>
            @endif

            {{-- QR Order: confirm button if pending --}}
            @if($order->status === 'pending' && $order->order_type === 'self')
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-amber-600 text-[24px]" style="font-variation-settings: 'FILL' 1;">notification_important</span>
                        <div>
                            <p class="text-label-md text-amber-800 mb-1">Pesanan QR Baru</p>
                            <p class="text-label-sm text-amber-700">Pesanan ini dibuat oleh pelanggan melalui kode QR dan menunggu konfirmasi Anda.</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

@endsection
