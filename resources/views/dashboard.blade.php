@extends('layouts.app')

@section('title', 'Dashboard - Gapuro System')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="mb-2 text-2xl font-bold text-gray-700">WELCOME TO GAPURO SITE.</h2>
        <p class="text-sm text-gray-500">Current Period : 2025-10-01 - 2025-12-31</p>
    </div>

    <div class="flex flex-col gap-6">
        <section class="order-2 rounded border border-gray-200 bg-gray-50 p-4">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-2xl font-black text-slate-800 tracking-wide">FG STORAGE AREA MAP</h3>
            </div>

            <style>
                .fg-map {
                    background: none;
                }

                .fg-map .cell {
                    position: absolute;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                    border: 1px solid #545454;
                    font-weight: 700;
                    line-height: 1.1;
                    overflow: hidden;
                    word-break: break-word;
                    padding: 0.12rem 0.18rem;
                }

                .fg-map .v {
                    writing-mode: vertical-rl;
                    text-orientation: mixed;
                    white-space: nowrap;
                    line-height: 1;
                    letter-spacing: 0.01em;
                }

                .fg-map .sm {
                    font-size: clamp(8px, 0.6vw, 11px);
                }

                .fg-map .md {
                    font-size: clamp(9px, 0.75vw, 13px);
                }

                .fg-map .lg {
                    font-size: clamp(10px, 1vw, 16px);
                }

                .fg-map .xl {
                    font-size: clamp(11px, 1.2vw, 20px);
                }

                .fg-map .xxl {
                    font-size: clamp(12px, 1.4vw, 24px);
                }
            </style>

            <div class="mx-auto w-full max-w-[1320px] mb-5">
                <div class="fg-map relative mx-auto aspect-[16/7] w-full overflow-hidden text-white">
                    <div class="cell md" style="left:1.8%;top:8%;width:6.4%;height:10%;background:#585858;">Bottle<br>20ml</div>
                    <div class="cell md" style="left:8.4%;top:8%;width:6.4%;height:10%;background:#505050;">Bottle<br>40ml</div>
                    <div class="cell xl" style="left:16.5%;top:8%;width:31.5%;height:10%;background:#7ad255;border-color:#487d36;color:#f4f9e8;">PRISM INJ</div>
                    <div class="cell lg" style="left:60%;top:8%;width:11.3%;height:10%;background:#ffd321;border-color:#8a751d;color:#1a1a1a;">PART RETURN</div>

                    <div class="cell md" style="left:1.8%;top:22%;width:13%;height:8%;background:#9f9f9f;">Bottle 70ml</div>
                    <div class="cell md" style="left:1.8%;top:30.5%;width:13%;height:8%;background:#a3a3a3;">Bottle 70ml</div>
                    <div class="cell md" style="left:1.8%;top:43.5%;width:13%;height:8%;background:#a3a3a3;">Bottle 70ml</div>
                    <div class="cell md" style="left:1.8%;top:52%;width:13%;height:8%;background:#a3a3a3;">Bottle 70ml</div>
                    <div class="cell md" style="left:1.8%;top:65%;width:13%;height:8%;background:#a3a3a3;">Bottle 70ml</div>
                    <div class="cell md" style="left:1.8%;top:73.5%;width:13%;height:8%;background:#a3a3a3;">Bottle 70ml</div>

                    <div class="cell lg v" style="left:16.5%;top:21%;width:3.5%;height:58%;background:#a7a7a7;color:#efefef;">Bottle 70ml</div>
                    <div class="cell xl v" style="left:20.2%;top:21%;width:3.3%;height:58%;background:#000;color:#f0f0f0;">SAKURA</div>
                    <div class="cell lg" style="left:23.8%;top:21%;width:5.3%;height:19%;background:#f3ba58;color:#fafafa;">N3 COVER</div>
                    <div class="cell lg" style="left:23.8%;top:42%;width:5.3%;height:19%;background:#f3ba58;color:#fafafa;">N3 COVER M</div>
                    <div class="cell xl" style="left:29.2%;top:21%;width:8.6%;height:58%;background:#e95bb9;color:#fff;">NASUNO 3 CASE</div>
                    <div class="cell lg" style="left:37.9%;top:21%;width:5.7%;height:58%;background:#e95bb9;color:#fff;">N3 CASE M1, M2</div>
                    <div class="cell md v" style="left:43.7%;top:21%;width:3.1%;height:58%;background:#8f8f8f;color:#ececec;">CRAIG CASE L</div>
                    <div class="cell md v" style="left:46.9%;top:21%;width:6.2%;height:58%;background:#9f9f9f;color:#ececec;">CRAIG CASE S 3,4,5</div>
                    <div class="cell sm v" style="left:53.3%;top:21%;width:2.5%;height:58%;background:#0abb63;color:#e8fff2;">HA3P CASE S2 / M</div>
                    <div class="cell sm v" style="left:56%;top:21%;width:2.5%;height:58%;background:#59cad2;color:#e8ffff;">HA3P CASE S2</div>

                    <div class="cell md v" style="left:60%;top:21%;width:5%;height:29%;background:#1159b5;color:#f4f9ff;">INK BOTTLE CAP</div>
                    <div class="cell md v" style="left:60%;top:50.1%;width:5%;height:28.9%;background:#1152a5;color:#f4f9ff;">TOP CAP</div>
                    <div class="cell md v" style="left:65.2%;top:21%;width:6.2%;height:58%;background:#5a5a5a;color:#ececec;">HAMANA GLEE</div>
                    <div class="cell md v" style="left:71.6%;top:21%;width:6.2%;height:58%;background:#5a5a5a;color:#ececec;">HAMANA GROW</div>

                    <div class="cell xl v" style="left:79%;top:21%;width:6.2%;height:43%;background:#2404b8;color:#f6f1a6;">SPOUT</div>
                    <div class="cell md v" style="left:79%;top:64.5%;width:6.2%;height:14.5%;background:#ff7a19;color:#fff3dc;">MASHU</div>
                    <div class="cell xl v" style="left:86.4%;top:21%;width:5.4%;height:58%;background:#2204b8;color:#f6f1a6;">SPOUT</div>

                    <div class="cell md v" style="left:93%;top:21%;width:5.5%;height:14%;background:#2204b8;color:#fff4df;">SPOUT</div>
                    <div class="cell xl v" style="left:93%;top:35.2%;width:5.5%;height:22%;background:#79d34f;color:#e8ffd9;">S15</div>
                    <div class="cell xl v" style="left:93%;top:57.5%;width:5.5%;height:18%;background:#e30000;color:#ffe4d5;">A3</div>
                    <div class="cell lg v" style="left:93%;top:75.8%;width:5.5%;height:9%;background:#1d1bb3;color:#e2dcff;">FB</div>
                    <div class="cell md v" style="left:93%;top:85%;width:5.5%;height:9%;background:#303138;color:#ececec;">ADF</div>
                    <div class="cell lg v" style="left:93%;top:94.2%;width:5.5%;height:5.8%;background:#d50000;color:#ffe7e7;">LG</div>

                    <div class="cell lg" style="left:1.8%;top:81.8%;width:9.5%;height:11%;background:#cdcdcd;color:#1f1f1f;">TRAY CLEANING</div>
                    <div class="cell xl" style="left:11.5%;top:81.8%;width:15.2%;height:11%;background:#bdbdbd;color:#1f1f1f;">EMPTY BOX</div>
                    <div class="cell xl" style="left:43.5%;top:81.8%;width:15%;height:11%;background:#a9a9a9;color:#f6d74f;">CRAIG COVER L</div>
                    <div class="cell lg" style="left:60%;top:81.8%;width:18.8%;height:11%;background:#a8a8a8;color:#f6d74f;font-size:clamp(10px,1vw,17px);">NASUNO CASE B</div>
                    <div class="cell md" style="left:78.9%;top:81.8%;width:6.2%;height:11%;background:#a8a8a8;color:#f6d74f;font-size:clamp(9px,0.78vw,13px);line-height:1.15;">COVER S, M</div>
                    <div class="cell lg" style="left:60%;top:90.3%;width:25.1%;height:6.2%;background:#8c5800;border-color:#5f3200;color:#f7e8cd;font-size:clamp(11px,1.05vw,18px);">PRISM COATING</div>
                </div>
            </div>
        </section>

        @php
        $fgMetrics = $fgStorageMetrics ?? [];
        $todayReceiving = data_get($fgMetrics, 'today.receiving', ['rows' => 0, 'qty' => 0]);
        $todayDelivery = data_get($fgMetrics, 'today.delivery', ['rows' => 0, 'qty' => 0]);
        $stockMetrics = data_get($fgMetrics, 'stock', []);
        $itemTypeCapacities = data_get($fgMetrics, 'item_type_capacities', []);
        $dailyFlow = data_get($fgMetrics, 'daily_flow', []);
        $maxFlowQty = (int) collect($dailyFlow)
        ->map(fn($row) => max((int) data_get($row, 'receiving_qty', 0), (int) data_get($row, 'delivery_qty', 0)))
        ->max();
        $maxFlowQty = max(1, $maxFlowQty);
        $lastUpdate = data_get($fgMetrics, 'as_of');
        @endphp

        <section
            id="fg-live-dashboard"
            data-endpoint="{{ route('dashboard.fg-storage.metrics') }}"
            data-polling-ms="{{ max(5000, ((int) data_get($fgMetrics, 'meta.polling_seconds', 15) * 1000)) }}"
            class="order-1 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Realtime Dashboard</p>
                    <h3 class="mt-1 text-2xl font-black text-slate-800">FG Storage Live Monitoring</h3>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">
                    <span id="fg-live-indicator" class="inline-flex h-2.5 w-2.5 animate-pulse rounded-full bg-amber-500"></span>
                    <span>Last update: <span id="fg-last-update">{{ $lastUpdate ? \Illuminate\Support\Carbon::parse($lastUpdate)->format('d/m/Y H:i:s') : '-' }}</span></span>
                </div>
            </div>

            <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-500 to-teal-600 p-4 text-white shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-100">Receive Today</p>
                    <p id="fg-receiving-qty" class="mt-3 text-3xl font-black leading-none">{{ number_format((int) data_get($todayReceiving, 'qty', 0)) }} box</p>
                    <p id="fg-receiving-rows" class="mt-2 text-xs text-emerald-100">{{ number_format((int) data_get($todayReceiving, 'rows', 0)) }} transaksi</p>
                </article>

                <article class="rounded-2xl border border-cyan-200 bg-gradient-to-br from-cyan-500 to-blue-600 p-4 text-white shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-100">Delivery Today</p>
                    <p id="fg-delivery-qty" class="mt-3 text-3xl font-black leading-none">{{ number_format((int) data_get($todayDelivery, 'qty', 0)) }} box</p>
                    <p id="fg-delivery-rows" class="mt-2 text-xs text-cyan-100">{{ number_format((int) data_get($todayDelivery, 'rows', 0)) }} transaksi</p>
                </article>

                <article class="rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-500 to-indigo-600 p-4 text-white shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-violet-100">Current Stock</p>
                    <p id="fg-stock-qty" class="mt-3 text-3xl font-black leading-none">{{ number_format((int) data_get($stockMetrics, 'qty', 0)) }} box</p>
                    <p id="fg-stock-rows" class="mt-2 text-xs text-violet-100">{{ number_format((int) data_get($stockMetrics, 'rows', 0)) }} lot aktif</p>
                </article>

                <article class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-400 to-orange-500 p-4 text-slate-900 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-amber-900/80">Available Capacity</p>
                    <p id="fg-available-qty" class="mt-3 text-3xl font-black leading-none">{{ number_format((int) data_get($stockMetrics, 'available_qty', 0)) }} box</p>
                    <p id="fg-capacity-label" class="mt-2 text-xs text-amber-900/80">dari total {{ number_format((int) data_get($stockMetrics, 'capacity_qty', 0)) }} box</p>
                </article>
            </div>

            <div class="mt-5 rounded-2xl border border-slate-700 bg-slate-700 p-4 text-slate-100 shadow-inner">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-[0.12em] text-cyan-300">Realtime Trend Monitor</h4>
                    </div>
                    <div class="flex items-center gap-3 text-[11px] text-slate-300">
                        <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-emerald-400"></span>Receive</span>
                        <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-sky-400"></span>Delivery</span>
                        <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-amber-400"></span>Stock</span>
                    </div>
                </div>

                <div id="fg-trend-chart-wrap" class="mt-3 h-72 w-full rounded-xl border border-slate-700 bg-slate-900/80 p-2">
                    <svg id="fg-trend-chart" class="h-full w-full"></svg>
                </div>

                <div class="mt-3 grid gap-2 text-xs text-slate-300 sm:grid-cols-4">
                    <p>Date: <strong id="fg-trend-last-date" class="text-slate-100">-</strong></p>
                    <p>Receive: <strong id="fg-trend-last-receive" class="text-emerald-300">0 box</strong></p>
                    <p>Delivery: <strong id="fg-trend-last-delivery" class="text-sky-300">0 box</strong></p>
                    <p>Stock: <strong id="fg-trend-last-stock" class="text-amber-300">0 box</strong></p>
                </div>
            </div>

            <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                <div class="mb-2 flex items-center justify-between text-xs font-semibold text-slate-600">
                    <span>Warehouse Capacity Usage</span>
                    <span id="fg-used-percent">{{ number_format((float) data_get($stockMetrics, 'used_percent', 0), 1) }}%</span>
                </div>
                <div class="h-3 w-full rounded-full bg-slate-200">
                    <div
                        id="fg-used-bar"
                        class="{{ (bool) data_get($stockMetrics, 'over_capacity', false) ? 'bg-rose-500' : 'bg-gradient-to-r from-emerald-500 via-cyan-500 to-indigo-500' }} h-3 rounded-full transition-all duration-500"
                        style="width: {{ number_format((float) data_get($stockMetrics, 'used_percent_for_bar', 0), 1, '.', '') }}%"></div>
                </div>
                <div class="mt-2 grid gap-2 text-xs text-slate-600 sm:grid-cols-3">
                    <p>Terpakai: <strong id="fg-used-qty" class="text-slate-800">{{ number_format((int) data_get($stockMetrics, 'qty', 0)) }} box</strong></p>
                    <p>Sisa: <strong id="fg-remaining-qty" class="text-slate-800">{{ number_format((int) data_get($stockMetrics, 'available_qty', 0)) }} box</strong></p>
                    <p>Status: <strong id="fg-capacity-status" class="{{ (bool) data_get($stockMetrics, 'over_capacity', false) ? 'text-rose-600' : 'text-emerald-700' }}">{{ (bool) data_get($stockMetrics, 'over_capacity', false) ? 'OVER CAPACITY' : 'Within Capacity' }}</strong></p>
                </div>
            </div>

            <div class="mt-5 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-xs">
                    <thead class="bg-slate-100 text-[11px] uppercase tracking-[0.12em] text-slate-600">
                        <tr>
                            <th class="px-3 py-2 text-left">Date</th>
                            <th class="px-3 py-2 text-right">Receive Qty</th>
                            <th class="px-3 py-2 text-right">Delivery Qty</th>
                            <th class="px-3 py-2 text-right">Receive Txn</th>
                            <th class="px-3 py-2 text-right">Delivery Txn</th>
                            <th class="px-3 py-2 text-right">Net Flow</th>
                            <th class="px-3 py-2 text-left">Visual</th>
                        </tr>
                    </thead>
                    <tbody id="fg-flow-tbody" class="divide-y divide-slate-100 bg-white text-[12px] text-slate-700">
                        @forelse ($dailyFlow as $row)
                        @php
                        $receiveQty = (int) data_get($row, 'receiving_qty', 0);
                        $deliveryQty = (int) data_get($row, 'delivery_qty', 0);
                        $receiveRows = (int) data_get($row, 'receiving_rows', 0);
                        $deliveryRows = (int) data_get($row, 'delivery_rows', 0);
                        $netQty = (int) data_get($row, 'net_qty', 0);
                        $dateLabel = \Illuminate\Support\Carbon::parse((string) data_get($row, 'date'))->format('d M Y');
                        $receiveWidth = number_format(($receiveQty / $maxFlowQty) * 100, 1, '.', '');
                        $deliveryWidth = number_format(($deliveryQty / $maxFlowQty) * 100, 1, '.', '');
                        @endphp
                        <tr class="hover:bg-slate-50/80">
                            <td class="whitespace-nowrap px-3 py-2">{{ $dateLabel }}</td>
                            <td class="whitespace-nowrap px-3 py-2 text-right font-semibold text-emerald-700">{{ number_format($receiveQty) }}</td>
                            <td class="whitespace-nowrap px-3 py-2 text-right font-semibold text-cyan-700">{{ number_format($deliveryQty) }}</td>
                            <td class="whitespace-nowrap px-3 py-2 text-right">{{ number_format($receiveRows) }}</td>
                            <td class="whitespace-nowrap px-3 py-2 text-right">{{ number_format($deliveryRows) }}</td>
                            <td class="whitespace-nowrap px-3 py-2 text-right {{ $netQty >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ number_format($netQty) }}</td>
                            <td class="px-3 py-2">
                                <div class="space-y-1">
                                    <div class="h-1.5 rounded-full bg-slate-100">
                                        <div class="h-1.5 rounded-full bg-emerald-500" style="width: {{ $receiveWidth }}%"></div>
                                    </div>
                                    <div class="h-1.5 rounded-full bg-slate-100">
                                        <div class="h-1.5 rounded-full bg-cyan-500" style="width: {{ $deliveryWidth }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-3 py-3 text-center text-slate-500">Belum ada data pergerakan receive/delivery.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Available Capacity -->
            <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <h4 class="text-sm font-semibold text-slate-700">Available Capacity per Jenis Barang</h4>
                </div>
                <div class="max-h-[360px] overflow-auto rounded-xl border border-slate-100">
                    <table class="min-w-full divide-y divide-slate-200 text-xs">
                        <thead class="bg-slate-100 text-[11px] uppercase tracking-[0.1em] text-slate-600">
                            <tr>
                                <th class="px-3 py-2 text-left">Jenis Barang</th>
                                <th class="px-3 py-2 text-right">Stock</th>
                                <th class="px-3 py-2 text-right">Capacity</th>
                                <th class="px-3 py-2 text-right">Available</th>
                                <th class="px-3 py-2 text-right">Lot</th>
                                <th class="px-3 py-2 text-left">Utilization</th>
                            </tr>
                        </thead>
                        <tbody id="fg-type-capacity-tbody" class="divide-y divide-slate-100 bg-white text-[12px] text-slate-700">
                            @forelse ($itemTypeCapacities as $item)
                            @php
                            $hasCapacity = (bool) data_get($item, 'has_capacity', false);
                            $stockQty = (int) data_get($item, 'stock_qty', 0);
                            $capacityQty = $hasCapacity ? (int) data_get($item, 'capacity_qty', 0) : null;
                            $availableQty = $hasCapacity ? (int) data_get($item, 'available_qty', 0) : null;
                            $stockRowsPerType = (int) data_get($item, 'stock_rows', 0);
                            $usedPercent = $hasCapacity ? (float) data_get($item, 'used_percent', 0) : null;
                            $usedPercentBar = $hasCapacity ? (float) data_get($item, 'used_percent_for_bar', 0) : 0.0;
                            $overCapacity = (bool) data_get($item, 'over_capacity', false);
                            @endphp
                            <tr class="hover:bg-slate-50/80">
                                <td class="whitespace-nowrap px-3 py-2 font-semibold text-slate-800">{{ data_get($item, 'label', '-') }}</td>
                                <td class="whitespace-nowrap px-3 py-2 text-right">{{ number_format($stockQty) }}</td>
                                <td class="whitespace-nowrap px-3 py-2 text-right">{{ $capacityQty !== null ? number_format($capacityQty) : 'N/A' }}</td>
                                <td class="whitespace-nowrap px-3 py-2 text-right {{ $overCapacity ? 'text-rose-600' : 'text-emerald-700' }}">{{ $availableQty !== null ? number_format($availableQty) : 'N/A' }}</td>
                                <td class="whitespace-nowrap px-3 py-2 text-right">{{ number_format($stockRowsPerType) }}</td>
                                <td class="px-3 py-2">
                                    @if ($hasCapacity)
                                    <div class="h-2 rounded-full bg-slate-100">
                                        <div class="{{ $overCapacity ? 'bg-rose-500' : 'bg-gradient-to-r from-emerald-500 to-cyan-500' }} h-2 rounded-full" style="width: {{ number_format($usedPercentBar, 1, '.', '') }}%"></div>
                                    </div>
                                    <p class="mt-1 text-[11px] {{ $overCapacity ? 'text-rose-600' : 'text-slate-600' }}">{{ number_format((float) $usedPercent, 1) }}%</p>
                                    @else
                                    <span class="text-[11px] text-amber-600">Unmapped</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-3 py-3 text-center text-slate-500">Belum ada data stok per jenis barang.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- end Available Capacity -->

            <script>
                (function() {
                    const root = document.getElementById('fg-live-dashboard');
                    if (!root) return;

                    const endpoint = root.dataset.endpoint || '';
                    const pollingMs = Math.max(5000, Number(root.dataset.pollingMs || '15000'));
                    const formatter = new Intl.NumberFormat('id-ID');
                    const flowBody = document.getElementById('fg-flow-tbody');
                    const typeCapacityBody = document.getElementById('fg-type-capacity-tbody');
                    const trendChart = document.getElementById('fg-trend-chart');
                    const trendChartWrap = document.getElementById('fg-trend-chart-wrap');
                    const indicator = document.getElementById('fg-live-indicator');
                    const bar = document.getElementById('fg-used-bar');
                    const lastUpdate = document.getElementById('fg-last-update');
                    let latestPayload = null;
                    let chartResizeObserver = null;

                    const setText = (id, value) => {
                        const element = document.getElementById(id);
                        if (!element) return;
                        element.textContent = value;
                    };

                    const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

                    const formatDate = (value) => {
                        if (!value) return '-';
                        const date = new Date(value);
                        if (Number.isNaN(date.getTime())) {
                            return value;
                        }

                        return date.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                        });
                    };

                    const formatDateTime = (value) => {
                        if (!value) return '-';
                        const date = new Date(value);
                        if (Number.isNaN(date.getTime())) {
                            return value;
                        }

                        return date.toLocaleString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                        });
                    };

                    const setIndicatorState = (state) => {
                        if (!indicator) return;

                        indicator.className = 'inline-flex h-2.5 w-2.5 rounded-full';
                        if (state === 'loading') {
                            indicator.classList.add('animate-pulse', 'bg-amber-500');
                            return;
                        }

                        if (state === 'ok') {
                            indicator.classList.add('bg-emerald-500');
                            return;
                        }

                        indicator.classList.add('bg-rose-500');
                    };

                    const renderFlowTable = (rows) => {
                        if (!flowBody) return;
                        if (!Array.isArray(rows) || rows.length === 0) {
                            flowBody.innerHTML = '<tr><td colspan="7" class="px-3 py-3 text-center text-slate-500">Belum ada data pergerakan receive/delivery.</td></tr>';
                            return;
                        }

                        const maxQty = rows.reduce((accumulator, row) => {
                            const receiveQty = Number(row?.receiving_qty || 0);
                            const deliveryQty = Number(row?.delivery_qty || 0);
                            return Math.max(accumulator, receiveQty, deliveryQty);
                        }, 1);

                        flowBody.innerHTML = rows.map((row) => {
                            const receiveQty = Number(row?.receiving_qty || 0);
                            const deliveryQty = Number(row?.delivery_qty || 0);
                            const receiveRows = Number(row?.receiving_rows || 0);
                            const deliveryRows = Number(row?.delivery_rows || 0);
                            const netQty = Number(row?.net_qty || 0);
                            const receiveWidth = clamp((receiveQty / maxQty) * 100, 0, 100).toFixed(1);
                            const deliveryWidth = clamp((deliveryQty / maxQty) * 100, 0, 100).toFixed(1);
                            const netClass = netQty >= 0 ? 'text-emerald-700' : 'text-rose-700';

                            return `
                                <tr class="hover:bg-slate-50/80">
                                    <td class="whitespace-nowrap px-3 py-2">${formatDate(row?.date || '')}</td>
                                    <td class="whitespace-nowrap px-3 py-2 text-right font-semibold text-emerald-700">${formatter.format(receiveQty)}</td>
                                    <td class="whitespace-nowrap px-3 py-2 text-right font-semibold text-cyan-700">${formatter.format(deliveryQty)}</td>
                                    <td class="whitespace-nowrap px-3 py-2 text-right">${formatter.format(receiveRows)}</td>
                                    <td class="whitespace-nowrap px-3 py-2 text-right">${formatter.format(deliveryRows)}</td>
                                    <td class="whitespace-nowrap px-3 py-2 text-right ${netClass}">${formatter.format(netQty)}</td>
                                    <td class="px-3 py-2">
                                        <div class="space-y-1">
                                            <div class="h-1.5 rounded-full bg-slate-100">
                                                <div class="h-1.5 rounded-full bg-emerald-500" style="width:${receiveWidth}%"></div>
                                            </div>
                                            <div class="h-1.5 rounded-full bg-slate-100">
                                                <div class="h-1.5 rounded-full bg-cyan-500" style="width:${deliveryWidth}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }).join('');
                    };

                    const renderTypeCapacityTable = (rows) => {
                        if (!typeCapacityBody) return;
                        if (!Array.isArray(rows) || rows.length === 0) {
                            typeCapacityBody.innerHTML = '<tr><td colspan="6" class="px-3 py-3 text-center text-slate-500">Belum ada data stok per jenis barang.</td></tr>';
                            return;
                        }

                        typeCapacityBody.innerHTML = rows.map((row) => {
                            const hasCapacity = Boolean(row?.has_capacity);
                            const stockQty = Number(row?.stock_qty || 0);
                            const capacityQty = hasCapacity ? Number(row?.capacity_qty || 0) : null;
                            const availableQty = hasCapacity ? Number(row?.available_qty || 0) : null;
                            const stockRowsPerType = Number(row?.stock_rows || 0);
                            const usedPercent = hasCapacity ? Number(row?.used_percent || 0) : null;
                            const usedPercentBar = hasCapacity ? clamp(Number(row?.used_percent_for_bar || 0), 0, 100).toFixed(1) : '0.0';
                            const overCapacity = Boolean(row?.over_capacity);
                            const usageClass = overCapacity ? 'text-rose-600' : 'text-slate-600';
                            const barClass = overCapacity ? 'bg-rose-500' : 'bg-gradient-to-r from-emerald-500 to-cyan-500';

                            return `
                                <tr class="hover:bg-slate-50/80">
                                    <td class="whitespace-nowrap px-3 py-2 font-semibold text-slate-800">${row?.label || '-'}</td>
                                    <td class="whitespace-nowrap px-3 py-2 text-right">${formatter.format(stockQty)}</td>
                                    <td class="whitespace-nowrap px-3 py-2 text-right">${capacityQty === null ? 'N/A' : formatter.format(capacityQty)}</td>
                                    <td class="whitespace-nowrap px-3 py-2 text-right ${overCapacity ? 'text-rose-600' : 'text-emerald-700'}">${availableQty === null ? 'N/A' : formatter.format(availableQty)}</td>
                                    <td class="whitespace-nowrap px-3 py-2 text-right">${formatter.format(stockRowsPerType)}</td>
                                    <td class="px-3 py-2">
                                        ${hasCapacity ? `
                                            <div class="h-2 rounded-full bg-slate-100">
                                                <div class="${barClass} h-2 rounded-full" style="width:${usedPercentBar}%"></div>
                                            </div>
                                            <p class="mt-1 text-[11px] ${usageClass}">${usedPercent.toFixed(1)}%</p>
                                        ` : '<span class="text-[11px] text-amber-600">Unmapped</span>'}
                                    </td>
                                </tr>
                            `;
                        }).join('');
                    };

                    const createSvgNode = (tag, attributes = {}, content = '') => {
                        const node = document.createElementNS('http://www.w3.org/2000/svg', tag);
                        Object.entries(attributes).forEach(([key, value]) => {
                            node.setAttribute(key, String(value));
                        });
                        if (content !== '') {
                            node.textContent = content;
                        }
                        return node;
                    };

                    const renderTrendChart = (payload) => {
                        if (!trendChart) return;

                        const rows = Array.isArray(payload?.daily_flow) ? payload.daily_flow : [];
                        trendChart.innerHTML = '';

                        if (rows.length === 0) {
                            setText('fg-trend-last-date', '-');
                            setText('fg-trend-last-receive', '0 box');
                            setText('fg-trend-last-delivery', '0 box');
                            setText('fg-trend-last-stock', '0 box');
                            return;
                        }

                        const width = Math.max(trendChart.clientWidth, 320);
                        const height = Math.max(trendChart.clientHeight, 220);
                        trendChart.setAttribute('viewBox', `0 0 ${width} ${height}`);
                        trendChart.setAttribute('preserveAspectRatio', 'none');

                        const padding = {
                            top: 18,
                            right: 16,
                            bottom: 30,
                            left: 46,
                        };
                        const chartWidth = width - padding.left - padding.right;
                        const chartHeight = height - padding.top - padding.bottom;
                        if (chartWidth <= 0 || chartHeight <= 0) return;

                        const receiveSeries = rows.map((row) => Number(row?.receiving_qty || 0));
                        const deliverySeries = rows.map((row) => Number(row?.delivery_qty || 0));
                        const stockSeries = rows.map((row) => Number(row?.stock_qty || 0));

                        const allValues = [...receiveSeries, ...deliverySeries, ...stockSeries];
                        let yMin = Math.min(0, ...allValues);
                        let yMax = Math.max(10, ...allValues);
                        if (yMin === yMax) {
                            yMax += 10;
                        }
                        const extraTopPadding = (yMax - yMin) * 0.12;
                        yMax += extraTopPadding;
                        const yRange = yMax - yMin || 1;

                        const xFor = (index) => {
                            if (rows.length === 1) {
                                return padding.left + chartWidth / 2;
                            }
                            return padding.left + (index / (rows.length - 1)) * chartWidth;
                        };
                        const yFor = (value) => padding.top + ((yMax - value) / yRange) * chartHeight;

                        for (let tick = 0; tick <= 5; tick += 1) {
                            const y = padding.top + (tick / 5) * chartHeight;
                            const value = yMax - (tick / 5) * yRange;
                            trendChart.appendChild(createSvgNode('line', {
                                x1: padding.left,
                                y1: y,
                                x2: width - padding.right,
                                y2: y,
                                stroke: '#334155',
                                'stroke-width': 1,
                                'stroke-dasharray': '3 4',
                                opacity: 0.6,
                            }));
                            trendChart.appendChild(createSvgNode('text', {
                                x: padding.left - 8,
                                y: y + 3,
                                'text-anchor': 'end',
                                'font-size': 10,
                                fill: '#94a3b8',
                            }, formatter.format(Math.round(value))));
                        }

                        const xAxisY = yFor(0);
                        trendChart.appendChild(createSvgNode('line', {
                            x1: padding.left,
                            y1: xAxisY,
                            x2: width - padding.right,
                            y2: xAxisY,
                            stroke: '#64748b',
                            'stroke-width': 1,
                            opacity: 0.8,
                        }));

                        rows.forEach((row, index) => {
                            const labelStep = rows.length <= 8 ? 1 : Math.ceil(rows.length / 8);
                            const shouldDrawLabel = index === 0 || index === rows.length - 1 || index % labelStep === 0;
                            if (!shouldDrawLabel) return;

                            trendChart.appendChild(createSvgNode('text', {
                                x: xFor(index),
                                y: height - 8,
                                'text-anchor': 'middle',
                                'font-size': 10,
                                fill: '#94a3b8',
                            }, formatDate(row?.date || '')));
                        });

                        const drawSeries = (series, color, strokeWidth, pointRadius, fillOpacity = 0) => {
                            const points = series.map((value, index) => `${xFor(index)},${yFor(value)}`).join(' ');
                            if (fillOpacity > 0 && points !== '') {
                                const areaPoints = `${padding.left},${yFor(0)} ${points} ${xFor(series.length - 1)},${yFor(0)}`;
                                trendChart.appendChild(createSvgNode('polygon', {
                                    points: areaPoints,
                                    fill: color,
                                    opacity: fillOpacity,
                                }));
                            }

                            trendChart.appendChild(createSvgNode('polyline', {
                                points,
                                fill: 'none',
                                stroke: color,
                                'stroke-width': strokeWidth,
                                'stroke-linecap': 'round',
                                'stroke-linejoin': 'round',
                            }));

                            series.forEach((value, index) => {
                                trendChart.appendChild(createSvgNode('circle', {
                                    cx: xFor(index),
                                    cy: yFor(value),
                                    r: pointRadius,
                                    fill: color,
                                }));
                            });
                        };

                        drawSeries(stockSeries, '#facc15', 2.4, 2.1, 0.08);
                        drawSeries(receiveSeries, '#34d399', 2.3, 2);
                        drawSeries(deliverySeries, '#38bdf8', 2.3, 2);

                        const lastIndex = rows.length - 1;
                        setText('fg-trend-last-date', formatDate(rows[lastIndex]?.date || '-'));
                        setText('fg-trend-last-receive', `${formatter.format(receiveSeries[lastIndex] || 0)} box`);
                        setText('fg-trend-last-delivery', `${formatter.format(deliverySeries[lastIndex] || 0)} box`);
                        setText('fg-trend-last-stock', `${formatter.format(stockSeries[lastIndex] || 0)} box`);
                    };

                    const render = (payload) => {
                        latestPayload = payload;
                        const receiving = payload?.today?.receiving ?? {};
                        const delivery = payload?.today?.delivery ?? {};
                        const stock = payload?.stock ?? {};

                        const receivingQty = Number(receiving?.qty || 0);
                        const receivingRows = Number(receiving?.rows || 0);
                        const deliveryQty = Number(delivery?.qty || 0);
                        const deliveryRows = Number(delivery?.rows || 0);

                        const stockQty = Number(stock?.qty || 0);
                        const stockRows = Number(stock?.rows || 0);
                        const availableQty = Number(stock?.available_qty || 0);
                        const capacityQty = Number(stock?.capacity_qty || 0);
                        const usedPercent = Number(stock?.used_percent || 0);
                        const usedPercentForBar = Number(stock?.used_percent_for_bar || clamp(usedPercent, 0, 100));
                        const overCapacity = Boolean(stock?.over_capacity);

                        setText('fg-receiving-qty', `${formatter.format(receivingQty)} box`);
                        setText('fg-receiving-rows', `${formatter.format(receivingRows)} transaksi`);
                        setText('fg-delivery-qty', `${formatter.format(deliveryQty)} box`);
                        setText('fg-delivery-rows', `${formatter.format(deliveryRows)} transaksi`);
                        setText('fg-stock-qty', `${formatter.format(stockQty)} box`);
                        setText('fg-stock-rows', `${formatter.format(stockRows)} lot aktif`);
                        setText('fg-available-qty', `${formatter.format(availableQty)} box`);
                        setText('fg-capacity-label', `dari total ${formatter.format(capacityQty)} box`);
                        setText('fg-used-percent', `${usedPercent.toFixed(1)}%`);
                        setText('fg-used-qty', `${formatter.format(stockQty)} box`);
                        setText('fg-remaining-qty', `${formatter.format(availableQty)} box`);
                        setText('fg-capacity-status', overCapacity ? 'OVER CAPACITY' : 'Within Capacity');

                        const statusLabel = document.getElementById('fg-capacity-status');
                        if (statusLabel) {
                            statusLabel.className = overCapacity ? 'text-rose-600' : 'text-emerald-700';
                        }

                        if (bar) {
                            bar.style.width = `${clamp(usedPercentForBar, 0, 100)}%`;
                            bar.className = overCapacity ?
                                'h-3 rounded-full bg-rose-500 transition-all duration-500' :
                                'h-3 rounded-full bg-gradient-to-r from-emerald-500 via-cyan-500 to-indigo-500 transition-all duration-500';
                        }

                        if (lastUpdate) {
                            lastUpdate.textContent = formatDateTime(payload?.as_of);
                        }

                        renderTrendChart(payload);
                        renderTypeCapacityTable(payload?.item_type_capacities ?? []);
                        renderFlowTable(payload?.daily_flow ?? []);
                    };

                    const refresh = async () => {
                        if (!endpoint) return;

                        setIndicatorState('loading');
                        try {
                            const response = await fetch(endpoint, {
                                headers: {
                                    Accept: 'application/json',
                                },
                                cache: 'no-store',
                            });

                            if (!response.ok) {
                                throw new Error(`Request failed with status ${response.status}`);
                            }

                            const payload = await response.json();
                            render(payload);
                            setIndicatorState('ok');
                        } catch (error) {
                            setIndicatorState('error');
                        }
                    };

                    render(@json($fgMetrics));
                    refresh();

                    const rerenderChart = () => {
                        if (latestPayload !== null) {
                            renderTrendChart(latestPayload);
                        }
                    };

                    if (typeof ResizeObserver !== 'undefined' && trendChartWrap) {
                        chartResizeObserver = new ResizeObserver(() => rerenderChart());
                        chartResizeObserver.observe(trendChartWrap);
                    } else {
                        window.addEventListener('resize', rerenderChart);
                    }

                    const timer = setInterval(refresh, pollingMs);
                    window.addEventListener('beforeunload', () => {
                        clearInterval(timer);
                        if (chartResizeObserver) {
                            chartResizeObserver.disconnect();
                        }
                    });
                })();
            </script>
        </section>
    </div>
</div>
@endsection
