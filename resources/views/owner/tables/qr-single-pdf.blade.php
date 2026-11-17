<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>QR Code — Table {{ $table->table_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a1c1c;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px;
        }

        .wrapper {
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
            text-align: center;
        }

        /* ── Card ────────────────────── */
        .qr-card {
            border: 3px solid #1B3856;
            border-radius: 20px;
            padding: 32px 28px 24px;
            background: #fff;
        }

        .brand-name {
            font-size: 22px;
            font-weight: 700;
            color: #1B3856;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
        }

        .scan-label {
            font-size: 12px;
            color: #76777d;
            margin-bottom: 20px;
        }

        .qr-image {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            background: #fff;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #e2e2e2;
        }

        .qr-image img {
            width: 220px;
            height: 220px;
        }

        .table-number {
            font-size: 40px;
            font-weight: 700;
            color: #1B3856;
            margin-bottom: 6px;
        }

        .table-capacity {
            font-size: 13px;
            color: #76777d;
            margin-bottom: 16px;
        }

        .instruction {
            font-size: 12px;
            color: #45464d;
            line-height: 1.6;
            margin-bottom: 18px;
        }

        .divider {
            border: none;
            border-top: 1px solid #e2e2e2;
            margin-bottom: 12px;
        }

        .qr-url {
            font-size: 10px;
            color: #76777d;
            word-break: break-all;
        }

        /* ── Footer ─────────────────── */
        .footer {
            margin-top: 16px;
            font-size: 10px;
            color: #76777d;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="qr-card">
            <div class="brand-name">Smart Dining</div>
            <div class="scan-label">Scan to Order</div>

            <div class="qr-image">
                <img src="{{ $qrImage }}" alt="QR Code Table {{ $table->table_number }}">
            </div>

            <div class="table-number">Table {{ $table->table_number }}</div>
            <div class="table-capacity">Kapasitas: {{ $table->capacity }} orang</div>

            <p class="instruction">
                Scan QR code di atas dengan kamera HP<br>
                untuk melihat menu dan memesan langsung.
            </p>

            <hr class="divider">
            <div class="qr-url">{{ $qrUrl }}</div>
        </div>

        <div class="footer">
            Generated: {{ now()->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB &bull; Smart Dining SmartDining
        </div>
    </div>
</body>
</html>
