<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Struk — {{ $order->invoice_number }} | SmartDining</title>

    <style>
        /* ── Reset ── */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        /* ── @page: Kertas thermal 58mm ── */
        @page {
            size: 58mm auto;   /* lebar 58mm, tinggi otomatis */
            margin: 2mm 3mm;   /* margin tipis kiri-kanan */
        }

        /* ── Base ── */
        html, body {
            width: 100%;
            min-height: 100vh;
            font-family: 'Courier New', Courier, monospace;
            font-size: 9pt;
            font-weight: 700;
            color: #000;
            background: #e5e7eb;
        }

        /* ── Preview wrapper (hanya tampil di layar, bukan saat print) ── */
        .preview-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px 16px;
        }

        /* ── Struk card di layar ── */
        .receipt-card {
            background: #fff;
            width: 58mm;
            padding: 6mm 4mm;
            box-shadow: 0 4px 24px rgba(0,0,0,0.15);
            border-radius: 4px;
        }

        /* ── Tombol aksi (hanya layar) ── */
        .action-area {
            margin-top: 16px;
            width: 58mm;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .btn-print {
            width: 100%;
            padding: 10px;
            background: #D32F2F;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: 0.02em;
        }
        .btn-print:hover { background: #162d4a; }
        .btn-back {
            display: block;
            width: 100%;
            padding: 8px;
            background: #fff;
            color: #6b7280;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        .btn-back:hover { background: #f9fafb; }

        /* ══════════════════════════════════════════
           KONTEN STRUK
        ══════════════════════════════════════════ */

        /* Logo */
        .rh-logo {
            display: block;
            margin: 0 auto 1.5mm;
            width: 18mm;
            height: auto;
        }

        /* Header */
        .rh-name {
            text-align: center;
            font-size: 11pt;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 1mm;
        }
        .rh-tagline {
            text-align: center;
            font-size: 7.5pt;
            color: #000;
            margin-bottom: 2mm;
        }

        /* Divider */
        .div-dash {
            border: none;
            border-top: 1px dashed #aaa;
            margin: 2mm 0;
        }
        .div-solid {
            border: none;
            border-top: 2px solid #000;
            margin: 2mm 0;
        }
        .div-eq {
            text-align: center;
            font-size: 8pt;
            color: #000;
            letter-spacing: 0.05em;
            margin: 1.5mm 0;
            overflow: hidden;
            white-space: nowrap;
        }

        /* Info rows */
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 8pt;
            font-weight: 700;
            margin-bottom: 0.8mm;
            gap: 4px;
        }
        .info-lbl { color: #000; white-space: nowrap; font-weight: 700; }
        .info-val { text-align: right; font-weight: 700; word-break: break-all; }

        /* Section title */
        .sec-title {
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 1.5mm;
        }

        /* Item rows — layout 3 kolom */
        .item-row {
            display: grid;
            /* Kolom: nama | qty | harga */
            grid-template-columns: 1fr auto auto;
            column-gap: 2mm;
            align-items: start;
            padding: 1mm 0;
            border-bottom: 1px dashed #ddd;
            font-size: 8pt;
            font-weight: 700;
        }
        .item-row:last-child { border-bottom: none; }
        .item-name   { font-weight: 700; word-break: break-word; }
        .item-note   { font-size: 7pt; color: #000; font-style: italic; grid-column: 1 / -1; margin-top: 0.3mm; font-weight: 700; }
        .item-qty    { color: #000; white-space: nowrap; text-align: right; font-weight: 700; }
        .item-price  { white-space: nowrap; text-align: right; font-weight: 700; }

        /* Summary */
        .sum-row {
            display: flex;
            justify-content: space-between;
            font-size: 8pt;
            font-weight: 700;
            padding: 0.6mm 0;
        }
        .sum-lbl { color: #000; font-weight: 700; }

        /* Total besar */
        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 11pt;
            font-weight: 700;
            padding: 1.5mm 0 0.5mm;
            border-top: 2px solid #000;
            margin-top: 1mm;
        }

        /* Kembalian */
        .change-row {
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            font-weight: 700;
            padding: 0.8mm 0;
        }

        /* Footer */
        .foot-thanks {
            text-align: center;
            font-size: 9pt;
            font-weight: 700;
            margin-bottom: 1mm;
            letter-spacing: 0.02em;
        }
        .foot-note {
            text-align: center;
            font-size: 7.5pt;
            color: #000;
            line-height: 1.4;
        }

        /* Barcode / QR placeholder */
        .foot-inv {
            text-align: center;
            font-size: 7pt;
            color: #000;
            margin-top: 1.5mm;
            letter-spacing: 0.1em;
        }

        /* ── Print overrides ── */
        @media print {
            html, body {
                width: 58mm;
                background: #fff;
            }
            .preview-wrapper {
                display: block;
                background: none;
                padding: 0;
            }
            .receipt-card {
                box-shadow: none;
                border-radius: 0;
                width: 100%;
                padding: 0;
            }
            .action-area { display: none !important; }
        }
    </style>
</head>
<body>

<div class="preview-wrapper">
    {{-- ══ STRUK ══ --}}
    <div class="receipt-card" id="receipt-content">

        {{-- Header --}}
        <img src="{{ asset('images/logo.png') }}" alt="Zazil Bakery Logo" class="rh-logo" />
        <p class="rh-name">Zazil Bakery</p>
        <p class="rh-tagline">Restoran &amp; Café</p>

        <hr class="div-solid" />

        {{-- Info Pesanan --}}
        <div class="info-row">
            <span class="info-lbl">No. Invoice</span>
            <span class="info-val">{{ $order->invoice_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-lbl">Tanggal</span>
            <span class="info-val">{{ $order->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-lbl">Meja</span>
            <span class="info-val">{{ $order->table_number }}</span>
        </div>
        @if($order->customer_name)
        <div class="info-row">
            <span class="info-lbl">Pelanggan</span>
            <span class="info-val">{{ $order->customer_name }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-lbl">Tipe</span>
            <span class="info-val">{{ $order->order_type_label }}</span>
        </div>
        <div class="info-row">
            <span class="info-lbl">Kasir</span>
            <span class="info-val">Zazil Bakery Kasir</span>
        </div>

        <hr class="div-dash" />

        {{-- Item Pesanan --}}
        <p class="sec-title">Item Pesanan</p>

        {{-- Header kolom item --}}
        <div style="display:grid; grid-template-columns:1fr auto auto; column-gap:2mm; font-size:7.5pt; color:#000; margin-bottom:1mm; padding-bottom:0.5mm; border-bottom:1px solid #000;">
            <span>Nama</span>
            <span style="text-align:right;">Qty</span>
            <span style="text-align:right;">Harga</span>
        </div>

        @foreach ($order->items as $item)
        <div class="item-row">
            <span class="item-name">{{ $item->menuItem?->name ?? 'Item' }}</span>
            <span class="item-qty">x{{ $item->quantity }}</span>
            <span class="item-price">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
            @if($item->notes)
                <span class="item-note">* {{ $item->notes }}</span>
            @endif
        </div>
        @endforeach

        <hr class="div-dash" />

        {{-- Subtotal --}}
        <div class="sum-row">
            <span class="sum-lbl">Subtotal ({{ $order->total_items }} item)</span>
            <span>Rp{{ number_format($order->subtotal > 0 ? $order->subtotal : $order->total_price, 0, ',', '.') }}</span>
        </div>

        @if($order->tax_amount > 0)
        <div class="sum-row">
            <span class="sum-lbl">PPN ({{ rtrim(rtrim(number_format($order->tax_percentage, 2), '0'), '.') }}%)</span>
            <span>Rp{{ number_format($order->tax_amount, 0, ',', '.') }}</span>
        </div>
        @endif

        {{-- Total --}}
        <div class="total-row">
            <span>TOTAL</span>
            <span>Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
        </div>

        {{-- Pembayaran --}}
        @if($order->payment)
        <hr class="div-dash" />
        <div class="sum-row">
            <span class="sum-lbl">Metode Bayar</span>
            <span style="font-weight:700; text-transform:uppercase;">{{ $order->payment->method }}</span>
        </div>
        @if($order->payment->method === 'cash')
            <div class="sum-row">
                <span class="sum-lbl">Tunai Diterima</span>
                <span>Rp{{ number_format($order->payment->amount, 0, ',', '.') }}</span>
            </div>
            @if($order->payment->change_amount !== null)
            <div class="change-row">
                <span>Kembalian</span>
                <span>Rp{{ number_format($order->payment->change_amount, 0, ',', '.') }}</span>
            </div>
            @endif
        @endif
        <div class="sum-row">
            <span class="sum-lbl">Status</span>
            <span style="font-weight:700; color:{{ $order->payment->status === 'paid' ? '#059669' : '#b45309' }}">
                {{ $order->payment->status === 'paid' ? 'LUNAS' : strtoupper($order->payment->status ?? 'MENUNGGU') }}
            </span>
        </div>
        @endif

        <hr class="div-solid" />

        {{-- Footer --}}
        <p class="foot-thanks">*** Terima Kasih! ***</p>
        <p class="foot-note">Anda Senyum - Kami Bahagia</p>
        <p class="foot-note">Zazil Bakery · Restoran &amp; Café</p>
        <p class="foot-inv">{{ $order->invoice_number }}</p>

    </div>{{-- end receipt-card --}}

    {{-- Tombol Aksi (tidak dicetak) --}}
    <div class="action-area no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Cetak Struk</button>
        <a href="javascript:history.back()" class="btn-back">← Kembali ke Pesanan</a>
    </div>
</div>

<script>
    // Auto-print jika URL mengandung ?autoprint=1
    if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
        window.addEventListener('load', () => setTimeout(() => window.print(), 400));
    }
</script>

</body>
</html>
