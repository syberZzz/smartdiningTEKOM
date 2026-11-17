<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Report {{ $startDate }} to {{ $endDate }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #1a1c1c; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1B3856; padding-bottom: 15px; }
        .header h1 { font-size: 22px; color: #1B3856; margin-bottom: 4px; }
        .header p { font-size: 12px; color: #76777d; }
        .section { margin-bottom: 25px; }
        .section h2 { font-size: 16px; color: #1B3856; margin-bottom: 10px; padding-bottom: 5px; border-bottom: 1px solid #e2e2e2; }
        .summary-grid { display: table; width: 100%; margin-bottom: 20px; }
        .summary-item { display: table-cell; text-align: center; padding: 10px; border: 1px solid #e2e2e2; }
        .summary-item .value { font-size: 18px; font-weight: bold; color: #1B3856; }
        .summary-item .label { font-size: 10px; color: #76777d; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #f3f3f4; color: #45464d; font-size: 11px; font-weight: 600; text-align: left; padding: 8px 10px; border: 1px solid #e2e2e2; }
        td { padding: 7px 10px; border: 1px solid #e2e2e2; font-size: 11px; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { text-align: center; font-size: 10px; color: #76777d; margin-top: 30px; padding-top: 15px; border-top: 1px solid #e2e2e2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Smart Dining — Business Report</h1>
        <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    {{-- Summary --}}
    <div class="section">
        <h2>Summary</h2>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="value">Rp{{ number_format($summary['revenue'], 0, ',', '.') }}</div>
                <div class="label">Revenue (Inc. Tax)</div>
            </div>
            <div class="summary-item">
                <div class="value">Rp{{ number_format($summary['total_tax'] ?? 0, 0, ',', '.') }}</div>
                <div class="label">Total Tax Collected</div>
            </div>
            <div class="summary-item">
                <div class="value">{{ $summary['orders'] }}</div>
                <div class="label">Total Orders</div>
            </div>
            <div class="summary-item">
                <div class="value">{{ $summary['customers'] }}</div>
                <div class="label">Customers</div>
            </div>
            <div class="summary-item">
                <div class="value">Rp{{ number_format($summary['avg_transaction'], 0, ',', '.') }}</div>
                <div class="label">Avg Transaction</div>
            </div>
        </div>
    </div>

    {{-- Best Selling Menu --}}
    <div class="section">
        <h2>Best Selling Menu</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Menu Name</th>
                    <th>Total Sold</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bestSelling as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->total_sold }}</td>
                        <td>Rp{{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align: center;">No data available</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Category Report --}}
    <div class="section">
        <h2>Category Report</h2>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Total Menu</th>
                    <th>Sold</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categoryReport as $row)
                    <tr>
                        <td>{{ $row->category }}</td>
                        <td>{{ $row->total_menu }}</td>
                        <td>{{ $row->total_sold }}</td>
                        <td>Rp{{ number_format($row->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align: center;">No data available</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Payment Summary --}}
    <div class="section">
        <h2>Payment Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Method</th>
                    <th>Transactions</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Cash</td>
                    <td>{{ $paymentSummary['cash']['count'] }}</td>
                    <td>Rp{{ number_format($paymentSummary['cash']['total'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Digital (QRIS, E-Wallet, Card)</td>
                    <td>{{ $paymentSummary['digital']['count'] }}</td>
                    <td>Rp{{ number_format($paymentSummary['digital']['total'], 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>




    <div class="footer">
        <p>Generated on {{ now()->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB — Smart Dining</p>
    </div>
</body>
</html>
