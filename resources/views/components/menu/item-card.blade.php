{{-- Menu Item Card --}}
@props([
    'itemId',
    'name',
    'image',
    'imageAlt' => '',
    'rating',
    'time',
    'description',
    'price',
    'category',
    'popular' => false,
])

<div
    class="menu-item-card bg-surface-container-lowest rounded-2xl border border-outline-variant overflow-hidden shadow-[0px_4px_12px_rgba(15,23,42,0.05)] flex flex-col transition-transform duration-200 hover:shadow-[0px_8px_24px_rgba(15,23,42,0.1)] hover:-translate-y-0.5"
    data-item-id="{{ $itemId }}"
    data-name="{{ strtolower($name) }}"
    data-category="{{ $category }}"
    data-price="{{ $price }}"
    data-popular="{{ $popular ? 'true' : 'false' }}"
>
    {{-- Image --}}
    <div class="p-3 pb-0 relative">
        <img
            class="w-full h-[150px] object-cover rounded-xl"
            src="{{ $image }}"
            alt="{{ $imageAlt ?: $name }}"
            loading="lazy"
        />
        @if($popular)
            <span class="absolute top-5 left-5 bg-orange-500 text-white text-[11px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1 shadow-sm">
                <span class="material-symbols-outlined text-[14px]">local_fire_department</span>
                Best Seller
            </span>
        @endif
    </div>

    {{-- Details --}}
    <div class="p-3 pt-3 flex-grow flex flex-col justify-between">
        <div>
            <h4 class="text-base font-bold text-primary mb-1 line-clamp-1">{{ $name }}</h4>
            {{-- Description --}}
            <p class="text-xs text-on-surface-variant line-clamp-2 leading-relaxed">{{ $description }}</p>
        </div>

        <div class="flex items-end justify-between mt-3">
            {{-- Price --}}
            <div class="text-lg font-bold text-primary">Rp{{ number_format($price, 0, ',', '.') }}</div>

            {{-- Add Button --}}
            <div class="flex items-center gap-1.5" data-item-id="{{ $itemId }}" data-name="{{ $name }}" data-price="{{ $price }}">
                <button
                    class="qty-btn qty-minus hidden material-symbols-outlined text-[18px] text-on-surface-variant w-8 h-8 items-center justify-center rounded-full bg-surface-container hover:bg-surface-variant active:scale-95 transition-colors"
                    data-item-id="{{ $itemId }}"
                >remove</button>
                <span class="qty-display hidden font-bold text-primary min-w-[20px] text-center text-sm select-none" data-item-id="{{ $itemId }}">0</span>
                <button
                    class="qty-btn qty-plus material-symbols-outlined text-[22px] text-white w-9 h-9 flex items-center justify-center rounded-full bg-primary hover:opacity-90 active:scale-95 transition-opacity shadow-sm"
                    data-item-id="{{ $itemId }}"
                >add</button>
            </div>
        </div>
    </div>
</div>
