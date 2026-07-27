@extends('layouts.owner')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('content')
    <div class="max-w-2xl">
        <div class="flex items-center gap-2 text-label-sm text-on-surface-variant mb-6">
            <a href="{{ route('owner.categories.index') }}" class="hover:text-primary transition-colors">Kategori</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary">Tambah Kategori</span>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-outline-variant">
                <h2 class="text-headline-md text-primary">Tambah Kategori Baru</h2>
            </div>

            <form method="POST" action="{{ route('owner.categories.store') }}" class="p-6 space-y-5">
                @csrf

                {{-- Category Name --}}
                <div>
                    <label for="name" class="block text-label-md text-on-surface mb-1.5">Nama Kategori <span class="text-error">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors"
                           placeholder="e.g. Makanan" />
                    @error('name') <p class="text-label-sm text-error mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Icon Picker --}}
                @include('owner.categories._icon-picker', ['selectedIcon' => old('icon', 'category')])

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-label-md text-on-surface mb-1.5">Deskripsi</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors resize-none"
                              placeholder="Deskripsi opsional...">{{ old('description') }}</textarea>
                    @error('description') <p class="text-label-sm text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-outline-variant">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-primary text-on-primary px-6 py-2.5 rounded-xl text-label-md hover:opacity-90 transition-opacity shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        Simpan Kategori
                    </button>
                    <a href="{{ route('owner.categories.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-label-md text-on-surface-variant border border-outline-variant hover:bg-surface-container transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
