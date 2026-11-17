@extends('layouts.owner')

@section('title', 'Laporan')
@section('page-title', 'Laporan')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-headline-lg-mobile lg:text-headline-lg text-primary">Laporan</h1>
            <p class="text-body-md text-on-surface-variant mt-1">Analitik bisnis dan metrik kinerja</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('owner.reports.export.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-label-md text-red-600 border border-red-200 hover:bg-red-50 transition-colors">
                <span class="material-symbols-outlined text-[18px]">picture_as_pdf</span>
                Export PDF
            </a>
        </div>
    </div>

    {{-- Period Filter --}}
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('owner.reports.index') }}" class="flex flex-col sm:flex-row gap-3 items-end">
            <div>
                <label class="block text-label-sm text-on-surface-variant mb-1">Periode</label>
                <select name="period" onchange="toggleCustomDates(this.value)"
                        class="px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors">
                    <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Rentang Kustom</option>
                </select>
            </div>
            <div id="custom-dates" class="flex gap-3 {{ $period !== 'custom' ? 'hidden' : '' }}">
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                           class="px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors" />
                </div>
                <div>
                    <label class="block text-label-sm text-on-surface-variant mb-1">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                           class="px-4 py-2.5 border border-outline-variant rounded-xl bg-surface text-body-md text-on-surface focus:outline-none focus:border-primary transition-colors" />
                </div>
            </div>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-xl text-label-md hover:opacity-90 transition-opacity shadow-sm">
                <span class="material-symbols-outlined text-[18px]">filter_list</span>
                Terapkan
            </button>
        </form>
    </div>

    {{-- Dashboard Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm">
            <p class="text-label-sm text-on-surface-variant mb-2">Pendapatan</p>
            <p class="text-xl font-bold text-primary">Rp{{ number_format($summary['revenue'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm">
            <p class="text-label-sm text-on-surface-variant mb-2">Pesanan</p>
            <p class="text-xl font-bold text-primary">{{ $summary['orders'] }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm">
            <p class="text-label-sm text-on-surface-variant mb-2">Pelanggan</p>
            <p class="text-xl font-bold text-primary">{{ $summary['customers'] }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm">
            <p class="text-label-sm text-on-surface-variant mb-2">Rata-Rata Transaksi</p>
            <p class="text-xl font-bold text-primary">Rp{{ number_format($summary['avg_transaction'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm">
            <p class="text-label-sm text-on-surface-variant mb-2">Meja Aktif</p>
            <p class="text-xl font-bold text-primary">{{ $summary['active_tables'] }}</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div x-data="{ activeTab: 'bestselling' }" class="space-y-6">
        {{-- Tab Buttons --}}
        <div class="flex gap-1 bg-surface-container rounded-xl p-1 overflow-x-auto">
            @foreach (['bestselling' => 'Performa Produk', 'payment' => 'Pembayaran & Status', 'daily' => 'Laporan Harian', 'monthly' => 'Laporan Bulanan'] as $key => $label)
                <button @click="activeTab = '{{ $key }}'; setTimeout(() => window.dispatchEvent(new Event('resize')), 100);"
                        :class="activeTab === '{{ $key }}' ? 'bg-primary text-on-primary shadow-sm' : 'text-on-surface-variant hover:text-primary'"
                        class="px-4 py-2 rounded-lg text-label-md font-medium transition-all whitespace-nowrap">
                    {{ $label }}
                </button>
            @endforeach
        </div>


        {{-- Daily Report Tab --}}
        <div x-show="activeTab === 'daily'" style="display: none;" class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-outline-variant">
                <h3 class="text-headline-md text-primary">Laporan Harian</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-outline-variant bg-surface-container-low">
                            <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Tanggal</th>
                            <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Pendapatan</th>
                            <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Transaksi</th>
                            <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Pesanan QR</th>
                            <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Pesanan Kasir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse ($dailyReport as $row)
                            <tr class="hover:bg-surface-container-low transition-colors">
                                <td class="px-5 py-3 text-label-md text-primary">{{ \Carbon\Carbon::parse($row->date)->format('d M Y') }}</td>
                                <td class="px-5 py-3 text-label-md text-primary font-semibold">Rp{{ number_format($row->revenue, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-label-md text-on-surface">{{ $row->transactions }}</td>
                                <td class="px-5 py-3 text-label-md text-on-surface">{{ $row->self_orders }}</td>
                                <td class="px-5 py-3 text-label-md text-on-surface">{{ $row->cashier_orders }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-8 text-center text-on-surface-variant">Tidak ada data untuk periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Monthly Report Tab --}}
        <div x-show="activeTab === 'monthly'" style="display: none;">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Chart --}}
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm p-6">
                    <h3 class="text-headline-md text-primary mb-4">Pendapatan Bulanan</h3>
                    <div class="relative w-full h-[300px]">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                </div>
                {{-- Table --}}
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-outline-variant">
                        <h3 class="text-headline-md text-primary">Ringkasan Bulanan</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-outline-variant bg-surface-container-low">
                                    <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Bulan</th>
                                    <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Pendapatan</th>
                                    <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Pesanan</th>
                                    <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Pelanggan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant">
                                @php $months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']; @endphp
                                @forelse ($monthlyReport as $row)
                                    <tr class="hover:bg-surface-container-low transition-colors">
                                        <td class="px-5 py-3 text-label-md text-primary">{{ $months[$row->month] ?? $row->month }}</td>
                                        <td class="px-5 py-3 text-label-md text-primary font-semibold">Rp{{ number_format($row->revenue, 0, ',', '.') }}</td>
                                        <td class="px-5 py-3 text-label-md text-on-surface">{{ $row->orders }}</td>
                                        <td class="px-5 py-3 text-label-md text-on-surface">{{ $row->customers }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-5 py-8 text-center text-on-surface-variant">Tidak ada data tersedia.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Best Selling & Product Performance Tab --}}
        <div x-show="activeTab === 'bestselling'" style="display: none;" class="space-y-6">
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-outline-variant">
                    <h3 class="text-headline-md text-primary">Menu Terlaris (Top Selling)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-outline-variant bg-surface-container-low">
                                <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">#</th>
                                <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Gambar</th>
                                <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Menu</th>
                                <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Terjual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant">
                            @forelse (collect($bestSelling)->take(10) as $index => $item)
                                <tr class="hover:bg-surface-container-low transition-colors">
                                    <td class="px-5 py-3 text-label-md text-on-surface-variant font-bold">{{ $index + 1 }}</td>
                                    <td class="px-5 py-3"><img src="{{ $item->image }}" alt="{{ $item->name }}" class="w-10 h-10 rounded-lg object-cover" /></td>
                                    <td class="px-5 py-3 text-label-md text-primary font-semibold">{{ $item->name }}</td>
                                    <td class="px-5 py-3 text-label-md text-on-surface">{{ $item->total_sold }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-8 text-center text-on-surface-variant">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Category Report Tab --}}
        <div x-show="activeTab === 'category'" style="display: none;"
             class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-outline-variant">
                <h3 class="text-headline-md text-primary">Laporan Kategori</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-outline-variant bg-surface-container-low">
                            <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Kategori</th>
                            <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Total Menu</th>
                            <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Terjual</th>
                            <th class="px-5 py-3 text-label-sm text-on-surface-variant font-semibold">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse ($categoryReport as $row)
                            <tr class="hover:bg-surface-container-low transition-colors">
                                <td class="px-5 py-3 text-label-md text-primary font-semibold">{{ $row->category }}</td>
                                <td class="px-5 py-3 text-label-md text-on-surface">{{ $row->total_menu }}</td>
                                <td class="px-5 py-3 text-label-md text-on-surface">{{ $row->total_sold }}</td>
                                <td class="px-5 py-3 text-label-md text-primary font-semibold">Rp{{ number_format($row->total_revenue, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-8 text-center text-on-surface-variant">Tidak ada data kategori tersedia.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>



        {{-- Payment Summary Tab --}}
        <div x-show="activeTab === 'payment'" style="display: none;" class="space-y-6">
            <h3 class="text-headline-md text-primary">Volume Berdasarkan Metode Pembayaran</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="material-symbols-outlined text-green-600 text-[28px] bg-green-50 p-2.5 rounded-xl" style="font-variation-settings: 'FILL' 1;">payments</span>
                        <div>
                            <p class="text-label-sm text-on-surface-variant">Tunai (Cash)</p>
                            <p class="text-xl font-bold text-primary">Rp{{ number_format($paymentSummary['cash']['total'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <p class="text-label-md text-on-surface-variant">{{ $paymentSummary['cash']['count'] }} transaksi</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="material-symbols-outlined text-blue-600 text-[28px] bg-blue-50 p-2.5 rounded-xl" style="font-variation-settings: 'FILL' 1;">qr_code_2</span>
                        <div>
                            <p class="text-label-sm text-on-surface-variant">Digital (QRIS, E-Wallet, Card)</p>
                            <p class="text-xl font-bold text-primary">Rp{{ number_format($paymentSummary['digital']['total'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <p class="text-label-md text-on-surface-variant">{{ $paymentSummary['digital']['count'] }} transaksi</p>
                </div>
            </div>

            <h3 class="text-headline-md text-primary pt-4 border-t border-outline-variant">Status Pesanan & Pembayaran</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm p-5 border-l-4 border-l-green-500">
                    <p class="text-label-sm text-on-surface-variant mb-1">Lunas (Paid)</p>
                    <p class="text-2xl font-bold text-primary">{{ $paymentStatus['paid'] }}</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm p-5 border-l-4 border-l-yellow-500">
                    <p class="text-label-sm text-on-surface-variant mb-1">Pay at Cashier</p>
                    <p class="text-2xl font-bold text-primary">{{ $paymentStatus['pay_at_cashier'] }}</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant shadow-sm p-5 border-l-4 border-l-red-500">
                    <p class="text-label-sm text-on-surface-variant mb-1">Cancelled / Refunded</p>
                    <p class="text-2xl font-bold text-primary">{{ $paymentStatus['cancelled'] }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleCustomDates(value) {
        document.getElementById('custom-dates').classList.toggle('hidden', value !== 'custom');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Revenue Chart
        const monthlyData = @json($monthlyReport);
        const months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        const monthlyCtx = document.getElementById('monthlyRevenueChart');
        if (monthlyCtx) {
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(d => months[d.month] || d.month),
                    datasets: [{
                        label: 'Revenue',
                        data: monthlyData.map(d => d.revenue),
                        borderColor: '#1B3856',
                        backgroundColor: 'rgba(27, 56, 86, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#C5A059',
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: v => 'Rp' + (v/1000).toLocaleString('id-ID') + 'K' }
                        }
                    }
                }
            });
        }


    });
</script>
@endpush
