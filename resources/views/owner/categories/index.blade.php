@extends('layouts.owner')

@section('title', 'Manajemen Kategori')
@section('page-title', 'Manajemen Kategori')

@section('content')
    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-headline-lg-mobile lg:text-headline-lg text-primary">Kategori</h1>
            <p class="text-body-md text-on-surface-variant mt-1">Kelola kategori menu</p>
        </div>
        <a href="{{ route('owner.categories.create') }}"
           class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-xl text-label-md hover:opacity-90 transition-opacity shadow-sm">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Tambah Kategori
        </a>
    </div>

    {{-- Search --}}
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('owner.categories.index') }}" class="flex gap-3">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari kategori..."
                       class="w-full pl-10 pr-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors" />
            </div>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary/10 text-primary rounded-xl text-label-md hover:bg-primary/20 transition-colors">
                <span class="material-symbols-outlined text-[18px]">search</span>
                Cari
            </button>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-outline-variant bg-surface-container-low">
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">#</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Nama Kategori</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold hidden md:table-cell">Deskripsi</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Total Menu</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold hidden lg:table-cell">Dibuat Pada</th>
                        <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse ($categories as $index => $category)
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="px-5 py-3 text-label-sm text-on-surface-variant">{{ $categories->firstItem() + $index }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                        <span class="material-symbols-outlined text-primary text-[18px]">{{ $category->icon ?? 'category' }}</span>
                                    </span>
                                    <span class="text-label-md text-primary font-semibold">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 hidden md:table-cell">
                                <span class="text-label-sm text-on-surface-variant truncate max-w-[300px] block">{{ $category->description ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1 text-label-md bg-primary/10 text-primary px-3 py-1 rounded-full font-semibold">
                                    <span class="material-symbols-outlined text-[16px]">restaurant_menu</span>
                                    {{ $category->menus_count }}
                                </span>
                            </td>
                            <td class="px-5 py-3 hidden lg:table-cell">
                                <span class="text-label-sm text-on-surface-variant">{{ $category->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('owner.categories.edit', $category) }}"
                                       class="p-2 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors"
                                       title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('owner.categories.destroy', $category) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 rounded-lg text-on-surface-variant hover:bg-red-50 hover:text-red-600 transition-colors"
                                                title="Delete">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <span class="material-symbols-outlined text-[48px] text-outline-variant mb-3 block">category</span>
                                <p class="text-headline-md text-on-surface-variant mb-1">Tidak ada kategori ditemukan</p>
                                <p class="text-body-md text-outline">Buat kategori pertama Anda untuk memulai.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($categories->hasPages())
            <div class="px-5 py-4 border-t border-outline-variant">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
@endsection
