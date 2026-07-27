@extends('layouts.app')

@section('title', 'Pesanan ' . $order->invoice_number . ' — Lumiere Dining')

@section('content')
    <div class="min-h-screen bg-surface flex flex-col items-center px-margin-mobile md:px-margin-desktop py-lg">

        {{-- Success Animation --}}
        <div class="confirmation-entrance mb-md">
            <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-md animate-confirm-pop">
                <span class="material-symbols-outlined text-[40px] text-primary" style="font-variation-settings: 'FILL' 1;">
                    {{ $order->status === 'cancelled' ? 'cancel' : 'check_circle' }}
                </span>
            </div>
            <h1 class="text-headline-lg-mobile md:text-headline-lg text-primary text-center mb-1">
                {{ $order->status === 'cancelled' ? 'Pesanan Dibatalkan' : 'Pesanan Berhasil!' }}
            </h1>
            <p class="text-body-md text-on-surface-variant text-center">
                Pesanan {{ $order->invoice_number }} &mdash; Meja {{ $order->table_number }}
            </p>
        </div>

        {{-- Status Badge --}}
        <div class="flex items-center gap-2 px-4 py-2 rounded-full mb-md
            {{ match($order->status) {
                'pending'   => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                'preparing' => 'bg-blue-50 text-blue-700 border border-blue-200',
                'completed' => 'bg-primary/10 text-primary border border-primary/20',
                'cancelled' => 'bg-red-50 text-red-700 border border-red-200',
                default     => 'bg-surface-container text-on-surface-variant',
            } }}
        ">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">
                {{ $order->status_icon }}
            </span>
            <span class="text-label-md">{{ $order->status_label }}</span>
        </div>

        {{-- Order Card --}}
        <div class="w-full max-w-md bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-[0px_4px_16px_rgba(15,23,42,0.06)] overflow-hidden">
            {{-- Items --}}
            <div class="divide-y divide-outline-variant">
                @foreach ($order->items as $orderItem)
                    <div class="flex items-start gap-3 px-sm py-3">
                        <img
                            src="{{ $orderItem->menuItem->image }}"
                            alt="{{ $orderItem->menuItem->name }}"
                            class="w-14 h-14 rounded-xl object-cover flex-shrink-0"
                            loading="lazy"
                        />
                        <div class="flex-1 min-w-0">
                            <p class="text-label-md text-primary truncate">{{ $orderItem->menuItem->name }}</p>
                            <p class="text-label-sm text-on-surface-variant">x{{ $orderItem->quantity }}</p>
                            @if($orderItem->notes)
                                <p class="text-[11px] text-on-surface-variant/70 mt-0.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">edit_note</span>
                                    {{ $orderItem->notes }}
                                </p>
                            @endif
                        </div>
                        <span class="text-label-md text-primary flex-shrink-0">
                            Rp{{ number_format($orderItem->subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Notes --}}
            @if ($order->notes)
                <div class="px-sm py-3 border-t border-outline-variant bg-surface-container-low">
                    <p class="text-label-sm text-on-surface-variant mb-1">Catatan</p>
                    <p class="text-body-md text-on-surface">{{ $order->notes }}</p>
                </div>
            @endif

            {{-- Total Breakdown --}}
            <div class="border-t border-outline-variant bg-surface-container">
                <div class="px-sm py-3 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-label-sm text-on-surface-variant">Subtotal</span>
                        <span class="text-label-md text-on-surface">
                            Rp{{ number_format($order->subtotal > 0 ? $order->subtotal : $order->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                    @if($order->tax_amount > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-label-sm text-on-surface-variant">PPN ({{ rtrim(rtrim(number_format($order->tax_percentage, 2), '0'), '.') }}%)</span>
                            <span class="text-label-md text-on-surface">
                                Rp{{ number_format($order->tax_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="flex justify-between items-center px-sm py-sm border-t border-outline-variant">
                    <span class="text-label-md text-on-surface-variant">Total</span>
                    <span class="text-headline-md text-primary font-bold">
                        Rp{{ number_format($order->total_price, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="w-full max-w-md mt-md space-y-3">
            <a
                href="{{ $backUrl }}"
                class="w-full bg-primary text-on-primary text-label-md h-[52px] rounded-full flex items-center justify-center gap-2 shadow-[0px_4px_12px_rgba(15,23,42,0.15)] hover:opacity-90 transition-all active:scale-[0.98] duration-150"
            >
                <span class="material-symbols-outlined text-[20px]">restaurant_menu</span>
                {{ $order->restaurantTable ? 'Pesan Lagi' : 'Kembali ke Menu' }}
            </a>
        </div>

        {{-- Timestamp --}}
        <p class="text-label-sm text-outline mt-md">
            {{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
        </p>
    </div>
@endsection
