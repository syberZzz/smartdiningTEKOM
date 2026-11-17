{{-- Top App Bar --}}
<header class="fixed top-0 w-full z-50 bg-surface shadow-[0px_4px_12px_rgba(15,23,42,0.05)]">
    <div class="flex justify-between items-center px-margin-mobile md:px-margin-desktop py-base w-full max-w-7xl mx-auto h-[64px]">
        {{-- Logo --}}
        <div class="flex items-center gap-xs">
            <img src="{{ asset('images/logo.png') }}" alt="Smart Dining Logo" class="h-8 w-8 object-contain" />
            <h1 class="text-headline-md font-bold text-primary">Smart Dining</h1>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-sm">
            <span class="text-label-md bg-surface-container px-xs py-1 rounded-lg text-on-surface-variant">
                {{ $tableNumber ? 'Table ' . $tableNumber : 'Walk-in' }}
            </span>
            <button id="header-cart-btn" class="material-symbols-outlined text-primary hover:opacity-80 transition-opacity active:scale-95 duration-200 relative">
                shopping_cart
                <span id="header-cart-badge" class="absolute -top-1.5 -right-2.5 bg-primary text-on-primary text-[10px] min-w-5 h-5 px-1 rounded-full flex items-center justify-center font-bold hidden">
                    0
                </span>
            </button>
        </div>
    </div>
</header>
