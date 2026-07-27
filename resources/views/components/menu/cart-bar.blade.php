{{-- Floating Cart / Order Bar --}}
<div id="cart-bar" class="fixed bottom-0 left-0 w-full z-50 p-sm md:p-md bg-surface/80 backdrop-blur-md border-t border-outline-variant shadow-[0px_-4px_12px_rgba(15,23,42,0.05)] flex justify-center transition-all duration-300 translate-y-full">
    <button
        id="view-order-btn"
        class="w-full max-w-sm bg-primary text-on-primary text-label-md h-[56px] rounded-full flex items-center justify-between px-6 shadow-[0px_4px_12px_rgba(15,23,42,0.15)] hover:opacity-90 transition-opacity active:scale-[0.98] duration-150"
    >
        <span class="flex items-center gap-2">
            <span id="cart-item-count" class="bg-on-primary text-primary w-6 h-6 rounded-full flex items-center justify-center font-bold text-[12px]">0</span>
            Items
        </span>
        <span id="cart-total-price">Rp0</span>
        <span class="flex items-center gap-1">
            View Order
            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
        </span>
    </button>
</div>
