@extends('layouts.owner')

@section('title', 'QR Code — Table ' . $table->table_number)
@section('page-title', 'QR Code')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-2 text-label-sm text-on-surface-variant mb-6">
            <a href="{{ route('owner.tables.index') }}" class="hover:text-primary transition-colors">Tables</a>
            <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            <span class="text-primary">QR Code — Table {{ $table->table_number }}</span>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-outline-variant flex items-center justify-between">
                <h2 class="text-headline-md text-primary">Table {{ $table->table_number }} — QR Code</h2>
                <span class="inline-flex items-center gap-1.5 text-[12px] px-3 py-1 rounded-full font-medium
                    {{ match($table->status) {
                        'available' => 'bg-green-50 text-green-700',
                        'disabled'  => 'bg-red-50 text-red-700',
                        default     => 'bg-surface-container text-on-surface-variant',
                    } }}">
                    {{ match($table->status) {
                        'available' => 'Tersedia',
                        'disabled'  => 'Tidak Tersedia',
                        default     => ucfirst($table->status),
                    } }}
                </span>
            </div>

            <div class="p-8 flex flex-col items-center">
                {{-- QR Preview --}}
                <div class="bg-white p-6 rounded-2xl border border-outline-variant shadow-sm mb-6">
                    <div class="flex justify-center">
                        {!! $qrSvg !!}
                    </div>
                </div>

                {{-- URL Info --}}
                <div class="bg-surface-container-low rounded-xl px-5 py-3 mb-6 w-full max-w-md text-center">
                    <p class="text-label-sm text-on-surface-variant mb-1">QR Code URL</p>
                    <p class="text-label-md text-primary font-mono break-all">{{ $qrUrl }}</p>
                </div>

                {{-- Table Info --}}
                <div class="flex items-center gap-6 mb-6 text-on-surface-variant">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">group</span>
                        <span class="text-body-md">{{ $table->capacity }} persons</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">tag</span>
                        <span class="text-body-md">Table {{ $table->table_number }}</span>
                    </div>
                </div>

                {{-- Download & Print Actions --}}
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('owner.tables.qr.download', $table) }}"
                       class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-xl text-label-md hover:opacity-90 transition-opacity shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">download</span>
                        Download SVG
                    </a>
                    <a href="{{ route('owner.tables.qr.print', $table) }}" target="_blank"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-label-md text-on-surface-variant border border-outline-variant hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                        Print
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('owner.tables.index') }}"
               class="inline-flex items-center gap-2 text-label-md text-on-surface-variant hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Back to Tables
            </a>
        </div>
    </div>
@endsection
