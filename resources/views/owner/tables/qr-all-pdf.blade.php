<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>QR Codes — All Tables</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #1a1c1c;
            padding: 30px 40px;
        }

        /* ── Header ─────────────────────────────────── */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #1B3856;
        }
        .header h1 {
            font-size: 22px;
            color: #1B3856;
            margin-bottom: 4px;
        }
        .header p {
            font-size: 11px;
            color: #76777d;
        }

        /* ── QR Grid (2 per row) ────────────────────── */
        .qr-grid {
            width: 100%;
        }
        .qr-row {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 15px;
        }
        .qr-cell {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }

        /* ── QR Card ────────────────────────────────── */
        .qr-card {
            border: 2px solid #1B3856;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            background: #fff;
            page-break-inside: avoid;
        }
        .qr-card .brand-name {
            font-size: 16px;
            font-weight: 700;
            color: #1B3856;
            margin-bottom: 2px;
        }
        .qr-card .scan-label {
            font-size: 11px;
            color: #76777d;
            margin-bottom: 12px;
        }
        .qr-card .qr-image {
            text-align: center;
            margin-bottom: 12px;
        }
        .qr-card .qr-image img {
            width: 160px;
            height: 160px;
        }
        .qr-card .table-number {
            font-size: 32px;
            font-weight: 700;
            color: #1B3856;
            margin-bottom: 6px;
        }
        .qr-card .instruction {
            font-size: 10px;
            color: #45464d;
            line-height: 1.4;
            margin-bottom: 10px;
        }
        .qr-card .qr-url {
            font-size: 9px;
            color: #76777d;
            padding-top: 8px;
            border-top: 1px solid #e2e2e2;
            word-break: break-all;
        }

        /* ── Footer ─────────────────────────────────── */
        .footer {
            text-align: center;
            font-size: 10px;
            color: #76777d;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e2e2e2;
        }

        /* ── Page break control ─────────────────────── */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>Smart Dining — QR Codes</h1>
        <p>{{ $tables->count() }} table(s) &bull; Generated: {{ now()->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
        @if($statusFilter === 'available')
            <p style="margin-top: 4px; font-size: 10px;">Filter: Hanya meja Available</p>
        @else
            <p style="margin-top: 4px; font-size: 10px;">Filter: Semua meja aktif (Available + Occupied)</p>
        @endif
    </div>

    {{-- QR Cards Grid — 2 per row --}}
    <div class="qr-grid">
        @foreach ($tables->chunk(2) as $chunkIndex => $pair)
            <div class="qr-row">
                @foreach ($pair as $table)
                    <div class="qr-cell">
                        <div class="qr-card">
                            <div class="brand-name">Smart Dining</div>
                            <div class="scan-label">Scan to Order</div>

                            <div class="qr-image">
                                <img src="{{ $qrImages[$table->id] }}" alt="QR Code Table {{ $table->table_number }}">
                            </div>

                            <div class="table-number">Table {{ $table->table_number }}</div>
                            <p class="instruction">
                                Scan QR code di atas dengan<br>
                                kamera HP untuk melihat menu<br>
                                dan memesan langsung.
                            </p>

                            <div class="qr-url">{{ $qrUrls[$table->id] }}</div>
                        </div>
                    </div>
                @endforeach

                {{-- Fill empty cell if odd --}}
                @if ($pair->count() === 1)
                    <div class="qr-cell"></div>
                @endif
            </div>

            {{-- Page break after every 3 rows (6 cards) --}}
            @if (($chunkIndex + 1) % 3 === 0 && !$loop->last)
                <div class="page-break"></div>
                <div class="header" style="margin-top: 10px;">
                    <h1>Smart Dining — QR Codes</h1>
                    <p>Continued — Page {{ floor($chunkIndex / 3) + 2 }}</p>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Generated on {{ now()->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB — Smart Dining &bull; SmartDining System</p>
    </div>
</body>
</html>
