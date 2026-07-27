@extends('layouts.owner')

@section('title', 'Manajemen Menu')
@section('page-title', 'Manajemen Menu')

@section('content')
    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-headline-lg-mobile lg:text-headline-lg text-primary">Manajemen Menu</h1>
            <p class="text-body-md text-on-surface-variant mt-1">Kelola item menu restoran Anda</p>
        </div>
        <a href="{{ route('owner.menus.create') }}"
           class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-xl text-label-md hover:opacity-90 transition-opacity shadow-sm">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Item Menu
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('owner.menus.index') }}" class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari menu..."
                       class="w-full pl-10 pr-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors" />
            </div>

            {{-- Category Filter --}}
            <select name="category_id"
                    class="px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            {{-- Status Filter --}}
            <select name="status"
                    class="px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors">
                <option value="">Semua Status</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                <option value="unavailable" {{ request('status') === 'unavailable' ? 'selected' : '' }}>Tidak Tersedia</option>
            </select>

            {{-- Sort --}}
            <select name="sort"
                    class="px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors">
                <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
            </select>

            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary/10 text-primary rounded-xl text-label-md hover:bg-primary/20 transition-colors">
                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                Filter
            </button>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-outline-variant bg-surface-container-low">
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Gambar</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Nama Menu</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold hidden md:table-cell">Kategori</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Harga</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold hidden lg:table-cell">Status</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold hidden lg:table-cell">Dibuat</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($menus as $menu)
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="px-5 py-3">
                                <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="w-12 h-12 rounded-xl object-cover" />
                            </td>
                            <td class="px-5 py-3">
                                <p class="text-label-md text-primary font-semibold">{{ $menu->name }}</p>
                                <p class="text-label-sm text-on-surface-variant truncate max-w-[200px]">{{ $menu->description }}</p>
                            </td>
                            <td class="px-5 py-3 hidden md:table-cell">
                                <span class="text-label-sm bg-surface-container px-3 py-1 rounded-full text-on-surface-variant">
                                    {{ $menu->categoryRelation?->name ?? ucfirst($menu->category) }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-label-md text-primary font-semibold">Rp{{ number_format($menu->price, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-5 py-3 hidden lg:table-cell">
                                <form method="POST" action="{{ route('owner.menus.toggle-status', $menu) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 text-[12px] px-3 py-1 rounded-full font-medium transition-colors
                                        {{ $menu->is_available
                                            ? 'bg-green-50 text-green-700 hover:bg-green-100'
                                            : 'bg-red-50 text-red-700 hover:bg-red-100' }}">
                                        <span class="w-2 h-2 rounded-full {{ $menu->is_available ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                        {{ $menu->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-3 hidden lg:table-cell">
                                <span class="text-label-sm text-on-surface-variant">{{ $menu->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('owner.menus.show', $menu) }}"
                                       class="p-2 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors"
                                       title="Lihat Detail">
                                        <span class="material-symbols-outlined text-[20px]">visibility</span>
                                    </a>
                                    <a href="{{ route('owner.menus.edit', $menu) }}"
                                       class="p-2 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors"
                                       title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('owner.menus.destroy', $menu) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus item menu ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 rounded-lg text-on-surface-variant hover:bg-red-50 hover:text-red-600 transition-colors"
                                                title="Hapus">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] text-outline-variant mb-3 block">restaurant_menu</span>
                                <p class="text-headline-md text-on-surface-variant mb-1">Tidak ada item menu ditemukan</p>
                                <p class="text-body-md text-outline">Coba sesuaikan filter Anda atau tambahkan item menu baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($menus->hasPages())
            <div class="px-5 py-4 border-t border-outline-variant">
                {{ $menus->links() }}
            </div>
        @endif
    </div>
@endsection
