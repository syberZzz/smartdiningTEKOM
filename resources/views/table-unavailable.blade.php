@extends('layouts.app')

@section('title', 'Meja Tidak Tersedia — Lumiere Dining')

@section('content')
    <div class="min-h-screen bg-surface flex flex-col items-center justify-center px-6 py-12">

        {{-- Icon --}}
        <div class="w-24 h-24 rounded-full bg-red-50 flex items-center justify-center mb-6 animate-pulse">
            <span class="material-symbols-outlined text-[48px] text-red-400" style="font-variation-settings: 'FILL' 1;">
                block
            </span>
        </div>

        {{-- Heading --}}
        <h1 class="text-headline-lg-mobile md:text-headline-lg text-primary text-center mb-2">
            Meja Tidak Tersedia
        </h1>

        {{-- Description --}}
        <p class="text-body-md text-on-surface-variant text-center max-w-sm mb-2">
            Maaf, meja <strong class="text-primary">{{ $tableNumber }}</strong> sedang tidak aktif saat ini.
        </p>
        <p class="text-body-md text-on-surface-variant text-center max-w-sm mb-8">
            Silakan hubungi pelayan untuk bantuan atau scan kode QR meja lain yang tersedia.
        </p>

        {{-- Info Card --}}
        <div class="w-full max-w-sm bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm p-5 mb-8">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-amber-500 text-[24px] flex-shrink-0 mt-0.5" style="font-variation-settings: 'FILL' 1;">
                    info
                </span>
                <div>
                    <p class="text-label-md text-primary mb-1">Apa yang bisa Anda lakukan?</p>
                    <ul class="text-label-sm text-on-surface-variant space-y-1.5">
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-on-surface-variant mt-0.5">qr_code_scanner</span>
                            Scan kode QR meja lain yang aktif
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-[16px] text-on-surface-variant mt-0.5">support_agent</span>
                            Hubungi pelayan terdekat
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
@endsection
