{{-- Category Filter Buttons — Dinamis dari database --}}
{{--
    Menerima variabel $categories (Collection of Category model)
    Setiap kategori memiliki: id, name, icon, description
    Slug filter menggunakan strtolower(name) — sama persis dengan data-category di menu-item-card
--}}
<div class="flex overflow-x-auto no-scrollbar gap-sm py-2 -mx-margin-mobile px-margin-mobile md:mx-0 md:px-0">

    {{-- Tombol "Semua" — selalu muncul pertama --}}
    <button
        data-category="all"
        class="category-btn flex flex-col items-center justify-center rounded-full px-4 py-2 flex-shrink-0 min-w-[80px] transition-all duration-200
               bg-primary text-on-primary shadow-[0px_4px_12px_rgba(15,23,42,0.1)]"
    >
        <span class="material-symbols-outlined mb-1 text-[20px]">restaurant_menu</span>
        <span class="text-label-sm">Semua</span>
    </button>

    {{-- Kategori dari database --}}
    @foreach ($categories as $cat)
        <button
            data-category="{{ strtolower($cat->name) }}"
            class="category-btn flex flex-col items-center justify-center rounded-full px-4 py-2 flex-shrink-0 min-w-[80px] transition-all duration-200
                   bg-surface-container-lowest text-on-surface-variant border border-outline-variant hover:bg-surface-container"
        >
            <span class="material-symbols-outlined mb-1 text-[20px]">{{ $cat->icon ?? 'category' }}</span>
            <span class="text-label-sm">{{ $cat->name }}</span>
        </button>
    @endforeach

    {{-- Fallback jika tidak ada kategori di DB --}}
    @if ($categories->isEmpty())
        <p class="text-label-sm text-on-surface-variant self-center px-2 italic">
            Belum ada kategori tersedia.
        </p>
    @endif

</div>
