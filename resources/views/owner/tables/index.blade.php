@extends('layouts.owner')

@section('title', 'Manajemen Meja')
@section('page-title', 'Manajemen Meja')

@section('content')
    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-headline-lg-mobile lg:text-headline-lg text-primary">Manajemen Meja</h1>
            <p class="text-body-md text-on-surface-variant mt-1">Kelola meja restoran dan kode QR</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            {{-- Download All QR Dropdown --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button
                    @click="open = !open"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-label-md border border-primary text-primary hover:bg-primary/10 transition-colors"
                    title="Download semua QR code aktif"
                >
                    <span class="material-symbols-outlined text-[20px]">qr_code_2</span>
                    Unduh Semua QR
                    <span class="material-symbols-outlined text-[16px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>

                {{-- Dropdown Menu --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-72 bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-lg z-20 overflow-hidden"
                    style="display: none;"
                >
                    <div class="px-4 py-3 border-b border-outline-variant">
                        <p class="text-label-sm text-on-surface-variant font-medium">Pilih meja yang diunduh (PDF):</p>
                    </div>

                    <a
                        href="{{ route('owner.tables.qr.download-all-pdf', ['status_filter' => 'active']) }}"
                        class="flex items-start gap-3 px-4 py-3 hover:bg-primary/5 transition-colors group"
                        @click="open = false"
                    >
                        <span class="material-symbols-outlined text-[20px] text-primary mt-0.5">download_for_offline</span>
                        <div>
                            <p class="text-label-md text-on-surface group-hover:text-primary transition-colors">Semua Meja Aktif</p>
                            <p class="text-[11px] text-on-surface-variant mt-0.5">Available + Occupied (exclude disabled)</p>
                        </div>
                    </a>

                    <a
                        href="{{ route('owner.tables.qr.download-all-pdf', ['status_filter' => 'available']) }}"
                        class="flex items-start gap-3 px-4 py-3 hover:bg-primary/5 transition-colors group border-t border-outline-variant"
                        @click="open = false"
                    >
                        <span class="material-symbols-outlined text-[20px] text-green-600 mt-0.5">check_circle</span>
                        <div>
                            <p class="text-label-md text-on-surface group-hover:text-primary transition-colors">Hanya Meja Available</p>
                            <p class="text-[11px] text-on-surface-variant mt-0.5">Hanya meja dengan status Available</p>
                        </div>
                    </a>

                    <div class="px-4 py-2.5 border-t border-outline-variant bg-surface-container-low">
                        <p class="text-[11px] text-on-surface-variant flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[14px]">info</span>
                            File diunduh dalam format PDF (Siap cetak)
                        </p>
                    </div>
                </div>
            </div>

            <a href="{{ route('owner.tables.create') }}"
               class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-xl text-label-md hover:opacity-90 transition-opacity shadow-sm">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Tambah Meja
            </a>
        </div>
    </div>


    {{-- Filters --}}
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('owner.tables.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nomor meja..."
                       class="w-full pl-10 pr-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors" />
            </div>
            <select name="status"
                    class="px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors">
                <option value="">Semua Status</option>
                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                <option value="disabled" {{ request('status') === 'disabled' ? 'selected' : '' }}>Tidak Tersedia</option>
            </select>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary/10 text-primary rounded-xl text-label-md hover:bg-primary/20 transition-colors">
                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                Filter
            </button>
        </form>
    </div>

    {{-- Table Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse ($tables as $table)
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                {{-- Status Header --}}
                <div class="px-5 py-3 border-b border-outline-variant flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[24px]" style="font-variation-settings: 'FILL' 1;">table_restaurant</span>
                        <span class="text-headline-md text-primary font-bold">{{ $table->table_number }}</span>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-[12px] px-3 py-1 rounded-full font-medium
                        {{ match($table->status) {
                            'available' => 'bg-green-50 text-green-700',
                            'disabled'  => 'bg-red-50 text-red-700',
                            default     => 'bg-surface-container text-on-surface-variant',
                        } }}">
                        <span class="w-2 h-2 rounded-full {{ match($table->status) {
                            'available' => 'bg-green-600',
                            'disabled'  => 'bg-red-500',
                            default     => 'bg-gray-400',
                        } }}"></span>
                        {{ match($table->status) {
                            'available' => 'Tersedia',
                            'disabled'  => 'Tidak Tersedia',
                            default     => ucfirst($table->status),
                        } }}
                    </span>
                </div>

                {{-- Details --}}
                <div class="px-5 py-4">
                    <div class="flex items-center gap-2 text-on-surface-variant mb-3">
                        <span class="material-symbols-outlined text-[18px]">group</span>
                        <span class="text-body-md">Kapasitas: {{ $table->capacity }} orang</span>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('owner.tables.qr', $table) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-label-sm bg-primary/10 text-primary hover:bg-primary/20 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">qr_code_2</span>
                            QR Code
                        </a>
                        <a href="{{ route('owner.tables.edit', $table) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-label-sm bg-surface-container text-on-surface-variant hover:bg-surface-container-high transition-colors">
                            <span class="material-symbols-outlined text-[16px]">edit</span>
                            Edit
                        </a>
                        <form method="POST" action="{{ route('owner.tables.destroy', $table) }}"
                              onsubmit="return confirm('Hapus Meja {{ $table->table_number }}?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-label-sm text-red-600 hover:bg-red-50 transition-colors">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-surface-container-lowest rounded-2xl border border-outline-variant p-12 text-center">
                <span class="material-symbols-outlined text-[48px] text-outline-variant mb-3 block">table_restaurant</span>
                <p class="text-headline-md text-on-surface-variant mb-1">Tidak ada meja ditemukan</p>
                <p class="text-body-md text-outline">Tambahkan meja pertama Anda untuk memulai.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($tables->hasPages())
        <div class="mt-6">
            {{ $tables->links() }}
        </div>
    @endif
@endsection
