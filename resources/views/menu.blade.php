@extends('layouts.app')

@section('title', 'Zazil Bakery - Menu')

@push('meta')
    <meta name="table-number" content="{{ $tableNumber ?? '' }}" />
@endpush

@section('content')
    {{-- Header --}}
    @include('components.menu.header', ['tableNumber' => $tableNumber])

    <main class="pt-[80px] px-margin-mobile md:max-w-7xl md:mx-auto md:px-margin-desktop space-y-md pb-[120px]">
        {{-- Welcome Section --}}
        <section class="py-sm">
            <h2 class="text-headline-lg-mobile md:text-headline-lg text-primary mb-2">Welcome!</h2>
            <p class="text-body-md text-on-surface-variant">Browse our menu and place your order.</p>
        </section>

        {{-- Search Bar --}}
        @include('components.menu.search-bar')

        {{-- Categories — dinamis dari database --}}
        @include('components.menu.categories', ['categories' => $categories])

        {{-- Menu Grid --}}
        <h3 class="text-headline-md text-primary mb-sm mt-sm" id="menu-section-title">Semua Menu</h3>

        <div id="menu-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-sm">
            @foreach ($menuItems as $item)
                <x-menu.item-card
                    :item-id="$item['id']"
                    :name="$item['name']"
                    :image="$item['image']"
                    :image-alt="$item['imageAlt']"
                    :rating="$item['rating']"
                    :time="$item['time']"
                    :description="$item['description']"
                    :price="$item['price']"
                    :category="$item['category']"
                    :popular="$item['popular']"
                />
            @endforeach
        </div>

        {{-- Empty State --}}
        <div id="menu-empty-state" class="hidden flex-col items-center justify-center py-xl text-center">
            <span class="material-symbols-outlined text-[64px] text-outline-variant mb-md">search_off</span>
            <p class="text-headline-md text-on-surface-variant mb-2">No items found</p>
            <p class="text-body-md text-outline">Try a different search or category.</p>
        </div>
    </main>

    {{-- Cart Bar --}}
    @include('components.menu.cart-bar')

    {{-- Order Review Sheet --}}
    @include('components.order.review-sheet', ['menuItems' => $menuItems, 'taxRate' => $taxRate])
@endsection
