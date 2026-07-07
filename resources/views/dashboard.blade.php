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
                    <p class="text-xs text-slate-500">Live map integrating actual stock, capacity, and remaining space per item type.</p>
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
                    justify-content: center;
                    align-items: center;
                    gap: 0;
                    padding: 0.2rem 0.24rem;
                    border-radius: 0.5rem;
                    border: 1px solid rgba(15, 23, 42, 0.28);
                    text-align: center;
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
                    text-align: center;
                }

                .fg-map .zone.k-dark .zone-title {
                    color: rgba(15, 23, 42, 0.92);
                    text-shadow: none;
                }

                .fg-map .zone-metric {
                    display: none !important;
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
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone compact" data-zone-key="bottle_40ml" data-zone-label="Bottle 40ml" style="left:8.4%;top:8%;width:6.4%;height:10%;background:#505050;">
                            <span class="zone-title">Bottle 40ml</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone k-dark" data-zone-key="prism_inj" data-zone-label="PRISM INJ" style="left:16.5%;top:8%;width:31.5%;height:10%;background:#7ad255;">
                            <span class="zone-title">PRISM INJ</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone k-dark" data-zone-key="part_return" data-zone-label="PART RETURN" style="left:60%;top:8%;width:11.3%;height:10%;background:#ffd321;">
                            <span class="zone-title">PART RETURN</span>
                            <span class="zone-metric"></span>
                        </button>

                        <button type="button" class="zone compact" data-zone-key="bottle_70ml" data-zone-label="Bottle 70ml" style="left:1.8%;top:22%;width:13%;height:8%;background:#9f9f9f;">
                            <span class="zone-title">Bottle 70ml</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone compact" data-zone-key="bottle_70ml" data-zone-label="Bottle 70ml" style="left:1.8%;top:30.5%;width:13%;height:8%;background:#a3a3a3;">
                            <span class="zone-title">Bottle 70ml</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone compact" data-zone-key="bottle_70ml" data-zone-label="Bottle 70ml" style="left:1.8%;top:43.5%;width:13%;height:8%;background:#a3a3a3;">
                            <span class="zone-title">Bottle 70ml</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone compact" data-zone-key="bottle_70ml" data-zone-label="Bottle 70ml" style="left:1.8%;top:52%;width:13%;height:8%;background:#a3a3a3;">
                            <span class="zone-title">Bottle 70ml</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone compact" data-zone-key="bottle_70ml" data-zone-label="Bottle 70ml" style="left:1.8%;top:65%;width:13%;height:8%;background:#a3a3a3;">
                            <span class="zone-title">Bottle 70ml</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone compact" data-zone-key="bottle_70ml" data-zone-label="Bottle 70ml" style="left:1.8%;top:73.5%;width:13%;height:8%;background:#a3a3a3;">
                            <span class="zone-title">Bottle 70ml</span>
                            <span class="zone-metric"></span>
                        </button>

                        <button type="button" class="zone vertical" data-zone-key="bottle_70ml" data-zone-label="Bottle 70ml" style="left:16.5%;top:21%;width:3.5%;height:58%;background:#a7a7a7;">
                            <span class="zone-title">Bottle 70ml</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="sakura" data-zone-label="SAKURA" style="left:20.2%;top:21%;width:3.3%;height:58%;background:#000;">
                            <span class="zone-title">SAKURA</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone" data-zone-key="n3_cover" data-zone-label="N3 COVER" style="left:23.8%;top:21%;width:5.3%;height:19%;background:#f3ba58;">
                            <span class="zone-title">N3 COVER</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone" data-zone-key="n3_cover_m" data-zone-label="N3 COVER M" style="left:23.8%;top:42%;width:5.3%;height:19%;background:#f3ba58;">
                            <span class="zone-title">N3 COVER M</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone" data-zone-key="nasuno_3_case" data-zone-label="NASUNO 3 CASE" style="left:29.2%;top:21%;width:8.6%;height:58%;background:#e95bb9;">
                            <span class="zone-title">NASUNO 3 CASE</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone" data-zone-key="n3_case_m1_m2" data-zone-label="N3 CASE M1, M2" style="left:37.9%;top:21%;width:5.7%;height:58%;background:#e95bb9;">
                            <span class="zone-title">N3 CASE M1, M2</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="craig_case_l" data-zone-label="CRAIG CASE L" style="left:43.7%;top:21%;width:3.1%;height:58%;background:#8f8f8f;">
                            <span class="zone-title">CRAIG CASE L</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="craig_case_s_456" data-zone-label="CRAIG CASE S 4,5,6" style="left:46.9%;top:21%;width:6.2%;height:58%;background:#9f9f9f;">
                            <span class="zone-title">CRAIG CASE S 4,5,6</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone vertical compact" data-zone-key="ha3p_case_s2_m" data-zone-label="HA3P CASE S2 / M" style="left:53.3%;top:21%;width:2.5%;height:58%;background:#0abb63;">
                            <span class="zone-title">HA3P CASE S2 / M</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone vertical compact" data-zone-key="ha3p_case_s2" data-zone-label="HA3P CASE S2" style="left:56%;top:21%;width:2.5%;height:58%;background:#59cad2;">
                            <span class="zone-title">HA3P CASE S2</span>
                            <span class="zone-metric"></span>
                        </button>

                        <button type="button" class="zone vertical" data-zone-key="ink_bottle_cap" data-zone-label="INK BOTTLE CAP" style="left:60%;top:21%;width:5%;height:29%;background:#1159b5;">
                            <span class="zone-title">INK BOTTLE CAP</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="top_cap" data-zone-label="TOP CAP" style="left:60%;top:50.1%;width:5%;height:28.9%;background:#1152a5;">
                            <span class="zone-title">TOP CAP</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="hamana_glee" data-zone-label="HAMANA GLEE" style="left:65.2%;top:21%;width:6.2%;height:58%;background:#5a5a5a;">
                            <span class="zone-title">HAMANA GLEE</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="hamana_grow" data-zone-label="HAMANA GROW" style="left:71.6%;top:21%;width:6.2%;height:58%;background:#5a5a5a;">
                            <span class="zone-title">HAMANA GROW</span>
                            <span class="zone-metric"></span>
                        </button>

                        <button type="button" class="zone vertical" data-zone-key="spout" data-zone-label="SPOUT" style="left:79%;top:21%;width:6.2%;height:43%;background:#2404b8;">
                            <span class="zone-title">SPOUT</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="mashu" data-zone-label="MASHU" style="left:79%;top:64.5%;width:6.2%;height:14.5%;background:#ff7a19;">
                            <span class="zone-title">MASHU</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="spout" data-zone-label="SPOUT" style="left:86.4%;top:21%;width:5.4%;height:58%;background:#2204b8;">
                            <span class="zone-title">SPOUT</span>
                            <span class="zone-metric"></span>
                        </button>

                        <button type="button" class="zone vertical compact" data-zone-key="spout" data-zone-label="SPOUT" style="left:93%;top:21%;width:5.5%;height:14%;background:#2204b8;">
                            <span class="zone-title">SPOUT</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="s15" data-zone-label="S15" style="left:93%;top:35.2%;width:5.5%;height:22%;background:#79d34f;">
                            <span class="zone-title">S15</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone vertical" data-zone-key="a3" data-zone-label="A3" style="left:93%;top:57.5%;width:5.5%;height:18%;background:#e30000;">
                            <span class="zone-title">A3</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone vertical compact" data-zone-key="fb" data-zone-label="FB" style="left:93%;top:75.8%;width:5.5%;height:9%;background:#1d1bb3;">
                            <span class="zone-title">FB</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone vertical compact" data-zone-key="adf" data-zone-label="ADF" style="left:93%;top:85%;width:5.5%;height:9%;background:#303138;">
                            <span class="zone-title">ADF</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone vertical compact" data-zone-key="lg" data-zone-label="LG" style="left:93%;top:94.2%;width:5.5%;height:5.8%;background:#d50000;">
                            <span class="zone-title">LG</span>
                            <span class="zone-metric"></span>
                        </button>

                        <button type="button" class="zone k-dark compact" data-zone-label="TRAY CLEANING" style="left:1.8%;top:81.8%;width:9.5%;height:11%;background:#cdcdcd;">
                            <span class="zone-title">TRAY CLEANING</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone k-dark compact" data-zone-label="EMPTY BOX" style="left:11.5%;top:81.8%;width:15.2%;height:11%;background:#bdbdbd;">
                            <span class="zone-title">EMPTY BOX</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone k-dark" data-zone-key="craig_cover_l" data-zone-label="CRAIG COVER L" style="left:43.5%;top:81.8%;width:15%;height:11%;background:#a9a9a9;">
                            <span class="zone-title">CRAIG COVER L</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone k-dark" data-zone-key="nasuno_case_b" data-zone-label="NASUNO CASE B" style="left:60%;top:81.5%;width:18.1%;height:10.4%;background:#a8a8a8;">
                            <span class="zone-title">NASUNO CASE B</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone k-dark" data-zone-key="cover_s_m" data-zone-label="COVER S, M" style="left:78.6%;top:81.5%;width:6.4%;height:10.4%;background:#a8a8a8;">
                            <span class="zone-title">COVER S, M</span>
                            <span class="zone-metric"></span>
                        </button>
                        <button type="button" class="zone k-dark compact" data-zone-key="prism_coating" data-zone-label="PRISM COATING" style="left:60%;top:92.6%;width:25%;height:6.9%;background:#8c5800;">
                            <span class="zone-title">PRISM COATING</span>
                            <span class="zone-metric"></span>
                        </button>
                    </div>
                </div>

                <aside class="fg-map-info-panel">
                    <div class="mb-3 rounded-xl border border-slate-200 bg-white px-3 py-3">
                        <p class="text-[11px] uppercase tracking-[0.1em] text-slate-500">Selected Zone</p>
                        <p id="fg-map-selected-label" class="mt-1 text-base font-black text-slate-900">-</p>
                        <p id="fg-map-selected-subtitle" class="mt-1 text-xs text-slate-500">Click a map area to view capacity details.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <article class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-slate-500">Stock</p>
                            <p id="fg-map-selected-stock" class="fg-map-info-value text-slate-900">0</p>
                            <p class="text-[10px] text-slate-500">pcs</p>
                        </article>
                        <article class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                            <p class="text-slate-500">Remainder</p>
                            <p id="fg-map-selected-available" class="fg-map-info-value text-emerald-700">0</p>
                            <p class="text-[10px] text-slate-500">pcs</p>
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
            class="order-1 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Realtime Dashboard</p>
                    <h3 class="mt-1 text-2xl font-black text-slate-800">FG Storage Live Monitoring</h3>
                </div>
                <div class="flex items-center gap-2">
                    <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600">
                        <span id="fg-live-indicator" class="inline-flex h-2.5 w-2.5 animate-pulse rounded-full bg-amber-500"></span>
                        <span>Last update: <span id="fg-last-update">{{ $lastUpdate ? \Illuminate\Support\Carbon::parse($lastUpdate)->format('d/m/Y H:i:s') : '-' }}</span></span>
                    </div>
                    <button
                        id="btn-notif-mute"
                        type="button"
                        title="Mute sound notifications"
                        class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-600 hover:bg-slate-100 transition-colors">
                        <span id="btn-notif-mute-icon">🔔</span>
                        <span id="btn-notif-mute-label">Sound On</span>
                    </button>
                </div>
            </div>

            <div class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-500 to-teal-600 p-4 text-white shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-100">Receive Today</p>
                    <p id="fg-receiving-qty" class="mt-3 text-3xl font-black leading-none">{{ number_format((int) data_get($todayReceiving, 'rows', 0)) }} boxes</p>
                    <p id="fg-receiving-rows" class="mt-2 text-xs text-emerald-100">{{ number_format((int) data_get($todayReceiving, 'qty', 0)) }} pcs</p>
                </article>

                <article class="rounded-2xl border border-cyan-200 bg-gradient-to-br from-cyan-500 to-blue-600 p-4 text-white shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-cyan-100">Delivery Today</p>
                    <p id="fg-delivery-qty" class="mt-3 text-3xl font-black leading-none">{{ number_format((int) data_get($todayDelivery, 'rows', 0)) }} boxes</p>
                    <p id="fg-delivery-rows" class="mt-2 text-xs text-cyan-100">{{ number_format((int) data_get($todayDelivery, 'qty', 0)) }} pcs</p>
                </article>

                <article class="rounded-2xl border border-violet-200 bg-gradient-to-br from-violet-500 to-indigo-600 p-4 text-white shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-violet-100">Current Stock</p>
                    <p id="fg-stock-qty" class="mt-3 text-3xl font-black leading-none">{{ number_format((int) data_get($stockMetrics, 'rows', 0)) }} active lots</p>
                    <p id="fg-stock-rows" class="mt-2 text-xs text-violet-100">{{ number_format((int) data_get($stockMetrics, 'qty', 0)) }} pcs</p>
                </article>

                <article class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-400 to-orange-500 p-4 text-slate-900 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-amber-900/80">Available Capacity</p>
                    <p id="fg-available-qty" class="mt-3 text-3xl font-black leading-none">{{ number_format((int) data_get($stockMetrics, 'available_qty', 0)) }} pcs</p>
                    <p id="fg-capacity-label" class="mt-2 text-xs text-amber-900/80">of total {{ number_format((int) data_get($stockMetrics, 'capacity_qty', 0)) }} pcs</p>
                </article>
            </div>

            <div class="mt-5 rounded-2xl border border-amber-200 bg-[#fffdf7] p-4 text-slate-900 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2">
                    <div>
                        <h4 class="text-sm font-black uppercase tracking-[0.12em] text-amber-800">Realtime Trend Monitor</h4>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] font-semibold">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-1" style="border:1px solid rgba(140,200,112,.5);background:rgba(140,200,112,.12);color:#4a7c32"><span class="inline-block h-3 w-3.5 rounded-[3px]" style="background:#8CC870"></span>Receive</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-1" style="border:1px solid rgba(212,135,74,.5);background:rgba(212,135,74,.12);color:#8a4a1f"><span class="inline-block h-3 w-3.5 rounded-[3px]" style="background:#D4874A"></span>Delivery</span>
                    </div>
                </div>

                <div id="fg-trend-chart-wrap" class="relative mt-3 h-72 w-full rounded-xl border border-slate-200 bg-gradient-to-b from-white to-slate-50 p-2 shadow-inner">
                    <svg id="fg-trend-chart" class="h-full w-full"></svg>
                    <div id="fg-trend-tooltip" class="pointer-events-none absolute z-20 hidden min-w-[180px] rounded-lg border border-amber-200 bg-white px-3 py-2 text-[11px] text-slate-900 shadow-xl ring-1 ring-amber-100"></div>
                </div>

                <div class="mt-3 grid gap-2 text-xs text-slate-600 sm:grid-cols-3">
                    <p>Date: <strong id="fg-trend-last-date" class="text-slate-800">-</strong></p>
                    <p>Receive: <strong id="fg-trend-last-receive" style="color:#4a7c32">0 pcs</strong></p>
                    <p>Delivery: <strong id="fg-trend-last-delivery" style="color:#8a4a1f">0 pcs</strong></p>
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
                    <p>Used: <strong id="fg-used-qty" class="text-slate-800">{{ number_format((int) data_get($stockMetrics, 'qty', 0)) }} pcs</strong></p>
                    <p>Remaining: <strong id="fg-remaining-qty" class="text-slate-800">{{ number_format((int) data_get($stockMetrics, 'available_qty', 0)) }} pcs</strong></p>
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
            <div class="mt-5 rounded-2xl border border-amber-200 bg-[#fffdf7] p-4 text-slate-900 shadow-sm">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2">
                    <div>
                        <h4 class="text-sm font-black uppercase tracking-[0.12em] text-amber-800">Available Capacity</h4>
                        <p class="mt-0.5 text-[11px] text-slate-600">Warehouse capacity utilization — actual stock vs. configured limits</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-1" style="border:1px solid rgba(140,200,112,.5);background:rgba(140,200,112,.12);color:#4a7c32"><span class="inline-block h-2 w-3.5 rounded-[2px]" style="background:#8CC870"></span>Low (&lt;80%)</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-1" style="border:1px solid rgba(232,168,85,.5);background:rgba(232,168,85,.12);color:#7a5010"><span class="inline-block h-2 w-3.5 rounded-[2px]" style="background:#E8A855"></span>High (&gt;=80%)</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-1" style="border:1px solid rgba(212,135,74,.5);background:rgba(212,135,74,.12);color:#7a3c10"><span class="inline-block h-2 w-3.5 rounded-[2px]" style="background:#D4874A"></span>Over</span>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-1" style="border:1px solid rgba(212,216,88,.55);background:rgba(212,216,88,.12);color:#6b6820"><span class="inline-block h-2 w-3.5 rounded-[2px]" style="background:#D4D858"></span>Unmapped</span>
                    </div>
                </div>
                <div id="fg-capacity-viz" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(165px,1fr));gap:8px;max-height:480px;overflow-y:auto;padding-right:4px">
                    @forelse ($itemTypeCapacities as $item)
                    @php
                    $hasCapacity = (bool) data_get($item, 'has_capacity', false);
                    $stockQty = (int) data_get($item, 'stock_qty', 0);
                    $capacityQty = $hasCapacity ? (int) data_get($item, 'capacity_qty', 0) : null;
                    $availableQty = $hasCapacity ? (int) data_get($item, 'available_qty', 0) : null;
                    $usedPercent = $hasCapacity ? (float) data_get($item, 'used_percent', 0) : null;
                    $usedPercentBar = $hasCapacity ? (float) data_get($item, 'used_percent_for_bar', 0) : 0.0;
                    $overCapacity = (bool) data_get($item, 'over_capacity', false);
                    $arcR = 28; $arcCx = 36; $arcCy = 36;
                    $arcCirc = 2 * M_PI * $arcR;
                    $arcLen = number_format($arcCirc * ($usedPercentBar / 100), 2, '.', '');
                    $arcCircStr = number_format($arcCirc, 2, '.', '');
                    $strokeColor = !$hasCapacity ? '#D4D858' : ($overCapacity ? '#D4874A' : ($usedPercent >= 80 ? '#E8A855' : '#8CC870'));
                    $borderColor = !$hasCapacity ? 'rgba(212,216,88,0.45)' : ($overCapacity ? 'rgba(212,135,74,0.5)' : ($usedPercent >= 80 ? 'rgba(232,168,85,0.5)' : 'rgba(140,200,112,0.4)'));
                    $cardBgColor = !$hasCapacity ? '#fefef5' : ($overCapacity ? '#fdf5ee' : ($usedPercent >= 80 ? '#fef9ee' : '#f5fdf0'));
                    $availColor = ($hasCapacity && !$overCapacity) ? '#4a7c32' : '#8a4a1f';
                    @endphp
                    <div style="border:1px solid {{ $borderColor }};background:{{ $cardBgColor }};border-radius:10px;padding:10px 10px 8px;display:flex;flex-direction:column;align-items:center;gap:5px;transition:all .2s"
                         onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 10px 22px rgba(15,23,42,.16)'"
                         onmouseout="this.style.transform='';this.style.boxShadow=''">
                        <svg width="68" height="68" viewBox="0 0 72 72">
                            <circle cx="{{ $arcCx }}" cy="{{ $arcCy }}" r="{{ $arcR }}" fill="none" stroke="#ede8d8" stroke-width="6"/>
                            <circle cx="{{ $arcCx }}" cy="{{ $arcCy }}" r="{{ $arcR }}" fill="none" stroke="{{ $strokeColor }}" stroke-width="6"
                                stroke-dasharray="{{ $arcLen }} {{ $arcCircStr }}" stroke-linecap="round"
                                transform="rotate(-90 {{ $arcCx }} {{ $arcCy }})"/>
                            <text x="{{ $arcCx }}" y="{{ $arcCy - 3 }}" text-anchor="middle" font-size="10" font-weight="800" fill="{{ $strokeColor }}">{{ $hasCapacity ? number_format($usedPercent, 0) . '%' : '-' }}</text>
                            <text x="{{ $arcCx }}" y="{{ $arcCy + 9 }}" text-anchor="middle" font-size="7" fill="#a08060">used</text>
                        </svg>
                        <div style="width:100%;font-size:11px;font-weight:800;color:#3b2e1a;text-align:center;word-break:break-word;line-height:1.35">{{ data_get($item, 'label', '-') }}</div>
                        @if (!$hasCapacity)
                            <span style="background:rgba(212,216,88,.2);color:#6b6820;border:1px solid rgba(212,216,88,.55);border-radius:3px;padding:1px 5px;font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">Unmapped</span>
                        @elseif ($overCapacity)
                            <span style="background:rgba(212,135,74,.18);color:#7a3c10;border:1px solid rgba(212,135,74,.5);border-radius:3px;padding:1px 5px;font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">OVER</span>
                        @elseif ($usedPercent >= 80)
                            <span style="background:rgba(232,168,85,.2);color:#7a5010;border:1px solid rgba(232,168,85,.5);border-radius:3px;padding:1px 5px;font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">HIGH</span>
                        @endif
                        <div style="width:100%;font-size:10px;color:#8a7050;line-height:1.7;margin-top:2px">
                            <div style="display:flex;justify-content:space-between"><span>Stock</span><span style="color:#3b2e1a;font-weight:700">{{ number_format($stockQty) }}</span></div>
                            @if ($capacityQty !== null)
                                <div style="display:flex;justify-content:space-between"><span>Capacity</span><span style="color:#3b2e1a;font-weight:700">{{ number_format($capacityQty) }}</span></div>
                                <div style="display:flex;justify-content:space-between"><span>Remainder</span><span style="color:{{ $availColor }};font-weight:700">{{ number_format($availableQty) }}</span></div>
                            @endif
                        </div>
                        @if ($hasCapacity)
                        <div style="width:100%;height:3px;border-radius:999px;background:#ede8d8;overflow:hidden;margin-top:2px">
                            <div style="height:3px;border-radius:999px;width:{{ number_format($usedPercentBar, 1, '.', '') }}%;background:{{ $strokeColor }}"></div>
                        </div>
                        @endif
                    </div>
                    @empty
                    <p style="grid-column:1/-1;padding:16px 0;text-align:center;font-size:12px;color:#64748b">Belum ada data stok per jenis barang.</p>
                    @endforelse
                </div>
            </div>
            <!-- end Available Capacity -->

            <script type="application/json" id="fg-initial-metrics">@json($fgMetrics)</script>
            <script>
                (function() {
                    const root = document.getElementById('fg-live-dashboard');
                    if (!root) return;

                    const formatter = new Intl.NumberFormat('id-ID');
                    const flowBody = document.getElementById('fg-flow-tbody');
                    const typeCapacityBody = document.getElementById('fg-type-capacity-tbody');
                    const trendChart = document.getElementById('fg-trend-chart');
                    const trendChartWrap = document.getElementById('fg-trend-chart-wrap');
                    const trendTooltip = document.getElementById('fg-trend-tooltip');
                    const mapRoot = document.getElementById('fg-storage-map');
                    const mapZones = mapRoot ? Array.from(mapRoot.querySelectorAll('.zone')) : [];

                    // Assign a stable zone letter (A, B, C, ... Z, AA, AB, ...) to each unique
                    // item type, ordered by its leftmost position on the storage map (left → right).
                    // The same item type always maps to the same letter, wherever it repeats on the map.
                    const zoneKeyLetters = (() => {
                        const seen = new Map();
                        mapZones.forEach((zone, index) => {
                            const key = String(zone.dataset.zoneKey || '').trim();
                            if (key === '') return;
                            const left = parseFloat(zone.style.left) || 0;
                            const existing = seen.get(key);
                            if (!existing || left < existing.left) {
                                seen.set(key, { left, order: existing ? existing.order : index });
                            }
                        });

                        const toLetters = (zeroBasedIndex) => {
                            let n = zeroBasedIndex + 1;
                            let label = '';
                            while (n > 0) {
                                const rem = (n - 1) % 26;
                                label = String.fromCharCode(65 + rem) + label;
                                n = Math.floor((n - 1) / 26);
                            }
                            return label;
                        };

                        const orderedKeys = Array.from(seen.entries())
                            .sort((a, b) => (a[1].left - b[1].left) || (a[1].order - b[1].order))
                            .map(([key]) => key);

                        const labels = new Map();
                        orderedKeys.forEach((key, idx) => labels.set(key, toLetters(idx)));
                        return labels;
                    })();

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

                        if (zoneTotal) {
                            const zoneKey = zone ? String(zone.dataset.zoneKey || '').trim() : '';
                            zoneTotal.textContent = zoneKey !== '' ? (zoneKeyLetters.get(zoneKey) || '-') : '-';
                        }

                        if (!zone || !zoneStat) {
                            setText('fg-map-selected-label', zone?.dataset.zoneLabel || 'Utility Zone');
                            setText('fg-map-selected-subtitle', 'This zone has no stock capacity configuration.');
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
                            setText('fg-map-selected-subtitle', `Capacity ${formatter.format(capacityQty)} pcs | Lot ${formatter.format(safeNumber(zoneStat.stock_rows))}`);
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
                            setText('fg-map-selected-subtitle', 'No capacity registered for this item type yet.');
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

                            if (metricEl) {
                                metricEl.textContent = '';
                                metricEl.style.backgroundColor = '';
                                metricEl.style.color = '';
                            }

                            if (!stat) {
                                return;
                            }

                            const usedPercent = safeNumber(stat.used_percent);
                            const overCapacity = Boolean(stat.over_capacity);
                            const tone = resolveUsageTone(usedPercent ?? 0, overCapacity);

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

                    const renderCapacityViz = (rows) => {
                        const container = document.getElementById('fg-capacity-viz');
                        if (!container) return;
                        if (!Array.isArray(rows) || rows.length === 0) {
                            container.innerHTML = '<p style="grid-column:1/-1;padding:16px 0;text-align:center;font-size:12px;color:#8a7050">Belum ada data stok per jenis barang.</p>';
                            return;
                        }

                        const arcR = 28, arcCx = 36, arcCy = 36;
                        const arcCircumference = 2 * Math.PI * arcR;

                        container.innerHTML = rows.map((row) => {
                            const hasCapacity = Boolean(row?.has_capacity);
                            const stockQty = Number(row?.stock_qty || 0);
                            const capacityQty = hasCapacity ? Number(row?.capacity_qty || 0) : null;
                            const availableQty = hasCapacity ? Number(row?.available_qty || 0) : null;
                            const usedPercent = hasCapacity ? Number(row?.used_percent || 0) : 0;
                            const usedPercentBar = hasCapacity ? clamp(Number(row?.used_percent_for_bar || 0), 0, 100) : 0;
                            const overCapacity = Boolean(row?.over_capacity);
                            const label = row?.label || '-';

                            const arcLen = (arcCircumference * usedPercentBar / 100).toFixed(2);
                            const circumStr = arcCircumference.toFixed(2);
                            const strokeColor = !hasCapacity ? '#D4D858' : (overCapacity ? '#D4874A' : (usedPercent >= 80 ? '#E8A855' : '#8CC870'));
                            const borderColor = !hasCapacity ? 'rgba(212,216,88,.45)' : (overCapacity ? 'rgba(212,135,74,.5)' : (usedPercent >= 80 ? 'rgba(232,168,85,.5)' : 'rgba(140,200,112,.4)'));
                            const cardBg = !hasCapacity ? '#fefef5' : (overCapacity ? '#fdf5ee' : (usedPercent >= 80 ? '#fef9ee' : '#f5fdf0'));
                            const availColor = hasCapacity && !overCapacity ? '#4a7c32' : '#8a4a1f';

                            const badge = !hasCapacity
                                ? `<span style="background:rgba(212,216,88,.2);color:#6b6820;border:1px solid rgba(212,216,88,.55);border-radius:3px;padding:1px 5px;font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">Unmapped</span>`
                                : (overCapacity
                                    ? `<span style="background:rgba(212,135,74,.18);color:#7a3c10;border:1px solid rgba(212,135,74,.5);border-radius:3px;padding:1px 5px;font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">OVER</span>`
                                    : (usedPercent >= 80
                                        ? `<span style="background:rgba(232,168,85,.2);color:#7a5010;border:1px solid rgba(232,168,85,.5);border-radius:3px;padding:1px 5px;font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.05em">HIGH</span>`
                                        : ''));

                            return `<div style="border:1px solid ${borderColor};background:${cardBg};border-radius:10px;padding:10px 10px 8px;display:flex;flex-direction:column;align-items:center;gap:5px;transition:all .2s"
                                         onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 18px rgba(90,60,20,.14)'"
                                         onmouseout="this.style.transform='';this.style.boxShadow=''">
                                <svg width="68" height="68" viewBox="0 0 72 72">
                                    <circle cx="${arcCx}" cy="${arcCy}" r="${arcR}" fill="none" stroke="#ede8d8" stroke-width="6"/>
                                    <circle cx="${arcCx}" cy="${arcCy}" r="${arcR}" fill="none" stroke="${strokeColor}" stroke-width="6"
                                        stroke-dasharray="${arcLen} ${circumStr}" stroke-linecap="round"
                                        transform="rotate(-90 ${arcCx} ${arcCy})"/>
                                    <text x="${arcCx}" y="${arcCy - 3}" text-anchor="middle" font-size="10" font-weight="800" fill="${strokeColor}">${hasCapacity ? usedPercent.toFixed(0) + '%' : '—'}</text>
                                    <text x="${arcCx}" y="${arcCy + 9}" text-anchor="middle" font-size="7" fill="#a08060">used</text>
                                </svg>
                                <div style="width:100%;font-size:11px;font-weight:800;color:#3b2e1a;text-align:center;word-break:break-word;line-height:1.35">${label}</div>
                                ${badge}
                                <div style="width:100%;font-size:10px;color:#8a7050;line-height:1.7;margin-top:2px">
                                    <div style="display:flex;justify-content:space-between"><span>Stock</span><span style="color:#3b2e1a;font-weight:700">${formatter.format(stockQty)}</span></div>
                                    ${hasCapacity ? `<div style="display:flex;justify-content:space-between"><span>Capacity</span><span style="color:#3b2e1a;font-weight:700">${formatter.format(capacityQty)}</span></div>` : ''}
                                    ${hasCapacity ? `<div style="display:flex;justify-content:space-between"><span>Remainder</span><span style="color:${availColor};font-weight:700">${formatter.format(availableQty)}</span></div>` : ''}
                                </div>
                                ${hasCapacity ? `<div style="width:100%;height:3px;border-radius:999px;background:#ede8d8;overflow:hidden;margin-top:2px">
                                    <div style="height:3px;border-radius:999px;width:${usedPercentBar.toFixed(1)}%;background:${strokeColor};transition:width .5s"></div>
                                </div>` : ''}
                            </div>`;
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
                            if (trendTooltip) {
                                trendTooltip.classList.add('hidden');
                            }
                            return;
                        }

                        const width = Math.max(trendChart.clientWidth, 320);
                        const height = Math.max(trendChart.clientHeight, 220);
                        trendChart.setAttribute('viewBox', `0 0 ${width} ${height}`);
                        trendChart.setAttribute('preserveAspectRatio', 'none');

                        const padding = { top: 18, right: 16, bottom: 30, left: 46 };
                        const chartWidth = width - padding.left - padding.right;
                        const chartHeight = height - padding.top - padding.bottom;
                        if (chartWidth <= 0 || chartHeight <= 0) return;

                        const receiveSeries = rows.map((row) => Number(row?.receiving_qty || 0));
                        const deliverySeries = rows.map((row) => Number(row?.delivery_qty || 0));

                        const barValues = [...receiveSeries, ...deliverySeries];
                        let yBarMax = Math.max(10, ...barValues);
                        yBarMax += yBarMax * 0.15;
                        const yBarRange = yBarMax || 1;

                        const slotWidth = chartWidth / Math.max(rows.length, 1);
                        const xFor = (index) => padding.left + (index + 0.5) * slotWidth;
                        const yBarFor = (value) => padding.top + ((yBarMax - Math.max(0, value)) / yBarRange) * chartHeight;
                        const baseY = padding.top + chartHeight;

                        const hideTrendTooltip = () => {
                            if (trendTooltip) {
                                trendTooltip.classList.add('hidden');
                            }
                        };

                        const showTrendTooltip = (event, detail) => {
                            if (!trendTooltip || !trendChartWrap) return;

                            trendTooltip.innerHTML = `
                                <div class="mb-1 font-black" style="color:${detail.kind === 'receive' ? '#4a7c32' : '#8a4a1f'}">${detail.label}</div>
                                <div class="text-slate-500">${detail.date}</div>
                                <div class="mt-1 flex justify-between gap-5"><span>Qty</span><strong>${formatter.format(detail.qty)} pcs</strong></div>
                                <div class="flex justify-between gap-5"><span>Boxes</span><strong>${formatter.format(detail.rows)}</strong></div>
                            `;
                            trendTooltip.classList.remove('hidden');

                            const wrapRect = trendChartWrap.getBoundingClientRect();
                            const tooltipRect = trendTooltip.getBoundingClientRect();
                            const localX = event.clientX - wrapRect.left;
                            const localY = event.clientY - wrapRect.top;
                            const left = clamp(localX + 12, 8, wrapRect.width - tooltipRect.width - 8);
                            const top = clamp(localY - tooltipRect.height - 10, 8, wrapRect.height - tooltipRect.height - 8);

                            trendTooltip.style.left = `${left}px`;
                            trendTooltip.style.top = `${top}px`;
                        };

                        for (let tick = 0; tick <= 5; tick += 1) {
                            const y = padding.top + (tick / 5) * chartHeight;
                            const barVal = yBarMax - (tick / 5) * yBarRange;
                            trendChart.appendChild(createSvgNode('line', {
                                x1: padding.left, y1: y, x2: width - padding.right, y2: y,
                                stroke: '#d9e1ec', 'stroke-width': 1, 'stroke-dasharray': '3 4', opacity: 0.95,
                            }));
                            trendChart.appendChild(createSvgNode('text', {
                                x: padding.left - 8, y: y + 3,
                                'text-anchor': 'end', 'font-size': 10, fill: '#334155',
                            }, formatter.format(Math.round(barVal))));
                        }

                        trendChart.appendChild(createSvgNode('line', {
                            x1: padding.left, y1: baseY, x2: width - padding.right, y2: baseY,
                            stroke: '#94a3b8', 'stroke-width': 1.4, opacity: 0.95,
                        }));

                        rows.forEach((row, index) => {
                            const labelStep = rows.length <= 8 ? 1 : Math.ceil(rows.length / 8);
                            const shouldDrawLabel = index === 0 || index === rows.length - 1 || index % labelStep === 0;
                            if (!shouldDrawLabel) return;
                            trendChart.appendChild(createSvgNode('text', {
                                x: xFor(index), y: height - 8,
                                'text-anchor': 'middle', 'font-size': 10, fill: '#334155',
                            }, formatDate(row?.date || '')));
                        });

                        const groupPad = slotWidth * 0.1;
                        const barW = Math.max(1, (slotWidth - groupPad * 2 - 2) / 2);

                        const drawBar = (options) => {
                            const barHeight = Math.max(2, options.height);
                            const barY = Math.min(baseY - barHeight, options.y);
                            const bar = createSvgNode('rect', {
                                x: options.x,
                                y: barY,
                                width: options.width,
                                height: barHeight,
                                fill: options.fill,
                                rx: 2,
                                opacity: 0.86,
                                cursor: 'pointer',
                            });
                            bar.addEventListener('mouseenter', (event) => {
                                bar.setAttribute('opacity', '1');
                                showTrendTooltip(event, options.detail);
                            });
                            bar.addEventListener('mousemove', (event) => showTrendTooltip(event, options.detail));
                            bar.addEventListener('click', (event) => showTrendTooltip(event, options.detail));
                            bar.addEventListener('mouseleave', () => {
                                bar.setAttribute('opacity', '0.86');
                                hideTrendTooltip();
                            });
                            trendChart.appendChild(bar);
                        };

                        rows.forEach((row, index) => {
                            const groupLeft = padding.left + index * slotWidth + groupPad;

                            const rQty = receiveSeries[index];
                            const rY = yBarFor(rQty);
                            drawBar({
                                x: groupLeft, y: rY, width: barW, height: Math.max(1, baseY - rY),
                                fill: '#8CC870', rx: 2, opacity: 0.92,
                                detail: {
                                    kind: 'receive',
                                    label: 'Receive',
                                    date: formatDate(row?.date || ''),
                                    qty: rQty,
                                    rows: Number(row?.receiving_rows || 0),
                                },
                            });

                            const dQty = deliverySeries[index];
                            const dY = yBarFor(dQty);
                            drawBar({
                                x: groupLeft + barW + 2, y: dY, width: barW, height: Math.max(1, baseY - dY),
                                fill: '#D4874A', rx: 2, opacity: 0.92,
                                detail: {
                                    kind: 'delivery',
                                    label: 'Delivery',
                                    date: formatDate(row?.date || ''),
                                    qty: dQty,
                                    rows: Number(row?.delivery_rows || 0),
                                },
                            });
                        });
                        trendChart.onmouseleave = hideTrendTooltip;

                        const lastIndex = rows.length - 1;
                        setText('fg-trend-last-date', formatDate(rows[lastIndex]?.date || '-'));
                        setText('fg-trend-last-receive', `${formatter.format(receiveSeries[lastIndex] || 0)} boxes`);
                        setText('fg-trend-last-delivery', `${formatter.format(deliverySeries[lastIndex] || 0)} boxes`);
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

                        setText('fg-receiving-qty', `${formatter.format(receivingRows)} boxes`);
                        setText('fg-receiving-rows', `${formatter.format(receivingQty)} boxes`);
                        setText('fg-delivery-qty', `${formatter.format(deliveryRows)} boxes`);
                        setText('fg-delivery-rows', `${formatter.format(deliveryQty)} boxes`);
                        setText('fg-stock-qty', `${formatter.format(stockRows)} boxes`);
                        setText('fg-stock-rows', `${formatter.format(stockQty)} boxes`);
                        setText('fg-available-qty', `${formatter.format(availableQty)} boxes`);
                        setText('fg-capacity-label', `of total ${formatter.format(capacityQty)} boxes`);
                        setText('fg-used-percent', `${usedPercent.toFixed(1)}%`);
                        setText('fg-used-qty', `${formatter.format(stockQty)} boxes`);
                        setText('fg-remaining-qty', `${formatter.format(availableQty)} boxes`);
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
                        renderCapacityViz(payload?.item_type_capacities ?? []);
                        renderAreaMap(payload?.item_type_capacities ?? []);
                        renderFlowTable(payload?.daily_flow ?? []);
                    };

                    const initialMetricsEl = document.getElementById('fg-initial-metrics');
                    const initialMetrics = initialMetricsEl ? JSON.parse(initialMetricsEl.textContent) : null;
                    if (initialMetrics) render(initialMetrics);

                    // Metrics are fetched once for the whole app by the site-wide notifier poller in
                    // layouts/app.blade.php; this page only listens for its results instead of polling
                    // the same endpoint a second time.
                    window.addEventListener('fg-metrics-updated', (event) => {
                        render(event.detail);
                        setIndicatorState('ok');
                    });
                    window.addEventListener('fg-metrics-error', () => setIndicatorState('error'));

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

                    window.addEventListener('beforeunload', () => {
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
