@extends('layouts.cashier')

@section('title', 'Pesanan Manual Baru')
@section('page-title', 'Pesanan Manual Baru (POS)')

@section('content')

<div x-data="posSystem()" x-init="init()" class="flex flex-col lg:flex-row gap-6 h-full">

    {{-- ════════════════════════════════════════════════════
         LEFT PANEL — Menu Selection
    ════════════════════════════════════════════════════ --}}
    <div class="flex-1 min-w-0 flex flex-col gap-4 bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 shadow-sm">

        {{-- Search + Category Filter --}}
        <div>
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Search --}}
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                    <input type="text"
                           x-model="search"
                           placeholder="Cari item menu…"
                           class="w-full pl-10 pr-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors" />
                </div>
            </div>

            {{-- Category Pills --}}
            <div class="flex gap-2 mt-3 flex-wrap">
                <button @click="activeCategory = null"
                        :class="activeCategory === null ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'"
                        class="px-4 py-1.5 rounded-full text-label-sm font-medium transition-colors">
                    All
                </button>
                @foreach ($categories as $cat)
                    <button @click="activeCategory = {{ $cat->id }}"
                            :class="activeCategory === {{ $cat->id }} ? 'bg-primary text-on-primary' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high'"
                            class="px-4 py-1.5 rounded-full text-label-sm font-medium transition-colors">
                        @if($cat->icon)<span class="material-symbols-outlined text-[16px] align-middle mr-1">{{ $cat->icon }}</span>@endif
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Menu Cards Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 overflow-y-auto pr-1 flex-1 content-start" style="min-height: 400px;">
            @foreach ($menuItems as $item)
                @php
                    $itemId    = $item->id;
                    $itemName  = json_encode($item->name);
                    $itemPrice = (int) $item->price;  // Price in full Rupiah — Alpine formatNumber() shows as-is
                    $itemImage = json_encode($item->image ?? '');
                    $isUnavail = $item->status === 'unavailable';
                    $catId     = $item->category_id ?? 'null';
                @endphp

                <div x-show="matchesFilter({{ $itemId }}, {{ $catId }}, {{ $itemName }})"
                     class="relative bg-white rounded-2xl border border-gray-100 overflow-hidden flex flex-col transition-all duration-200 group
                            {{ $isUnavail ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:shadow-md hover:-translate-y-0.5' }}"
                     style="height: 200px; box-shadow: 0 1px 4px rgba(0,0,0,0.07);"
                     @if(!$isUnavail)
                         @click="addToCart({{ $itemId }}, {{ $itemName }}, {{ $itemPrice }}, {{ $itemImage }})"
                     @endif>

                    {{-- Qty badge --}}
                    <span x-show="cart.find(i => i.id === {{ $itemId }})"
                          x-text="cart.find(i => i.id === {{ $itemId }})?.quantity"
                          class="absolute top-2 right-2 z-20 bg-primary text-on-primary text-[10px] font-bold min-w-[20px] h-[20px] px-1 rounded-full flex items-center justify-center shadow"
                          style="display:none;">
                    </span>

                    {{-- Image — 120px --}}
                    <div class="relative overflow-hidden flex-shrink-0" style="height: 120px;">
                        @if($item->image)
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                 src="{{ $item->image }}" alt="{{ $item->name }}" loading="lazy" />
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                <span class="material-symbols-outlined text-[40px] text-gray-300">restaurant</span>
                            </div>
                        @endif

                        {{-- Gradient overlay bawah --}}
                        <div class="absolute inset-x-0 bottom-0 h-6 bg-gradient-to-t from-black/10 to-transparent"></div>

                        @if($item->popular)
                            <span class="absolute top-2 left-2 z-10 bg-orange-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full flex items-center gap-0.5 shadow-sm">
                                <span class="material-symbols-outlined text-[11px]">local_fire_department</span>
                                Terlaris
                            </span>
                        @endif

                        @if($isUnavail)
                            <div class="absolute inset-0 bg-white/70 flex items-center justify-center">
                                <span class="text-[11px] text-gray-500 font-semibold bg-white/80 px-2 py-0.5 rounded-full border border-gray-200">Tidak Tersedia</span>
                            </div>
                        @endif
                    </div>

                    {{-- Details — 80px --}}
                    <div class="flex flex-col justify-between flex-1 px-3 py-2">
                        {{-- Name --}}
                        <p class="text-[12px] font-semibold text-gray-800 leading-snug line-clamp-2">{{ $item->name }}</p>

                        {{-- Price row --}}
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-[13px] font-bold text-primary">Rp{{ number_format($itemPrice, 0, ',', '.') }}</span>

                            @if(!$isUnavail)
                                <div class="w-7 h-7 rounded-full bg-primary flex items-center justify-center shadow-sm group-hover:scale-110 group-hover:shadow-md transition-all duration-200 flex-shrink-0">
                                    <span class="material-symbols-outlined text-white text-[16px]" style="font-variation-settings:'wght' 600;">add</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    {{-- ════════════════════════════════════════════════════
         RIGHT PANEL — Cart + Order Info
    ════════════════════════════════════════════════════ --}}
    <div class="w-full lg:w-96 xl:w-[420px] flex flex-col gap-4 flex-shrink-0">

        {{-- Cart --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b border-outline-variant flex items-center justify-between bg-surface-container-low">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-primary" style="font-variation-settings: 'FILL' 1;">shopping_cart</span>
                    <h3 class="text-headline-md text-primary">Keranjang</h3>
                    <span x-show="cart.length > 0" class="bg-primary text-on-primary text-[11px] font-bold px-2 py-0.5 rounded-full" x-text="totalItems"></span>
                </div>
                <button @click="clearCart()" x-show="cart.length > 0"
                        class="text-label-sm text-red-600 hover:underline">Kosongkan</button>
            </div>

            {{-- Cart Items --}}
            <div class="overflow-y-auto" style="max-height: 300px;">
                <template x-if="cart.length === 0">
                    <div class="px-5 py-10 text-center">
                        <span class="material-symbols-outlined text-[48px] text-outline-variant mb-2 block">shopping_cart</span>
                        <p class="text-label-sm text-on-surface-variant">Keranjang kosong</p>
                        <p class="text-[11px] text-on-surface-variant mt-1">Klik item menu untuk menambahkannya</p>
                    </div>
                </template>
                <div class="divide-y divide-outline-variant">
                    <template x-for="(item, index) in cart" :key="item.id">
                        <div class="px-4 py-3">
                            <div class="flex items-start gap-3">
                                <template x-if="item.image">
                                    <img :src="item.image" :alt="item.name"
                                         class="w-12 h-12 rounded-lg object-cover flex-shrink-0 bg-surface-container" />
                                </template>
                                <template x-if="!item.image">
                                    <div class="w-12 h-12 rounded-lg bg-surface-container flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-[20px] text-outline-variant">restaurant</span>
                                    </div>
                                </template>
                                <div class="flex-1 min-w-0">
                                    <p class="text-label-md text-on-surface truncate" x-text="item.name"></p>
                                    <p class="text-label-sm text-primary" x-text="'Rp' + formatNumber(item.price)"></p>

                                    {{-- Notes --}}
                                    <input type="text" x-model="item.notes"
                                           placeholder="Catatan (opsional)…"
                                           class="mt-1.5 w-full text-[12px] px-2.5 py-1 border border-outline-variant rounded-lg bg-surface focus:outline-none focus:border-primary transition-colors" />
                                </div>
                                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                                    <button @click="removeItem(index)"
                                            class="p-1 rounded-lg text-red-500 hover:bg-red-50 transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                    {{-- Qty controls --}}
                                    <div class="flex items-center gap-1.5">
                                        <button @click="decreaseQty(index)"
                                                class="w-7 h-7 rounded-lg bg-surface-container text-on-surface flex items-center justify-center hover:bg-surface-container-high transition-colors">
                                            <span class="material-symbols-outlined text-[16px]">remove</span>
                                        </button>
                                        <span class="w-6 text-center text-label-md text-on-surface font-semibold" x-text="item.quantity"></span>
                                        <button @click="increaseQty(index)"
                                                class="w-7 h-7 rounded-lg bg-primary text-on-primary flex items-center justify-center hover:opacity-90 transition-opacity">
                                            <span class="material-symbols-outlined text-[16px]">add</span>
                                        </button>
                                    </div>
                                    <span class="text-label-sm text-primary font-bold" x-text="'Rp' + formatNumber(item.price * item.quantity)"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Totals --}}
            <div x-show="cart.length > 0" class="px-5 py-3 border-t border-outline-variant bg-surface-container-low/50 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-label-sm text-on-surface-variant">Total Item</span>
                    <span class="text-label-sm text-on-surface" x-text="totalItems + ' items'"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-label-sm text-on-surface-variant">Subtotal</span>
                    <span class="text-label-sm text-on-surface" x-text="'Rp' + formatNumber(subtotal)"></span>
                </div>
                <div x-show="taxRate > 0" class="flex items-center justify-between">
                    <span class="text-label-sm text-on-surface-variant">PPN (<span x-text="taxRate"></span>%)</span>
                    <span class="text-label-sm text-on-surface" x-text="'Rp' + formatNumber(taxAmount)"></span>
                </div>
                <div class="flex items-center justify-between border-t border-outline-variant pt-2">
                    <span class="text-label-md text-on-surface font-bold">Total</span>
                    <span class="text-headline-md text-primary font-bold" x-text="'Rp' + formatNumber(totalPrice)"></span>
                </div>
            </div>
        </div>

        {{-- ── Order Form ──────────────────────────────────────────────── --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm p-5">
            <h3 class="text-headline-md text-primary mb-4">Detail Pesanan</h3>

            <form id="pos-form" method="POST" action="{{ route('cashier.orders.store') }}" @submit.prevent="submitOrder">
                @csrf

                {{-- Hidden cart fields (populated by Alpine) --}}
                <div id="cart-fields"></div>

                {{-- Customer Name --}}
                <div class="mb-4">
                    <label class="text-label-sm text-on-surface-variant mb-1 block">Nama Pelanggan <span class="text-on-surface-variant">(opsional)</span></label>
                    <input type="text" name="customer_name" x-model="customerName"
                           placeholder="Masukkan nama pelanggan…"
                           class="w-full px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors" />
                </div>

                {{-- Order Type --}}
                <div class="mb-4">
                    <label class="text-label-sm text-on-surface-variant mb-2 block">Tipe Pesanan</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label :class="orderType === 'dine_in' ? 'border-primary bg-primary/5 text-primary' : 'border-outline-variant text-on-surface-variant'"
                               class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-all">
                            <input type="radio" name="order_type" value="dine_in" x-model="orderType" class="hidden" />
                            <span class="material-symbols-outlined text-[20px]" :style="orderType === 'dine_in' ? 'font-variation-settings: \'FILL\' 1;' : ''">table_restaurant</span>
                            <span class="text-label-sm font-medium">Dine In</span>
                        </label>
                        <label :class="orderType === 'take_away' ? 'border-primary bg-primary/5 text-primary' : 'border-outline-variant text-on-surface-variant'"
                               class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-all">
                            <input type="radio" name="order_type" value="take_away" x-model="orderType" class="hidden" />
                            <span class="material-symbols-outlined text-[20px]" :style="orderType === 'take_away' ? 'font-variation-settings: \'FILL\' 1;' : ''">takeout_dining</span>
                            <span class="text-label-sm font-medium">Bawa Pulang</span>
                        </label>
                    </div>
                </div>

                {{-- Table Selection (Dine In only) --}}
                <div x-show="orderType === 'dine_in'" x-transition class="mb-4">
                    <label class="text-label-sm text-on-surface-variant mb-1 block">Meja <span class="text-red-500">*</span></label>
                    <select name="restaurant_table_id" x-model="selectedTable"
                            class="w-full px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors">
                        <option value="">Pilih meja…</option>
                        @foreach ($tables as $table)
                            <option value="{{ $table->id }}"
                                    @if($table->status === 'occupied') disabled @endif>
                                Meja {{ $table->table_number }} ({{ match($table->status) {'available' => 'Tersedia', 'occupied' => 'Terisi', 'disabled' => 'Dinonaktifkan', default => ucfirst($table->status)} }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Payment Method --}}
                <div class="mb-4">
                    <label class="text-label-sm text-on-surface-variant mb-2 block">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label :class="paymentMethod === 'cash' ? 'border-primary bg-primary/5 text-primary' : 'border-outline-variant text-on-surface-variant'"
                               class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-all">
                            <input type="radio" name="payment_method" value="cash" x-model="paymentMethod" class="hidden" />
                            <span class="material-symbols-outlined text-[20px]" :style="paymentMethod === 'cash' ? 'font-variation-settings: \'FILL\' 1;' : ''">payments</span>
                            <span class="text-label-sm font-medium">Tunai</span>
                        </label>
                        <label :class="paymentMethod === 'qris' ? 'border-primary bg-primary/5 text-primary' : 'border-outline-variant text-on-surface-variant'"
                               class="flex items-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-all">
                            <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="hidden" />
                            <span class="material-symbols-outlined text-[20px]" :style="paymentMethod === 'qris' ? 'font-variation-settings: \'FILL\' 1;' : ''">qr_code</span>
                            <span class="text-label-sm font-medium">QRIS</span>
                        </label>
                    </div>
                </div>

                {{-- Cash Amount (Cash only) --}}
                <div x-show="paymentMethod === 'cash'" x-transition class="mb-4 space-y-2">
                    <label class="text-label-sm text-on-surface-variant mb-1 block">Uang Diterima <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-label-sm font-medium">Rp</span>
                        <input type="number" name="cash_amount" x-model.number="cashAmount" step="1000" min="0"
                               placeholder="0"
                               class="w-full pl-10 pr-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors" />
                    </div>
                    <div class="flex items-center justify-between p-3 rounded-xl"
                         :class="change >= 0 ? 'bg-emerald-50 border border-emerald-200' : 'bg-red-50 border border-red-200'">
                        <span class="text-label-sm" :class="change >= 0 ? 'text-emerald-700' : 'text-red-700'">
                            <span x-text="change >= 0 ? 'Kembalian' : 'Kurang'"></span>
                        </span>
                        <span class="text-label-md font-bold" :class="change >= 0 ? 'text-emerald-700' : 'text-red-700'"
                              x-text="'Rp' + formatNumber(Math.abs(change))"></span>
                    </div>

                    {{-- Quick cash buttons --}}
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="amt in quickCashAmounts" :key="amt">
                            <button type="button" @click="cashAmount = amt"
                                    class="px-3 py-1.5 bg-surface-container border border-outline-variant text-label-sm text-on-surface rounded-lg hover:bg-surface-container-high transition-colors"
                                    x-text="'Rp' + formatNumber(amt)">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- QRIS Info --}}
                <div x-show="paymentMethod === 'qris'" x-transition class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-blue-600 text-[20px]" style="font-variation-settings: 'FILL' 1;">qr_code</span>
                        <span class="text-label-md text-blue-800">QRIS Payment</span>
                    </div>
                    <p class="text-label-sm text-blue-700">Tunjukkan kode QRIS ke pelanggan dan konfirmasi pembayaran setelah pemindaian.</p>
                </div>

                {{-- Error messages --}}
                <div x-show="errors.length > 0" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl space-y-1">
                    <template x-for="err in errors" :key="err">
                        <p class="text-label-sm text-red-700" x-text="err"></p>
                    </template>
                </div>

                {{-- Action Buttons --}}
                <div class="space-y-2">
                    <button type="button" @click="submitOrder()"
                            :disabled="cart.length === 0 || loading"
                            :class="cart.length === 0 || loading ? 'opacity-50 cursor-not-allowed' : 'hover:opacity-90'"
                            class="w-full flex items-center justify-center gap-2 bg-primary text-on-primary py-3 rounded-xl text-label-md font-semibold shadow-sm transition-opacity">
                        <span x-show="!loading" class="material-symbols-outlined text-[20px]">receipt_long</span>
                        <span x-show="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <span x-text="loading ? 'Memproses…' : 'Buat Pesanan'"></span>
                    </button>
                    <button type="button" @click="clearCart()"
                            class="w-full flex items-center justify-center gap-2 py-2.5 border border-outline-variant text-on-surface-variant rounded-xl text-label-sm hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                        Reset Keranjang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function posSystem() {
    return {
        cart: [],
        search: '',
        activeCategory: null,
        taxRate: {{ \App\Models\Setting::getTaxRate() }},
        customerName: '',
        orderType: 'dine_in',
        selectedTable: '',
        paymentMethod: 'cash',
        cashAmount: 0,
        errors: [],
        loading: false,

        get totalItems() {
            return this.cart.reduce((sum, item) => sum + item.quantity, 0);
        },
        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        },
        get taxAmount() {
            return Math.round(this.subtotal * (this.taxRate / 100));
        },
        get totalPrice() {
            return this.subtotal + this.taxAmount;
        },
        get change() {
            return this.cashAmount - this.totalPrice;
        },
        get quickCashAmounts() {
            const t = this.totalPrice;
            const amounts = [];
            if (t <= 0) return [10000, 20000, 50000, 100000];
            const rounded = [
                Math.ceil(t / 1000) * 1000,
                Math.ceil(t / 5000) * 5000,
                Math.ceil(t / 10000) * 10000,
                Math.ceil(t / 50000) * 50000,
                Math.ceil(t / 100000) * 100000,
            ];
            return [...new Set(rounded)].filter(a => a >= t).slice(0, 4);
        },

        init() {
            // Nothing to init
        },

        matchesFilter(id, categoryId, name) {
            const matchCat = this.activeCategory === null || categoryId == this.activeCategory;
            const matchSearch = !this.search || name.toLowerCase().includes(this.search.toLowerCase());
            return matchCat && matchSearch;
        },

        addToCart(id, name, price, image) {
            const existing = this.cart.findIndex(i => i.id === id);
            if (existing >= 0) {
                this.cart[existing].quantity++;
            } else {
                this.cart.push({ id, name, price, image, quantity: 1, notes: '' });
            }
        },

        removeItem(index) {
            this.cart.splice(index, 1);
        },

        increaseQty(index) {
            if (this.cart[index].quantity < 99) this.cart[index].quantity++;
        },

        decreaseQty(index) {
            if (this.cart[index].quantity > 1) {
                this.cart[index].quantity--;
            } else {
                this.cart.splice(index, 1);
            }
        },

        clearCart() {
            this.cart = [];
            this.customerName = '';
            this.selectedTable = '';
            this.cashAmount = 0;
            this.errors = [];
        },

        formatNumber(n) {
            return Math.round(n).toLocaleString('id-ID');
        },

        validate() {
            const errs = [];
            if (this.cart.length === 0) errs.push('Please add at least one item to the cart.');
            if (this.orderType === 'dine_in' && !this.selectedTable) errs.push('Please select a table for Dine In orders.');
            if (this.paymentMethod === 'cash' && this.cashAmount < this.totalPrice) {
                errs.push('Cash received must be at least Rp' + this.formatNumber(this.totalPrice) + '.');
            }
            return errs;
        },

        async submitOrder() {
            this.errors = this.validate();
            if (this.errors.length > 0) return;

            this.loading = true;

            const fd = new FormData();
            fd.append('customer_name', this.customerName);
            fd.append('order_type', this.orderType);
            fd.append('payment_method', this.paymentMethod);
            if (this.orderType === 'dine_in') fd.append('restaurant_table_id', this.selectedTable);
            if (this.paymentMethod === 'cash') fd.append('cash_amount', this.cashAmount);

            this.cart.forEach((item, i) => {
                fd.append(`items[${i}][menu_item_id]`, item.id);
                fd.append(`items[${i}][quantity]`, item.quantity);
                fd.append(`items[${i}][notes]`, item.notes || '');
            });

            try {
                const response = await fetch('{{ route('cashier.orders.store') }}', {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: fd,
                    redirect: 'follow',
                });

                if (response.redirected) {
                    window.location.href = response.url;
                } else if (response.ok) {
                    const data = await response.json().catch(() => null);
                    if (data?.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    const data = await response.json().catch(() => null);
                    if (data?.errors) {
                        this.errors = Object.values(data.errors).flat();
                    } else if (data?.message) {
                        this.errors = [data.message];
                    } else {
                        this.errors = ['An error occurred. Please try again.'];
                    }
                }
            } catch (e) {
                this.errors = ['Network error. Please check your connection.'];
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
@endpush
