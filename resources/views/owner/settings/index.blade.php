@extends('layouts.owner')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@section('content')

    <div class="max-w-2xl space-y-6">

        {{-- PPN / Tax Setting Card --}}
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-outline-variant bg-surface-container-low flex items-center gap-3">
                <span class="material-symbols-outlined text-[24px] text-primary" style="font-variation-settings: 'FILL' 1;">receipt_long</span>
                <div>
                    <h3 class="text-headline-md text-primary">Pajak Pertambahan Nilai (PPN)</h3>
                    <p class="text-label-sm text-on-surface-variant">Atur persentase PPN yang diterapkan pada setiap pesanan</p>
                </div>
            </div>

            <form method="POST" action="{{ route('owner.settings.update') }}" x-data="{ taxRate: {{ $taxRate }} }">
                @csrf
                @method('PUT')

                <div class="px-5 py-5 space-y-5">

                    {{-- Tax Rate Input --}}
                    <div>
                        <label for="tax_rate" class="text-label-md text-on-surface font-medium mb-2 block">
                            Persentase PPN
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="relative flex-1 max-w-[200px]">
                                <input
                                    type="number"
                                    name="tax_rate"
                                    id="tax_rate"
                                    x-model="taxRate"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    placeholder="0"
                                    class="w-full px-4 py-2.5 pr-10 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors text-right @error('tax_rate') border-red-400 @enderror"
                                />
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-label-md font-medium">%</span>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" @click="taxRate = 0"
                                    class="px-3 py-2 bg-surface-container border border-outline-variant text-label-sm text-on-surface-variant rounded-lg hover:bg-surface-container-high transition-colors">
                                    0%
                                </button>
                                <button type="button" @click="taxRate = 10"
                                    class="px-3 py-2 bg-surface-container border border-outline-variant text-label-sm text-on-surface-variant rounded-lg hover:bg-surface-container-high transition-colors">
                                    10%
                                </button>
                                <button type="button" @click="taxRate = 11"
                                    class="px-3 py-2 bg-surface-container border border-outline-variant text-label-sm text-on-surface-variant rounded-lg hover:bg-surface-container-high transition-colors">
                                    11%
                                </button>
                                <button type="button" @click="taxRate = 12"
                                    class="px-3 py-2 bg-surface-container border border-outline-variant text-label-sm text-on-surface-variant rounded-lg hover:bg-surface-container-high transition-colors">
                                    12%
                                </button>
                            </div>
                        </div>
                        @error('tax_rate')
                            <p class="text-label-sm text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Preview Card --}}
                    <div class="bg-surface-container rounded-xl p-4 border border-outline-variant">
                        <p class="text-label-sm text-on-surface-variant mb-3 font-medium">Preview Perhitungan</p>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-label-sm text-on-surface-variant">Subtotal</span>
                                <span class="text-label-md text-on-surface">Rp100.000</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-label-sm text-on-surface-variant">
                                    PPN (<span x-text="parseFloat(taxRate || 0).toFixed(taxRate % 1 !== 0 ? 2 : 0)"></span>%)
                                </span>
                                <span class="text-label-md text-primary font-medium"
                                      x-text="'Rp' + Math.round(100000 * (parseFloat(taxRate || 0) / 100)).toLocaleString('id-ID')">
                                </span>
                            </div>
                            <div class="border-t border-outline-variant pt-2 flex items-center justify-between">
                                <span class="text-label-md text-on-surface font-bold">Total</span>
                                <span class="text-headline-md text-primary font-bold"
                                      x-text="'Rp' + Math.round(100000 + (100000 * (parseFloat(taxRate || 0) / 100))).toLocaleString('id-ID')">
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Info Note --}}
                    <div class="flex items-start gap-3 p-3 bg-blue-50 border border-blue-200 rounded-xl">
                        <span class="material-symbols-outlined text-blue-600 text-[20px] mt-0.5" style="font-variation-settings: 'FILL' 1;">info</span>
                        <div class="text-label-sm text-blue-700 space-y-1">
                            <p>PPN akan dihitung otomatis pada setiap pesanan baru (QR maupun manual).</p>
                            <p>Perubahan tarif <strong>tidak memengaruhi</strong> pesanan yang sudah ada.</p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="px-5 py-4 border-t border-outline-variant bg-surface-container-low flex items-center justify-end gap-3">
                    <a href="{{ route('owner.dashboard') }}"
                       class="px-5 py-2.5 text-label-md text-on-surface-variant border border-outline-variant rounded-xl hover:bg-surface-container-high transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-on-primary text-label-md rounded-xl hover:opacity-90 transition-opacity shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

    </div>

@endsection
