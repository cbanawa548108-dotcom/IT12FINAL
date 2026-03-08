@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total Products Card -->
    <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-blue-600">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-gray-600 text-sm font-semibold uppercase mb-2">Total Products</h3>
                <div class="mt-2 space-y-1">
                    @if(($lowStockProducts->count() ?? 0) > 0)
                        <p class="text-xs text-yellow-600">⚠️ {{ $lowStockProducts->count() }} low stock</p>
                    @endif
                    @if(($outOfStockCount ?? 0) > 0)
                        <p class="text-xs text-red-600">🚫 {{ $outOfStockCount }} out of stock</p>
                    @endif
                </div>
            </div>
            <p class="text-4xl font-bold text-gray-800">{{ $totalProducts ?? 0 }}</p>
        </div>
    </div>

    <!-- Total Customers Card -->
    <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-purple-600">
        <div class="flex justify-between items-start">
            <h3 class="text-gray-600 text-sm font-semibold uppercase mb-2">Total Customers</h3>
            <p class="text-4xl font-bold text-gray-800">{{ $totalCustomers ?? 0 }}</p>
        </div>
    </div>

    <!-- Total Suppliers Card -->
    <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-indigo-600">
        <div class="flex justify-between items-start">
            <h3 class="text-gray-600 text-sm font-semibold uppercase mb-2">Total Suppliers</h3>
            <p class="text-4xl font-bold text-gray-800">{{ $totalSuppliers ?? 0 }}</p>
        </div>
    </div>

    <!-- Total Sales Card -->
    <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-teal-600">
        <div class="flex justify-between items-start">
            <h3 class="text-gray-600 text-sm font-semibold uppercase mb-2">Total Sales</h3>
            <p class="text-4xl font-bold text-gray-800">{{ $totalSales ?? 0 }}</p>
        </div>
    </div>
</div>

<!-- Revenue Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-emerald-600">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-gray-600 text-sm font-semibold uppercase mb-2">Today's Revenue</h3>
                <p class="text-gray-500 text-xs mt-2">{{ \Carbon\Carbon::now()->format('l, M d, Y') }}</p>
            </div>
            <p class="text-4xl font-bold text-gray-800">₱{{ number_format($todayRevenue ?? 0, 2) }}</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-cyan-600">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-gray-600 text-sm font-semibold uppercase mb-2">This Week</h3>
                <p class="text-gray-500 text-xs mt-2">
                    {{ \Carbon\Carbon::now()->startOfWeek()->format('M d') }} - {{ \Carbon\Carbon::now()->endOfWeek()->format('M d') }}
                </p>
            </div>
            <p class="text-4xl font-bold text-gray-800">₱{{ number_format($weekRevenue ?? 0, 2) }}</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-green-600">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-gray-600 text-sm font-semibold uppercase mb-2">This Month</h3>
                <p class="text-gray-500 text-xs mt-2">{{ \Carbon\Carbon::now()->format('F Y') }}</p>
            </div>
            <p class="text-4xl font-bold text-gray-800">₱{{ number_format($monthRevenue ?? 0, 2) }}</p>
        </div>
    </div>
</div>

<!-- ✅ Admin Security Buttons -->
@if(auth()->user()->role === 'admin')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <a href="{{ route('blocked-ips.index') }}"
       class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-red-600 hover:shadow-xl transition flex items-center group">
        <div class="bg-red-100 p-4 rounded-full mr-4 group-hover:bg-red-200 transition">
            <span class="material-icons text-red-600" style="font-size: 36px;">block</span>
        </div>
        <div>
            <h3 class="text-gray-800 font-bold text-lg">IP Blocking</h3>
            <p class="text-gray-500 text-sm">Manage blocked IP addresses</p>
        </div>
        <span class="material-icons ml-auto text-gray-400 group-hover:text-red-500 transition">arrow_forward</span>
    </a>

    <a href="{{ route('password-change-logs.index') }}"
       class="bg-white p-6 rounded-lg shadow-lg border-l-4 border-blue-600 hover:shadow-xl transition flex items-center group">
        <div class="bg-blue-100 p-4 rounded-full mr-4 group-hover:bg-blue-200 transition">
            <span class="material-icons text-blue-600" style="font-size: 36px;">lock_reset</span>
        </div>
        <div>
            <h3 class="text-gray-800 font-bold text-lg">Password Change Logs</h3>
            <p class="text-gray-500 text-sm">View all password change history</p>
        </div>
        <span class="material-icons ml-auto text-gray-400 group-hover:text-blue-500 transition">arrow_forward</span>
    </a>
</div>
@endif

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Revenue Chart -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-gray-800 font-bold text-xl flex items-center">
                <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Revenue Overview
            </h3>
        </div>

        <div class="mb-4 border rounded-lg p-3 bg-gray-50">
            <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div class="flex gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Date from</label>
                        <input type="date" id="revFilterFrom"
                               class="px-3 py-2 text-xs md:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Date to</label>
                        <input type="date" id="revFilterTo"
                               class="px-3 py-2 text-xs md:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" data-range="daily"
                            class="rev-quick-btn px-3 py-1.5 text-xs md:text-sm rounded-full border border-green-500 text-green-600 bg-white hover:bg-green-50">
                        Today
                    </button>
                    <button type="button" data-range="weekly"
                            class="rev-quick-btn px-3 py-1.5 text-xs md:text-sm rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100">
                        This Week
                    </button>
                    <button type="button" data-range="monthly"
                            class="rev-quick-btn px-3 py-1.5 text-xs md:text-sm rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100">
                        This Month
                    </button>
                </div>
            </div>
        </div>

        <div class="flex gap-2 mb-4 border-b">
            <button onclick="showChart('daily')" id="dailyTab"
                    class="px-4 py-2 font-semibold text-green-600 border-b-2 border-green-600 transition-all">
                Daily
            </button>
            <button onclick="showChart('weekly')" id="weeklyTab"
                    class="px-4 py-2 font-semibold text-gray-600 hover:text-green-600 transition-all">
                Weekly
            </button>
            <button onclick="showChart('monthly')" id="monthlyTab"
                    class="px-4 py-2 font-semibold text-gray-600 hover:text-green-600 transition-all">
                Monthly
            </button>
        </div>

        <div style="height: 300px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-gray-800 font-bold text-xl mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            Top Selling Products
        </h3>
        @if($topProducts && $topProducts->count() > 0)
            <div class="space-y-4">
                @foreach($topProducts as $index => $product)
                    @php
                        $colors = [
                            ['from' => 'amber-500', 'to' => 'amber-700'],
                            ['from' => 'blue-500', 'to' => 'blue-700'],
                            ['from' => 'purple-500', 'to' => 'purple-700'],
                            ['from' => 'pink-500', 'to' => 'pink-700'],
                            ['from' => 'indigo-500', 'to' => 'indigo-700']
                        ];
                        $color = $colors[$index % count($colors)];
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-green-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-{{ $color['from'] }} to-{{ $color['to'] }} text-white rounded-full flex items-center justify-center font-bold text-lg shadow-lg">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">{{ $product->name }}</p>
                                <p class="text-sm text-gray-600">{{ $product->total_sold }} units sold</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-green-600 text-lg">₱{{ number_format($product->total_revenue, 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-500 mt-4">No sales data yet</p>
            </div>
        @endif
    </div>
</div>

<!-- Low Stock Alert -->
@if($lowStockProducts && $lowStockProducts->count() > 0)
    <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
        <div class="flex items-center gap-2 mb-6">
            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h3 class="text-gray-800 font-bold text-xl">Low Stock Alert</h3>
            <span class="ml-auto bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">{{ $lowStockProducts->count() }} Items</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($lowStockProducts as $product)
                        <tr class="hover:bg-red-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">{{ $product->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-red-600 font-bold text-lg">{{ $product->stock_quantity ?? $product->Quantity_in_Stock }}</span>
                                <span class="text-gray-500 text-sm ml-1">units</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700 font-medium">₱{{ number_format($product->unit_price ?? 0, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $qty = $product->stock_quantity ?? $product->Quantity_in_Stock; @endphp
                                @if($qty <= 5)
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-800 border border-red-200">🚨 CRITICAL</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">⚠️ LOW</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- Expiry Alerts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Expired Products -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center gap-2 mb-6">
            <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-red-600 font-bold text-xl">Expired Products</h3>
        </div>
        @if($expiredProducts && $expiredProducts->count() > 0)
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @foreach($expiredProducts as $product)
                    <div class="flex justify-between items-center p-4 bg-red-50 rounded-lg border-l-4 border-red-600 hover:shadow-md transition-shadow">
                        <div>
                            <p class="font-bold text-gray-800">{{ $product->Product_Name }}</p>
                            <p class="text-sm text-gray-600 mt-1">Expired: {{ \Carbon\Carbon::parse($product->expiry_date)->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500 mt-1">Stock: {{ $product->Quantity_in_Stock }} units</p>
                        </div>
                        <span class="text-red-700 font-bold text-sm px-4 py-2 bg-red-200 rounded-lg">EXPIRED</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500 mt-4 font-medium">No expired products</p>
            </div>
        @endif
    </div>

    <!-- Expiring Soon -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center gap-2 mb-6">
            <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-orange-600 font-bold text-xl">Expiring Soon (Within 7 Days)</h3>
        </div>
        @if($expiringSoonProducts && $expiringSoonProducts->count() > 0)
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @foreach($expiringSoonProducts as $product)
                    <div class="flex justify-between items-center p-4 bg-orange-50 rounded-lg border-l-4 border-orange-600 hover:shadow-md transition-shadow">
                        <div>
                            <p class="font-bold text-gray-800">{{ $product->Product_Name }}</p>
                            <p class="text-sm text-gray-600 mt-1">Expires: {{ \Carbon\Carbon::parse($product->expiry_date)->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500 mt-1">Stock: {{ $product->Quantity_in_Stock }} units</p>
                        </div>
                        <span class="text-orange-700 font-bold text-sm px-4 py-2 bg-orange-200 rounded-lg">
                            {{ $product->days_until_expiry }} {{ $product->days_until_expiry == 1 ? 'day' : 'days' }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500 mt-4 font-medium">No products expiring soon</p>
            </div>
        @endif
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
<script>
    const chartData = {
        daily:   { labels: @json($dailyLabels ?? []),   data: @json($dailyData ?? []) },
        weekly:  { labels: @json($weeklyLabels ?? []),  data: @json($weeklyRevenue ?? []) },
        monthly: { labels: @json($monthlyLabels ?? []), data: @json($monthlyRevenue ?? []) }
    };

    let revenueChart = null;
    let currentView = 'daily';

    function createChart(type) {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        if (revenueChart) revenueChart.destroy();

        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData[type].labels,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: chartData[type].data,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointBackgroundColor: 'rgb(34, 197, 94)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ₱' + context.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: function(value) { return '₱' + value.toLocaleString(); } },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    function showChart(type) {
        currentView = type;
        createChart(type);
        const tabs = ['dailyTab', 'weeklyTab', 'monthlyTab'];
        const types = ['daily', 'weekly', 'monthly'];
        tabs.forEach((tabId, index) => {
            const tab = document.getElementById(tabId);
            tab.className = types[index] === type
                ? 'px-4 py-2 font-semibold text-green-600 border-b-2 border-green-600 transition-all'
                : 'px-4 py-2 font-semibold text-gray-600 hover:text-green-600 transition-all';
        });
        highlightQuickButton(type);
    }

    function highlightQuickButton(range) {
        document.querySelectorAll('.rev-quick-btn').forEach(btn => {
            if (btn.dataset.range === range) {
                btn.classList.remove('border-gray-300', 'text-gray-600', 'bg-white');
                btn.classList.add('border-green-500', 'text-green-600', 'bg-green-50');
            } else {
                btn.classList.remove('border-green-500', 'text-green-600', 'bg-green-50');
                btn.classList.add('border-gray-300', 'text-gray-600', 'bg-white');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        createChart('daily');
        highlightQuickButton('daily');

        document.querySelectorAll('.rev-quick-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                showChart(btn.dataset.range);
                document.getElementById('revFilterFrom').value = '';
                document.getElementById('revFilterTo').value = '';
            });
        });

        document.getElementById('revFilterFrom').addEventListener('change', () => {
            document.querySelectorAll('.rev-quick-btn').forEach(btn => {
                btn.classList.remove('border-green-500', 'text-green-600', 'bg-green-50');
                btn.classList.add('border-gray-300', 'text-gray-600', 'bg-white');
            });
        });

        document.getElementById('revFilterTo').addEventListener('change', () => {
            document.querySelectorAll('.rev-quick-btn').forEach(btn => {
                btn.classList.remove('border-green-500', 'text-green-600', 'bg-green-50');
                btn.classList.add('border-gray-300', 'text-gray-600', 'bg-white');
            });
        });
    });
</script>
@endsection