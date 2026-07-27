{{-- ═══════════════════════════════════════════ --}}
{{-- Sheet 1: Order Review                       --}}
{{-- ═══════════════════════════════════════════ --}}
<div id="order-review-sheet" class="fixed inset-0 z-[100] pointer-events-none">
    {{-- Backdrop --}}
    <div id="order-review-backdrop" class="absolute inset-0 bg-black/0 transition-all duration-300"></div>

    {{-- Panel --}}
    <div id="order-review-panel" class="absolute bottom-0 left-0 w-full bg-surface rounded-t-[24px] shadow-[0px_-8px_32px_rgba(15,23,42,0.15)] translate-y-full transition-transform duration-300 ease-out max-h-[85vh] flex flex-col">
        {{-- Handle Bar --}}
        <div class="flex justify-center pt-3 pb-2 cursor-grab shrink-0">
            <div class="w-10 h-1 bg-outline-variant rounded-full"></div>
        </div>

        {{-- Header --}}
        <div class="flex items-center justify-between px-sm md:px-md pb-sm border-b border-outline-variant shrink-0">
            <h2 class="text-headline-md text-primary">Pesanan Anda</h2>
            <button id="close-review-btn" class="material-symbols-outlined text-on-surface-variant text-[24px] hover:text-primary transition-colors p-1">
                close
            </button>
        </div>

        {{-- Scrollable Content --}}
        <div class="flex-1 overflow-y-auto px-sm md:px-md py-sm space-y-3 min-h-0" id="order-review-items">
            {{-- Items will be injected by JS --}}
        </div>

        {{-- Empty State --}}
        <div id="order-review-empty" class="hidden flex-col items-center justify-center py-xl text-center px-sm shrink-0">
            <span class="material-symbols-outlined text-[48px] text-outline-variant mb-md">shopping_cart</span>
            <p class="text-headline-md text-on-surface-variant mb-2">Keranjang kosong</p>
            <p class="text-body-md text-outline">Tambahkan item dari menu untuk memulai pesanan.</p>
        </div>

        {{-- Tambah Item Lagi --}}
        <div class="px-sm md:px-md pt-1 pb-3 shrink-0" id="add-more-items-section">
            <button
                id="add-more-items-btn"
                class="w-full flex items-center justify-center gap-2 h-[44px] rounded-xl border-2 border-dashed border-primary/30 text-primary text-label-md hover:border-primary hover:bg-primary/5 active:scale-[0.98] transition-all duration-150 group"
            >
                <span class="material-symbols-outlined text-[20px] group-hover:rotate-90 transition-transform duration-200">add_circle</span>
                Tambah Item Lagi
            </button>
        </div>


        {{-- Footer --}}
        @php $taxRateValue = $taxRate ?? 0; @endphp
        <div class="border-t border-outline-variant px-sm md:px-md py-sm bg-surface shrink-0" id="order-review-footer"
             x-data="{ taxRate: {{ $taxRateValue }} }">
            <div class="space-y-2 mb-3">
                <div class="flex justify-between items-center">
                    <span class="text-body-sm text-on-surface-variant">Subtotal</span>
                    <span id="order-review-subtotal" class="text-label-md text-on-surface">Rp0</span>
                </div>
                @if($taxRateValue > 0)
                <div class="flex justify-between items-center">
                    <span class="text-body-sm text-on-surface-variant">PPN ({{ rtrim(rtrim(number_format($taxRateValue, 2), '0'), '.') }}%)</span>
                    <span id="order-review-tax" class="text-label-md text-on-surface">Rp0</span>
                </div>
                @endif
                <div class="flex justify-between items-center border-t border-outline-variant pt-2">
                    <span class="text-body-md text-on-surface-variant font-medium">Total</span>
                    <span id="order-review-total" class="text-headline-md text-primary font-bold">Rp0</span>
                </div>
            </div>
            <button
                id="place-order-btn"
                class="w-full bg-primary text-on-primary text-label-md h-[52px] rounded-full flex items-center justify-center gap-2 shadow-[0px_4px_12px_rgba(15,23,42,0.15)] hover:opacity-90 transition-all active:scale-[0.98] duration-150"
            >
                <span class="material-symbols-outlined text-[20px]">restaurant</span>
                Pesan Sekarang
            </button>
            <button
                id="place-order-loading"
                class="hidden w-full bg-primary/70 text-on-primary text-label-md h-[52px] rounded-full items-center justify-center gap-2 cursor-not-allowed"
                disabled
            >
                <span class="animate-spin material-symbols-outlined text-[20px]">progress_activity</span>
                Memproses...
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- Sheet 2: Mini Menu Picker (Tambah Item)    --}}
{{-- z-index lebih tinggi, sheet terpisah       --}}
{{-- ═══════════════════════════════════════════ --}}
<div id="mini-menu-sheet" class="fixed inset-0 z-[110] pointer-events-none">
    {{-- Backdrop --}}
    <div id="mini-menu-backdrop" class="absolute inset-0 bg-black/0 transition-all duration-300"></div>

    {{-- Panel --}}
    <div id="mini-menu-panel"
         class="absolute bottom-0 left-0 w-full bg-surface rounded-t-[24px] shadow-[0px_-8px_32px_rgba(15,23,42,0.15)] translate-y-full transition-transform duration-300 ease-out max-h-[90vh] flex flex-col">

        {{-- Handle --}}
        <div class="flex justify-center pt-3 pb-1 cursor-grab shrink-0">
            <div class="w-10 h-1 bg-outline-variant rounded-full"></div>
        </div>

        {{-- Header --}}
        <div class="flex items-center gap-3 px-sm md:px-md py-3 border-b border-outline-variant shrink-0">
            <button id="back-to-review-btn"
                    class="material-symbols-outlined text-on-surface-variant text-[24px] hover:text-primary transition-colors p-1">
                arrow_back
            </button>
            <div>
                <h2 class="text-headline-md text-primary">Tambah Item</h2>
                <p class="text-label-sm text-on-surface-variant">Pilih item yang ingin ditambahkan</p>
            </div>
        </div>

        {{-- Search --}}
        <div class="px-sm md:px-md pt-3 pb-2 shrink-0">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                <input
                    type="text"
                    id="mini-menu-search"
                    placeholder="Cari menu..."
                    class="w-full pl-10 pr-4 py-2.5 border border-outline-variant rounded-xl bg-surface-container-lowest text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors"
                />
            </div>
        </div>

        {{-- Menu Items List --}}
        <div class="flex-1 overflow-y-auto px-sm md:px-md pb-4 space-y-2 min-h-0" id="mini-menu-list">
            @foreach ($menuItems as $item)
                <div
                    class="mini-menu-item flex items-center gap-3 bg-surface-container-lowest rounded-xl border border-outline-variant p-3 transition-colors hover:border-primary/30"
                    data-item-id="{{ $item['id'] }}"
                    data-name="{{ strtolower($item['name']) }}"
                    data-price="{{ $item['price'] }}"
                    data-display-name="{{ $item['name'] }}"
                    data-image="{{ $item['image'] }}"
                >
                    <img
                        src="{{ $item['image'] }}"
                        alt="{{ $item['name'] }}"
                        class="w-14 h-14 rounded-xl object-cover flex-shrink-0"
                        loading="lazy"
                    />
                    <div class="flex-1 min-w-0">
                        <p class="text-label-md text-primary truncate">{{ $item['name'] }}</p>
                        <p class="text-label-sm text-on-surface-variant">Rp{{ number_format($item['price'], 0, ',', '.') }}</p>
                    </div>
                    {{-- Qty Controls --}}
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button class="mini-qty-minus material-symbols-outlined text-[18px] text-on-surface-variant w-8 h-8 items-center justify-center rounded-full bg-surface-container hover:bg-surface-variant active:scale-95 transition-colors hidden"
                                data-item-id="{{ $item['id'] }}">remove</button>
                        <span class="mini-qty-display text-label-md font-bold text-primary min-w-[20px] text-center hidden"
                              data-item-id="{{ $item['id'] }}">0</span>
                        <button class="mini-qty-plus material-symbols-outlined text-[22px] text-white w-9 h-9 flex items-center justify-center rounded-full bg-primary hover:opacity-90 active:scale-95 transition-opacity shadow-sm"
                                data-item-id="{{ $item['id'] }}">add</button>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
