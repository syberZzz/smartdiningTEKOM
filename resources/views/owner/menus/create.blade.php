@extends('layouts.owner')

@section('title', 'Tambah Item Menu')
@section('page-title', 'Tambah Item Menu')

@section('content')
    <div class="max-w-3xl">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-label-sm text-on-surface-variant mb-6">
            <a href="{{ route('owner.menus.index') }}" class="hover:text-primary transition-colors">Manajemen Menu</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary">Tambah Item Menu</span>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-outline-variant">
                <h2 class="text-headline-md text-primary">Tambah Item Menu Baru</h2>
            </div>

            <form method="POST" action="{{ route('owner.menus.store') }}" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf

                {{-- Menu Name --}}
                <div>
                    <label for="name" class="block text-label-md text-on-surface mb-1.5">Nama Menu <span class="text-error">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors"
                           placeholder="cth. Nasi Goreng Spesial" />
                    @error('name') <p class="text-label-sm text-error mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Category --}}
                <div>
                    <label for="category_id" class="block text-label-md text-on-surface mb-1.5">Kategori <span class="text-error">*</span></label>
                    <select id="category_id" name="category_id" required
                            class="w-full px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-label-sm text-error mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Price --}}
                <div x-data="{ 
                    rawPrice: '{{ old('price', '') }}',
                    get formattedPrice() {
                        if (!this.rawPrice) return '';
                        return parseInt(this.rawPrice).toLocaleString('id-ID');
                    },
                    updatePrice(value) {
                        let numericValue = value.replace(/[^0-9]/g, '');
                        this.rawPrice = numericValue;
                        $refs.displayInput.value = this.rawPrice ? parseInt(this.rawPrice).toLocaleString('id-ID') : '';
                    }
                }">
                    <label for="price_display" class="block text-label-md text-on-surface mb-1.5">Harga (Rupiah) <span class="text-error">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-2.5 text-on-surface-variant font-medium">Rp</span>
                        <input type="text" id="price_display" x-ref="displayInput" :value="formattedPrice" @input="updatePrice($event.target.value)" required
                               class="w-full pl-11 pr-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors"
                               placeholder="cth. 25.000" />
                        <input type="hidden" id="price" name="price" :value="rawPrice" />
                    </div>
                    @error('price') <p class="text-label-sm text-error mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-label-md text-on-surface mb-1.5">Deskripsi</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors resize-none"
                              placeholder="Deskripsi singkat item menu...">{{ old('description') }}</textarea>
                    @error('description') <p class="text-label-sm text-error mt-1">{{ $message }}</p> @enderror
                </div>


                {{-- Image Upload --}}
                <div x-data="{ preview: null }">
                    <label for="image" class="block text-label-md text-on-surface mb-1.5">Gambar</label>
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <input type="file" id="image" name="image" accept="image/*"
                                   @change="if ($event.target.files[0]) { const reader = new FileReader(); reader.onload = e => preview = e.target.result; reader.readAsDataURL($event.target.files[0]); }"
                                   class="w-full px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-label-sm file:bg-primary/10 file:text-primary" />
                            <p class="text-label-sm text-on-surface-variant mt-1">Maks 2MB. JPEG, PNG, WebP diterima.</p>
                        </div>
                        <div x-show="preview" class="flex-shrink-0">
                            <img :src="preview" class="w-20 h-20 rounded-xl object-cover border border-outline-variant" />
                        </div>
                    </div>
                    @error('image') <p class="text-label-sm text-error mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Status & Popular --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="block text-label-md text-on-surface mb-1.5">Status <span class="text-error">*</span></label>
                        <select id="status" name="status" required
                                class="w-full px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors">
                            <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="unavailable" {{ old('status') === 'unavailable' ? 'selected' : '' }}>Tidak Tersedia</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-3 px-4 py-2.5 border border-outline-variant rounded-xl bg-surface cursor-pointer hover:border-primary transition-colors w-full">
                            <input type="checkbox" name="popular" value="1" {{ old('popular') ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-outline-variant text-primary focus:ring-primary" />
                            <span class="text-body-md text-on-surface">Tandai sebagai Populer</span>
                        </label>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 pt-4 border-t border-outline-variant">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-primary text-on-primary px-6 py-2.5 rounded-xl text-label-md hover:opacity-90 transition-opacity shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        Simpan Item Menu
                    </button>
                    <a href="{{ route('owner.menus.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-label-md text-on-surface-variant border border-outline-variant hover:bg-surface-container transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
