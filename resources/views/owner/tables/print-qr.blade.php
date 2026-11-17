<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Print QR — Table {{ $table->table_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #fff;
        }
        .qr-card {
            text-align: center;
            padding: 40px;
            border: 2px solid #1B3856;
            border-radius: 24px;
            max-width: 400px;
            width: 100%;
        }
        .qr-card h1 {
            font-size: 24px;
            font-weight: 700;
            color: #1B3856;
            margin-bottom: 4px;
        }
        .qr-card .subtitle {
            font-size: 14px;
            color: #76777d;
            margin-bottom: 24px;
        }
        .qr-card .qr-image {
            display: flex;
            justify-content: center;
            margin-bottom: 24px;
        }
        .qr-card .qr-image svg {
            width: 280px;
            height: 280px;
        }
        .qr-card .table-number {
            font-size: 48px;
            font-weight: 700;
            color: #1B3856;
            margin-bottom: 8px;
        }
        .qr-card .instruction {
            font-size: 14px;
            color: #45464d;
            line-height: 1.6;
        }
        .qr-card .brand {
            font-size: 12px;
            color: #76777d;
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #e2e2e2;
        }
        @media print {
            body { background: #fff; }
            .qr-card { border: 2px solid #1B3856; }
        }
    </style>
</head>
<body>
    <div class="qr-card">
        <h1>Smart Dining</h1>
        <p class="subtitle">Scan to Order</p>

        <div class="qr-image">
            {!! $qrSvg !!}
        </div>

        <div class="table-number">Table {{ $table->table_number }}</div>
        <p class="instruction">
            Scan the QR code above with your<br>
            phone camera to view our menu<br>
            and place your order.
        </p>

        <p class="brand">{{ $qrUrl }}</p>
    </div>

    <script>
        window.onload = function() { window.print(); };
    </script>
</body>
</html>
