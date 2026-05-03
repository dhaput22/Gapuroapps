@extends('layouts.app')

@section('title', 'Dashboard - Gapuro System')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="mb-2 text-2xl font-bold text-gray-700">WELCOME TO GAPURO SITE.</h2>
        <p class="text-sm text-gray-500">Current Period : 2025-10-01 - 2025-12-31</p>
    </div>

    <div class="flex flex-col gap-6">
        <section class="order-2 rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4 shadow-sm">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h3 class="text-2xl font-black tracking-wide text-slate-900">FG STORAGE AREA MAP</h3>
                    <p class="text-xs text-slate-500">Live map terintegrasi stok aktual, kapasitas, dan sisa ruang per jenis barang.</p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] text-slate-600">
                    <span class="inline-flex h-2.5 w-2.5 animate-pulse rounded-full bg-cyan-500"></span>
                    <span>Realtime View</span>
                </div>
            </div>

            <style>
                .fg-map-wrap {
                    display: grid;
                    gap: 0.85rem;
                }

                @media (min-width: 1024px) {
                    .fg-map-wrap {
                        grid-template-columns: minmax(0, 1fr) 320px;
                    }
                }

                .fg-map-board {
                    border-radius: 1rem;
                    border: 1px solid #dbe3f2;
                    background: radial-gradient(circle at top left, #f8fbff 0%, #edf4ff 42%, #e6eef9 100%);
                    padding: 0.8rem;
                    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.7);
                }

                .fg-map {
                    position: relative;
                    width: 100%;
                    aspect-ratio: 16 / 7;
                    overflow: hidden;
                    border-radius: 0.8rem;
                    border: 1px solid #cad8ed;
                    background:
                        linear-gradient(to right, rgba(15, 23, 42, 0.03) 1px, transparent 1px) 0 0 / 28px 28px,
                        linear-gradient(to bottom, rgba(15, 23, 42, 0.03) 1px, transparent 1px) 0 0 / 28px 28px,
                        linear-gradient(160deg, #f7fbff 0%, #ebf3ff 55%, #e4eefb 100%);
                }

                .fg-map .zone {
                    position: absolute;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    align-items: flex-start;
                    gap: 0.16rem;
                    padding: 0.2rem 0.26rem;
                    border-radius: 0.5rem;
                    border: 1px solid rgba(15, 23, 42, 0.28);
                    text-align: left;
                    font-weight: 800;
                    line-height: 1.08;
                    letter-spacing: 0.01em;
                    overflow: hidden;
                    transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease, filter 180ms ease;
                    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.12);
                    cursor: pointer;
                }

                .fg-map .zone:hover,
                .fg-map .zone:focus-visible,
                .fg-map .zone.active {
                    transform: translateY(-1px) scale(1.01);
                    border-color: rgba(15, 23, 42, 0.55);
                    box-shadow: 0 8px 16px rgba(15, 23, 42, 0.16);
                    z-index: 8;
                    filter: saturate(1.05);
                    outline: none;
                }

                .fg-map .zone-title {
                    max-width: 100%;
                    font-size: clamp(8px, 0.72vw, 11px);
                    color: rgba(248, 250, 252, 0.95);
                    text-shadow: 0 1px 0 rgba(15, 23, 42, 0.35);
                    white-space: normal;
                    word-break: break-word;
                }

                .fg-map .zone.k-dark .zone-title {
                    color: rgba(15, 23, 42, 0.92);
                    text-shadow: none;
                }

                .fg-map .zone-metric {
                    display: inline-flex;
                    max-width: 100%;
                    align-items: center;
                    gap: 0.18rem;
                    border-radius: 999px;
                    background: rgba(15, 23, 42, 0.68);
                    padding: 0.12rem 0.32rem;
                    font-size: clamp(7px, 0.64vw, 10px);
                    font-weight: 700;
                    color: rgba(248, 250, 252, 0.95);
                    white-space: nowrap;
                }

                .fg-map .zone.k-dark .zone-metric {
                    background: rgba(248, 250, 252, 0.78);
                    color: rgba(15, 23, 42, 0.92);
                }

                .fg-map .zone.compact {
                    justify-content: center;
                }

                .fg-map .zone.compact .zone-metric {
                    display: none;
                }

                .fg-map .zone.vertical {
                    writing-mode: vertical-rl;
                    text-orientation: mixed;
                    align-items: center;
                }

                .fg-map .zone.vertical .zone-title {
                    white-space: nowrap;
                    line-height: 1;
                }

                .fg-map .zone.vertical .zone-metric {
                    writing-mode: horizontal-tb;
                    text-orientation: initial;
                    transform: rotate(90deg);
                    transform-origin: center;
                    margin-top: 0.12rem;
                    font-size: clamp(6px, 0.52vw, 8px);
                    padding: 0.08rem 0.2rem;
                }

                .fg-map-info-panel {
                    border-radius: 1rem;
                    border: 1px solid #dbe3f2;
                    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
                    padding: 0.85rem;
                }

                .fg-map-info-value {
                    font-size: 1.5rem;
                    font-weight: 900;
                    line-height: 1;
                }
            </style>

            <div class="fg-map-wrap">
                <div class="fg-map-board">
                    <div id="fg-storage-map" class="fg-map text-white">
                        <button type="button" class="zone k-dark compact" data-zone-key="bottle_20ml" data-zone-label="Bottle 20ml" style="left:1.8%;top:8%;width:6.4%;height:10%;background:#585858;">
                            <span class="zone-title">Bottle 20ml</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone compact" data-zone-key="bottle_40ml" data-zone-label="Bottle 40ml" style="left:8.4%;top:8%;width:6.4%;height:10%;background:#505050;">
                            <span class="zone-title">Bottle 40ml</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone k-dark" data-zone-key="prism_inj" data-zone-label="PRISM INJ" style="left:16.5%;top:8%;width:31.5%;height:10%;background:#7ad255;">
                            <span class="zone-title">PRISM INJ</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone k-dark" data-zone-key="part_return" data-zone-label="PART RETURN" style="left:60%;top:8%;width:11.3%;height:10%;background:#ffd321;">
                            <span class="zone-title">PART RETURN</span>
                            <span class="zone-metric">S:-</span>
                        </button>

                        <button type="button" class="zone compact" data-zone-key="bottle_70ml" data-zone-label="Bottle 70ml" style="left:1.8%;top:22%;width:13%;height:8%;background:#9f9f9f;">
                            <span class="zone-title">Bottle 70ml</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone compact" data-zone-key="bottle_70ml" data-zone-label="Bottle 70ml" style="left:1.8%;top:30.5%;width:13%;height:8%;background:#a3a3a3;">
                            <span class="zone-title">Bottle 70ml</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone compact" data-zone-key="bottle_70ml" data-zone-label="Bottle 70ml" style="left:1.8%;top:43.5%;width:13%;height:8%;background:#a3a3a3;">
                            <span class="zone-title">Bottle 70ml</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone compact" data-zone-key="bottle_70ml" data-zone-label="Bottle 70ml" style="left:1.8%;top:52%;width:13%;height:8%;background:#a3a3a3;">
                            <span class="zone-title">Bottle 70ml</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone compact" data-zone-key="bottle_70ml" data-zone-label="Bottle 70ml" style="left:1.8%;top:65%;width:13%;height:8%;background:#a3a3a3;">
                            <span class="zone-title">Bottle 70ml</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone compact" data-zone-key="bottle_70ml" data-zone-label="Bottle 70ml" style="left:1.8%;top:73.5%;width:13%;height:8%;background:#a3a3a3;">
                            <span class="zone-title">Bottle 70ml</span>
                            <span class="zone-metric">S:-</span>
                        </button>

                        <button type="button" class="zone vertical" data-zone-key="bottle_70ml" data-zone-label="Bottle 70ml" style="left:16.5%;top:21%;width:3.5%;height:58%;background:#a7a7a7;">
                            <span class="zone-title">Bottle 70ml</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="sakura" data-zone-label="SAKURA" style="left:20.2%;top:21%;width:3.3%;height:58%;background:#000;">
                            <span class="zone-title">SAKURA</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone" data-zone-key="n3_cover" data-zone-label="N3 COVER" style="left:23.8%;top:21%;width:5.3%;height:19%;background:#f3ba58;">
                            <span class="zone-title">N3 COVER</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone" data-zone-key="n3_cover_m" data-zone-label="N3 COVER M" style="left:23.8%;top:42%;width:5.3%;height:19%;background:#f3ba58;">
                            <span class="zone-title">N3 COVER M</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone" data-zone-key="nasuno_3_case" data-zone-label="NASUNO 3 CASE" style="left:29.2%;top:21%;width:8.6%;height:58%;background:#e95bb9;">
                            <span class="zone-title">NASUNO 3 CASE</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone" data-zone-key="n3_case_m1_m2" data-zone-label="N3 CASE M1, M2" style="left:37.9%;top:21%;width:5.7%;height:58%;background:#e95bb9;">
                            <span class="zone-title">N3 CASE M1, M2</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="craig_case_l" data-zone-label="CRAIG CASE L" style="left:43.7%;top:21%;width:3.1%;height:58%;background:#8f8f8f;">
                            <span class="zone-title">CRAIG CASE L</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="craig_case_s_345" data-zone-label="CRAIG CASE S 3,4,5" style="left:46.9%;top:21%;width:6.2%;height:58%;background:#9f9f9f;">
                            <span class="zone-title">CRAIG CASE S 3,4,5</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone vertical compact" data-zone-key="ha3p_case_s2_m" data-zone-label="HA3P CASE S2 / M" style="left:53.3%;top:21%;width:2.5%;height:58%;background:#0abb63;">
                            <span class="zone-title">HA3P CASE S2 / M</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone vertical compact" data-zone-key="ha3p_case_s2" data-zone-label="HA3P CASE S2" style="left:56%;top:21%;width:2.5%;height:58%;background:#59cad2;">
                            <span class="zone-title">HA3P CASE S2</span>
                            <span class="zone-metric">S:-</span>
                        </button>

                        <button type="button" class="zone vertical" data-zone-key="ink_bottle_cap" data-zone-label="INK BOTTLE CAP" style="left:60%;top:21%;width:5%;height:29%;background:#1159b5;">
                            <span class="zone-title">INK BOTTLE CAP</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="top_cap" data-zone-label="TOP CAP" style="left:60%;top:50.1%;width:5%;height:28.9%;background:#1152a5;">
                            <span class="zone-title">TOP CAP</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="hamana_glee" data-zone-label="HAMANA GLEE" style="left:65.2%;top:21%;width:6.2%;height:58%;background:#5a5a5a;">
                            <span class="zone-title">HAMANA GLEE</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="hamana_grow" data-zone-label="HAMANA GROW" style="left:71.6%;top:21%;width:6.2%;height:58%;background:#5a5a5a;">
                            <span class="zone-title">HAMANA GROW</span>
                            <span class="zone-metric">S:-</span>
                        </button>

                        <button type="button" class="zone vertical" data-zone-key="spout" data-zone-label="SPOUT" style="left:79%;top:21%;width:6.2%;height:43%;background:#2404b8;">
                            <span class="zone-title">SPOUT</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="mashu" data-zone-label="MASHU" style="left:79%;top:64.5%;width:6.2%;height:14.5%;background:#ff7a19;">
                            <span class="zone-title">MASHU</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="spout" data-zone-label="SPOUT" style="left:86.4%;top:21%;width:5.4%;height:58%;background:#2204b8;">
                            <span class="zone-title">SPOUT</span>
                            <span class="zone-metric">S:-</span>
                        </button>

                        <button type="button" class="zone vertical compact" data-zone-key="spout" data-zone-label="SPOUT" style="left:93%;top:21%;width:5.5%;height:14%;background:#2204b8;">
                            <span class="zone-title">SPOUT</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="s15" data-zone-label="S15" style="left:93%;top:35.2%;width:5.5%;height:22%;background:#79d34f;">
                            <span class="zone-title">S15</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="a3" data-zone-label="A3" style="left:93%;top:57.5%;width:5.5%;height:18%;background:#e30000;">
                            <span class="zone-title">A3</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone vertical compact" data-zone-key="fb" data-zone-label="FB" style="left:93%;top:75.8%;width:5.5%;height:9%;background:#1d1bb3;">
                            <span class="zone-title">FB</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone vertical compact" data-zone-key="adf" data-zone-label="ADF" style="left:93%;top:85%;width:5.5%;height:9%;background:#303138;">
                            <span class="zone-title">ADF</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone vertical compact" data-zone-key="lg" data-zone-label="LG" style="left:93%;top:94.2%;width:5.5%;height:5.8%;background:#d50000;">
                            <span class="zone-title">LG</span>
                            <span class="zone-metric">S:-</span>
                        </button>

                        <button type="button" class="zone k-dark compact" data-zone-label="TRAY CLEANING" style="left:1.8%;top:81.8%;width:9.5%;height:11%;background:#cdcdcd;">
                            <span class="zone-title">TRAY CLEANING</span>
                            <span class="zone-metric">Utility</span>
                        </button>
                        <button type="button" class="zone k-dark compact" data-zone-label="EMPTY BOX" style="left:11.5%;top:81.8%;width:15.2%;height:11%;background:#bdbdbd;">
                            <span class="zone-title">EMPTY BOX</span>
                            <span class="zone-metric">Utility</span>
                        </button>
                        <button type="button" class="zone k-dark" data-zone-key="craig_cover_l" data-zone-label="CRAIG COVER L" style="left:43.5%;top:81.8%;width:15%;height:11%;background:#a9a9a9;">
                            <span class="zone-title">CRAIG COVER L</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone k-dark" data-zone-key="nasuno_case_b" data-zone-label="NASUNO CASE B" style="left:60%;top:81.8%;width:18.8%;height:11%;background:#a8a8a8;">
                            <span class="zone-title">NASUNO CASE B</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone k-dark" data-zone-key="cover_s_m" data-zone-label="COVER S, M" style="left:78.9%;top:81.8%;width:6.2%;height:11%;background:#a8a8a8;">
                            <span class="zone-title">COVER S, M</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                        <button type="button" class="zone k-dark compact" data-zone-key="prism_coating" data-zone-label="PRISM COATING" style="left:60%;top:90.3%;width:25.1%;height:6.2%;background:#8c5800;">
                            <span class="zone-title">PRISM COATING</span>
                            <span class="zone-metric">S:-</span>
                        </button>
                    </div>
                </div>

                <aside class="fg-map-info-panel">
                    <div class="mb-3 rounded-xl border border-slate-200 bg-white px-3 py-3">
                        <p class="text-[11px] uppercase tracking-[0.1em] text-slate-500">Selected Zone</p>
                        <p id="fg-map-selected-label" class="mt-1 text-base font-black text-slate-900">-</p>
                        <p id="fg-map-selected-subtitle" class="mt-1 text-xs text-slate-500">Klik area map untuk melihat detail kapasitas.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <article class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-slate-500">Stock</p>
                            <p id="fg-map-selected-stock" class="fg-map-info-value text-slate-900">0</p>
                            <p class="text-[10px] text-slate-500">box</p>
                        </article>
                        <article class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-slate-500">Reminder</p>
                            <p id="fg-map-selected-available" class="fg-map-info-value text-emerald-700">0</p>
                            <p class="text-[10px] text-slate-500">box</p>
                        </article>
                        <article class="col-span-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-slate-500">Utilization</p>
                            <div class="mt-1 h-2.5 rounded-full bg-slate-200">
                                <div id="fg-map-selected-bar" class="h-2.5 w-0 rounded-full bg-gradient-to-r from-emerald-500 to-cyan-500 transition-all duration-300"></div>
                            </div>
                            <p id="fg-map-selected-used" class="mt-1 text-[11px] font-semibold text-slate-700">0%</p>
                        </article>
                    </div>

                    <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-lg border border-slate-200 bg-white px-2 py-2">
                            <p class="text-[10px] uppercase tracking-[0.08em] text-slate-500">Zone</p>
                            <p id="fg-map-zone-total" class="mt-1 text-lg font-black text-slate-900">0</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white px-2 py-2">
                            <p class="text-[10px] uppercase tracking-[0.08em] text-slate-500">Over</p>
                            <p id="fg-map-zone-over" class="mt-1 text-lg font-black text-rose-600">0</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-white px-2 py-2">
                            <p class="text-[10px] uppercase tracking-[0.08em] text-slate-500">Avg Use</p>
                            <p id="fg-map-zone-avg" class="mt-1 text-lg font-black text-cyan-700">0%</p>
                        </div>
                    </div>
                </aside>
            </div>

            <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px]">
                <span class="rounded-full border border-emerald-200 bg-emerald-50 px-2 py-1 text-emerald-700">Low Utilization</span>
                <span class="rounded-full border border-amber-200 bg-amber-50 px-2 py-1 text-amber-700">Medium Utilization</span>
                <span class="rounded-full border border-rose-200 bg-rose-50 px-2 py-1 text-rose-700">High / Over Capacity</span>
                <span class="rounded-full border border-slate-200 bg-slate-50 px-2 py-1 text-slate-600">Klik zona untuk detail</span>
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
                    const mapRoot = document.getElementById('fg-storage-map');
                    const mapZones = mapRoot ? Array.from(mapRoot.querySelectorAll('.zone')) : [];
                    const selectedAvailable = document.getElementById('fg-map-selected-available');
                    const selectedUsed = document.getElementById('fg-map-selected-used');
                    const selectedBar = document.getElementById('fg-map-selected-bar');
                    const zoneTotal = document.getElementById('fg-map-zone-total');
                    const zoneOver = document.getElementById('fg-map-zone-over');
                    const zoneAvg = document.getElementById('fg-map-zone-avg');
                    const indicator = document.getElementById('fg-live-indicator');
                    const bar = document.getElementById('fg-used-bar');
                    const lastUpdate = document.getElementById('fg-last-update');
                    let latestPayload = null;
                    let chartResizeObserver = null;
                    let selectedZoneKey = null;

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

                    const safeNumber = (value) => {
                        const parsed = Number(value);
                        return Number.isFinite(parsed) ? parsed : 0;
                    };

                    const resolveUsageTone = (usedPercent, overCapacity) => {
                        if (overCapacity || usedPercent >= 100) {
                            return {
                                badgeBg: '#e11d48',
                                badgeColor: '#f8fafc',
                                borderColor: 'rgba(225, 29, 72, 0.8)',
                                shadowColor: 'rgba(225, 29, 72, 0.35)',
                            };
                        }

                        if (usedPercent >= 80) {
                            return {
                                badgeBg: '#f59e0b',
                                badgeColor: '#111827',
                                borderColor: 'rgba(245, 158, 11, 0.85)',
                                shadowColor: 'rgba(245, 158, 11, 0.3)',
                            };
                        }

                        return {
                            badgeBg: '#10b981',
                            badgeColor: '#f8fafc',
                            borderColor: 'rgba(16, 185, 129, 0.8)',
                            shadowColor: 'rgba(16, 185, 129, 0.28)',
                        };
                    };

                    const setZoneSelection = (zone, zoneStat) => {
                        mapZones.forEach((mapZone) => mapZone.classList.remove('active'));
                        if (zone) {
                            zone.classList.add('active');
                        }

                        if (!zone || !zoneStat) {
                            setText('fg-map-selected-label', zone?.dataset.zoneLabel || 'Utility Zone');
                            setText('fg-map-selected-subtitle', 'Zona ini tidak memiliki konfigurasi kapasitas stok.');
                            setText('fg-map-selected-stock', '0');
                            setText('fg-map-selected-available', 'N/A');
                            setText('fg-map-selected-used', 'N/A');
                            if (selectedAvailable) {
                                selectedAvailable.className = 'fg-map-info-value text-slate-700';
                            }
                            if (selectedUsed) {
                                selectedUsed.className = 'mt-1 text-[11px] font-semibold text-slate-700';
                            }
                            if (selectedBar) {
                                selectedBar.style.width = '0%';
                                selectedBar.className = 'h-2.5 w-0 rounded-full bg-slate-300 transition-all duration-300';
                            }
                            return;
                        }

                        const stockQty = safeNumber(zoneStat.stock_qty);
                        const hasCapacity = Boolean(zoneStat.has_capacity);
                        const availableQty = hasCapacity ? safeNumber(zoneStat.available_qty) : null;
                        const capacityQty = hasCapacity ? safeNumber(zoneStat.capacity_qty) : null;
                        const usedPercent = hasCapacity ? safeNumber(zoneStat.used_percent) : 0;
                        const usedPercentForBar = hasCapacity ? clamp(safeNumber(zoneStat.used_percent_for_bar), 0, 100) : 0;
                        const overCapacity = Boolean(zoneStat.over_capacity);
                        const tone = resolveUsageTone(usedPercent, overCapacity);

                        setText('fg-map-selected-label', zoneStat.label || zone.dataset.zoneLabel || '-');
                        if (hasCapacity) {
                            setText('fg-map-selected-subtitle', `Capacity ${formatter.format(capacityQty)} box | Lot ${formatter.format(safeNumber(zoneStat.stock_rows))}`);
                            setText('fg-map-selected-stock', formatter.format(stockQty));
                            setText('fg-map-selected-available', formatter.format(availableQty));
                            setText('fg-map-selected-used', `${usedPercent.toFixed(1)}%`);
                            if (selectedBar) {
                                selectedBar.style.width = `${usedPercentForBar}%`;
                                selectedBar.className = overCapacity ?
                                    'h-2.5 rounded-full bg-rose-500 transition-all duration-300' :
                                    'h-2.5 rounded-full bg-gradient-to-r from-emerald-500 to-cyan-500 transition-all duration-300';
                            }
                        } else {
                            setText('fg-map-selected-subtitle', 'Belum ada kapasitas terdaftar untuk jenis barang ini.');
                            setText('fg-map-selected-stock', formatter.format(stockQty));
                            setText('fg-map-selected-available', 'N/A');
                            setText('fg-map-selected-used', 'N/A');
                            if (selectedBar) {
                                selectedBar.style.width = '0%';
                                selectedBar.className = 'h-2.5 w-0 rounded-full bg-slate-300 transition-all duration-300';
                            }
                        }

                        if (selectedAvailable) {
                            selectedAvailable.className = hasCapacity && overCapacity ?
                                'fg-map-info-value text-rose-600' :
                                'fg-map-info-value text-emerald-700';
                        }
                        if (selectedUsed) {
                            selectedUsed.className = hasCapacity && overCapacity ?
                                'mt-1 text-[11px] font-semibold text-rose-600' :
                                'mt-1 text-[11px] font-semibold text-slate-700';
                        }
                    };

                    const renderAreaMap = (rows) => {
                        if (!mapRoot || mapZones.length === 0) {
                            return;
                        }

                        const statsByKey = new Map();
                        if (Array.isArray(rows)) {
                            rows.forEach((row) => {
                                const key = String(row?.key || '').trim();
                                if (key !== '') {
                                    statsByKey.set(key, row);
                                }
                            });
                        }

                        let firstSelectableZone = null;

                        mapZones.forEach((zone) => {
                            const key = String(zone.dataset.zoneKey || '').trim();
                            const metricEl = zone.querySelector('.zone-metric');
                            const stat = key === '' ? null : statsByKey.get(key) || null;

                            if (!firstSelectableZone && key !== '') {
                                firstSelectableZone = zone;
                            }

                            zone.style.borderColor = 'rgba(15, 23, 42, 0.28)';
                            zone.style.boxShadow = '0 2px 6px rgba(15, 23, 42, 0.12)';

                            if (!stat) {
                                if (metricEl && key !== '') {
                                    metricEl.textContent = 'S:0';
                                    metricEl.style.backgroundColor = '';
                                    metricEl.style.color = '';
                                }
                                return;
                            }

                            const stockQty = safeNumber(stat.stock_qty);
                            const hasCapacity = Boolean(stat.has_capacity);
                            const availableQty = hasCapacity ? safeNumber(stat.available_qty) : null;
                            const usedPercent = hasCapacity ? safeNumber(stat.used_percent) : null;
                            const overCapacity = Boolean(stat.over_capacity);
                            const tone = resolveUsageTone(usedPercent ?? 0, overCapacity);

                            if (metricEl) {
                                if (hasCapacity) {
                                    metricEl.textContent = `S:${formatter.format(stockQty)} | R:${formatter.format(availableQty)}`;
                                } else {
                                    metricEl.textContent = `S:${formatter.format(stockQty)}`;
                                }
                                metricEl.style.backgroundColor = tone.badgeBg;
                                metricEl.style.color = tone.badgeColor;
                            }

                            zone.style.borderColor = tone.borderColor;
                            zone.style.boxShadow = `0 0 0 1px ${tone.borderColor}, 0 8px 14px ${tone.shadowColor}`;
                        });

                        const capacityRows = Array.isArray(rows)
                            ? rows.filter((row) => Boolean(row?.has_capacity))
                            : [];
                        const overCount = capacityRows.filter((row) => Boolean(row?.over_capacity)).length;
                        const averageUsage = capacityRows.length === 0 ?
                            0 :
                            capacityRows.reduce((acc, row) => acc + safeNumber(row?.used_percent), 0) / capacityRows.length;

                        if (zoneTotal) {
                            zoneTotal.textContent = formatter.format(capacityRows.length);
                        }
                        if (zoneOver) {
                            zoneOver.textContent = formatter.format(overCount);
                        }
                        if (zoneAvg) {
                            zoneAvg.textContent = `${averageUsage.toFixed(1)}%`;
                        }

                        let selectedZone = null;
                        if (selectedZoneKey !== null && selectedZoneKey !== '') {
                            selectedZone = mapZones.find((zone) => String(zone.dataset.zoneKey || '').trim() === selectedZoneKey) || null;
                        }
                        if (!selectedZone) {
                            selectedZone = firstSelectableZone;
                        }

                        const selectedStat = selectedZone ? statsByKey.get(String(selectedZone.dataset.zoneKey || '').trim()) || null : null;
                        setZoneSelection(selectedZone, selectedStat);
                    };

                    const findZoneStatFromPayload = (payload, zoneKey) => {
                        if (!payload || !Array.isArray(payload.item_type_capacities) || zoneKey === '') {
                            return null;
                        }

                        return payload.item_type_capacities.find((row) => String(row?.key || '').trim() === zoneKey) || null;
                    };

                    mapZones.forEach((zone) => {
                        zone.addEventListener('click', () => {
                            const zoneKey = String(zone.dataset.zoneKey || '').trim();
                            selectedZoneKey = zoneKey !== '' ? zoneKey : null;
                            const zoneStat = findZoneStatFromPayload(latestPayload, zoneKey);
                            setZoneSelection(zone, zoneStat);
                        });
                    });

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
                        renderAreaMap(payload?.item_type_capacities ?? []);
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
