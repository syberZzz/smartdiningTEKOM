@extends('layouts.owner')

@section('title', $menu->name)
@section('page-title', 'Detail Menu')

@section('content')
    <div class="max-w-3xl">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-label-sm text-on-surface-variant mb-6">
            <a href="{{ route('owner.menus.index') }}" class="hover:text-primary transition-colors">Manajemen Menu</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary">{{ $menu->name }}</span>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            {{-- Image Header --}}
            <div class="relative">
                <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-full h-56 object-cover" />
                <div class="absolute top-4 right-4 flex gap-2">
                    <span class="px-3 py-1.5 rounded-full text-[12px] font-medium shadow-sm
                        {{ $menu->is_available ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                        {{ $menu->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                    </span>
                    @if ($menu->popular)
                        <span class="px-3 py-1.5 rounded-full text-[12px] font-medium bg-orange-500 text-white shadow-sm flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">local_fire_department</span>
                            Popular
                        </span>
                    @endif
                </div>
            </div>

            {{-- Details --}}
            <div class="p-6 space-y-5">
                <div>
                    <h1 class="text-headline-lg text-primary mb-1">{{ $menu->name }}</h1>
                    <p class="text-body-md text-on-surface-variant">{{ $menu->description ?? 'Belum ada deskripsi' }}</p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-surface-container-low rounded-xl p-4">
                        <p class="text-label-sm text-on-surface-variant mb-1">Harga</p>
                        <p class="text-headline-md text-primary font-bold">Rp{{ number_format($menu->price, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-surface-container-low rounded-xl p-4">
                        <p class="text-label-sm text-on-surface-variant mb-1">Kategori</p>
                        <p class="text-label-md text-primary font-semibold">{{ $menu->categoryRelation?->name ?? ucfirst($menu->category) }}</p>
                    </div>
                    <div class="bg-surface-container-low rounded-xl p-4">
                        <p class="text-label-sm text-on-surface-variant mb-1">Total Terjual</p>
                        <p class="text-headline-md text-primary font-bold">{{ $totalSold }}</p>
                    </div>
                    <div class="bg-surface-container-low rounded-xl p-4">
                        <p class="text-label-sm text-on-surface-variant mb-1">Pendapatan</p>
                        <p class="text-headline-md text-primary font-bold">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    </div>
                </div>



                <div class="flex items-center gap-2 text-on-surface-variant">
                    <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                    <span class="text-body-md">Dibuat {{ $menu->created_at->format('d M Y, H:i') }}</span>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-4 border-t border-outline-variant">
                    <a href="{{ route('owner.menus.edit', $menu) }}"
                       class="inline-flex items-center gap-2 bg-primary text-on-primary px-6 py-2.5 rounded-xl text-label-md hover:opacity-90 transition-opacity shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">edit</span>
                        Edit
                    </a>
                    <form method="POST" action="{{ route('owner.menus.toggle-status', $menu) }}" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-label-md border border-outline-variant hover:bg-surface-container transition-colors">
                            <span class="material-symbols-outlined text-[20px]">{{ $menu->is_available ? 'visibility_off' : 'visibility' }}</span>
                            {{ $menu->is_available ? 'Tandai Tidak Tersedia' : 'Tandai Tersedia' }}
                        </button>
                    </form>
                    <a href="{{ route('owner.menus.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-label-md text-on-surface-variant border border-outline-variant hover:bg-surface-container transition-colors">
                        ← Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
