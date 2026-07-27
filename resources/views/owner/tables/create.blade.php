@extends('layouts.owner')

@section('title', 'Tambah Meja')
@section('page-title', 'Tambah Meja')

@section('content')
    <div class="max-w-2xl">
        <div class="flex items-center gap-2 text-label-sm text-on-surface-variant mb-6">
            <a href="{{ route('owner.tables.index') }}" class="hover:text-primary transition-colors">Meja</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary">Tambah Meja</span>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-outline-variant">
                <h2 class="text-headline-md text-primary">Tambah Meja Baru</h2>
            </div>

            <form method="POST" action="{{ route('owner.tables.store') }}" class="p-6 space-y-5">
                @csrf

                <div>
                    <label for="table_number" class="block text-label-md text-on-surface mb-1.5">Nomor Meja <span class="text-error">*</span></label>
                    <input type="text" id="table_number" name="table_number" value="{{ old('table_number') }}" required
                           class="w-full px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors"
                           placeholder="e.g. 01" />
                    @error('table_number') <p class="text-label-sm text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="capacity" class="block text-label-md text-on-surface mb-1.5">Kapasitas <span class="text-error">*</span></label>
                    <input type="number" id="capacity" name="capacity" value="{{ old('capacity', 4) }}" min="1" max="20" required
                           class="w-full px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors" />
                    @error('capacity') <p class="text-label-sm text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status" class="block text-label-md text-on-surface mb-1.5">Status <span class="text-error">*</span></label>
                    <select id="status" name="status" required
                        class="w-full h-12 px-4 rounded-xl border border-outline-variant bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors appearance-none"
                    >
                        <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="disabled" {{ old('status') === 'disabled' ? 'selected' : '' }}>Tidak Tersedia</option>
                    </select>
                    @error('status') <p class="text-label-sm text-error mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-outline-variant">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-primary text-on-primary px-6 py-2.5 rounded-xl text-label-md hover:opacity-90 transition-opacity shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        Simpan Meja
                    </button>
                    <a href="{{ route('owner.tables.index') }}"
                       class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-label-md text-on-surface-variant border border-outline-variant hover:bg-surface-container transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
