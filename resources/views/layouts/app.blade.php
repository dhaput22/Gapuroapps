<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>@yield('title','Gapuro System')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        #appShell {
            --sidebar-expanded-width: 18rem;
            --sidebar-collapsed-width: 4.5rem;
        }

        #sidebar {
            width: var(--sidebar-expanded-width);
            overflow-x: hidden;
            transition: width 0.25s ease;
        }

        #appShell.sidebar-collapsed #sidebar {
            width: var(--sidebar-collapsed-width);
        }

        #appShell.sidebar-collapsed .sidebar-hide-on-collapse {
            display: none !important;
        }

        #appShell.sidebar-collapsed .sidebar-center-on-collapse {
            justify-content: center;
        }

        #appShell.sidebar-collapsed .sidebar-item-on-collapse {
            justify-content: center;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        @keyframes notif-in {
            from {
                opacity: 0;
                transform: translateX(40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes notif-out {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(40px);
            }
        }
    </style>
</head>

<body class="h-full bg-gray-100 text-gray-800">

    {{-- Capacity notification toast container (must exist before page scripts run, so the very first check can render its toast).
         Polls fg-storage metrics from every authenticated page (not just the dashboard) so an over-capacity
         alert reaches the user immediately, wherever they are in the app. --}}
    <div id="capacity-notif-container"
        data-metrics-endpoint="{{ route('dashboard.fg-storage.metrics') }}"
        data-polling-ms="{{ max(1, (int) config('fg_storage.dashboard_polling_seconds', 1)) * 1000 }}"
        style="position:fixed;top:1rem;right:1rem;z-index:9999;display:flex;flex-direction:column;gap:0.5rem;pointer-events:none;max-width:380px;width:calc(100vw - 2rem)"></div>

    <div id="appShell" class="min-h-screen flex">
        <!-- SIDEBAR -->
        <aside id="sidebar" class="sticky top-0 h-screen overflow-y-auto bg-gray-50 border-r border-gray-200">
            <!-- logo + hide menu -->
            <a href="{{ route('dashboard') }}">
                <div class="px-4 py-3 flex items-center gap-3 border-b border-gray-200 sidebar-center-on-collapse">
                    <img src="/images/logo.png" class="w-10 h-10" alt="Gapuro">
                    <div class="sidebar-hide-on-collapse">
                        <div class="text-sm font-bold text-yellow-500">GAPURO SYSTEM</div>
                        <div class="text-xs text-gray-500">Production Process</div>
                    </div>
                </div>
            </a>

            <!-- Hide Menu (checkbox style) -->
            <div class="px-4 py-2 border-b border-gray-200">
                <label class="inline-flex items-center gap-2 cursor-pointer text-sm text-gray-600 sidebar-center-on-collapse">
                    <input id="chkHide" type="checkbox" class="form-checkbox" />
                    <span class="ml-1 sidebar-hide-on-collapse">Hide Menu</span>
                </label>
            </div>

            <!-- Menu groups -->
            <nav class="px-2 py-3">

                {{-- Group: MOLDING System (yellow header) --}}
                <div class="mb-3">
                    <div class="px-3 py-2 bg-yellow-300 text-sm font-semibold text-gray-800 rounded-t-md sidebar-hide-on-collapse">MOLDING System</div>

                    <ul class="bg-white border border-t-0 border-gray-200 rounded-b-md overflow-hidden">
                        <!-- <li class="group">
                            <button data-accordion="acc-1" class="w-full text-left px-4 py-3 flex items-center justify-between hover:bg-gray-50">
                                <span class="flex items-center gap-2">
                                    <svg class="w-3 h-3 text-gray-500 transform rotate-0 group-aria-expanded:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    Production Plan
                                </span>
                            </button>
                        </li> -->

                        <li class="border-t">
                            <a href="{{ route('fg.storage') }}" title="Finish Goods Control" class="w-full text-left px-4 py-3 flex items-center gap-2 hover:bg-gray-50 sidebar-item-on-collapse">
                                <svg class="w-3 h-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                                <span class="sidebar-hide-on-collapse">Finish Goods Control</span>
                            </a>
                        </li>

                        @if (auth()->user()?->isAdmin())
                        <li class="border-t">
                            <a href="{{ route('operators.index') }}" title="Operator" class="w-full text-left px-4 py-3 flex items-center justify-between hover:bg-gray-50 sidebar-item-on-collapse">
                                <span class="flex items-center gap-2">
                                    <svg class="w-3 h-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    <span class="sidebar-hide-on-collapse">Operator</span>
                                </span>
                            </a>
                        </li>
                        @endif

                        @if (auth()->user()?->isAdmin())
                        <li class="border-t">
                            <a href="{{ route('admin.users.index') }}" title="Manajemen User" class="w-full text-left px-4 py-3 flex items-center justify-between hover:bg-gray-50 sidebar-item-on-collapse">
                                <span class="flex items-center gap-2">
                                    <svg class="w-3 h-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    <span class="sidebar-hide-on-collapse">User Management</span>
                                </span>
                            </a>
                        </li>
                        @endif

                        <!-- <li class="border-t">
                            <button data-accordion="acc-5" class="w-full text-left px-4 py-3 flex items-center justify-between hover:bg-gray-50">
                                <span class="flex items-center gap-2">
                                    <svg class="w-3 h-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    Prodn Setting (MOLDING)
                                </span>
                            </button>
                        </li> -->
                    </ul>
                </div>


            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col">
            <!-- TOPBAR -->
            <header class="sticky top-0 z-40 flex items-center justify-between bg-white border-b border-gray-200 px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="text-sm text-gray-600">Current Period : <span class="font-medium text-gray-800">2025-10-01 - 2025-12-31</span></div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-xs text-gray-500">Application :
                        <select class="ml-2 text-sm border rounded px-2 pr-8 py-1">
                            <option>Production Process</option>
                        </select>
                    </div>

                    <div class="text-xs text-gray-500">Department :
                        <select class="ml-2 text-sm border rounded px-2 pr-8 py-1">
                            <option>IK MOLD</option>
                        </select>
                    </div>

                    <div class="relative flex items-center gap-3 pl-4 border-l border-gray-200">

                        <div class="text-right text-xs">
                            <div class="text-gray-500">Login : {{ now()->format('Y-m-d H:i') }}</div>
                            <div class="text-gray-700 font-medium">{{ auth()->user()->name ?? 'guest' }}</div>
                            <div class="text-[11px] text-gray-500">{{ auth()->user()->role_label ?? '-' }}</div>
                        </div>

                        <!-- Avatar button -->
                        <button id="btnUserMenu" class="focus:outline-none">
                            <img src="/images/logo.png" alt="avatar"
                                class="w-9 h-9 rounded-full object-cover cursor-pointer" />
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="userMenu"
                            class="hidden absolute right-0 top-12 w-36 bg-white border rounded shadow-md text-sm z-50">

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100">
                                    Logout
                                </button>
                            </form>

                        </div>
                    </div>

                </div>
            </header>

            <!-- content -->
            <main class="flex-1 p-6">
                <div class="bg-white rounded shadow-sm p-6 relative">
                    @yield('content')
                </div>
            </main>

            <footer class="text-xs text-gray-400 p-4 border-t border-gray-200 text-center">
                PEB ISD 2014 (Gapuro Team Site)
            </footer>
        </div>
    </div>

    {{-- Flyout panel (hidden by default) --}}
    <div id="flyout" class="hidden fixed left-72 top-32 w-64 bg-white border shadow-lg z-50">
        <div class="p-4 text-sm text-gray-700">
            <!-- content will be injected via JS -->
        </div>
    </div>

    <script>
        // Accordion toggles
        document.querySelectorAll('[data-accordion]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = btn.getAttribute('data-accordion');
                const el = document.getElementById(id);
                if (!el) return;
                el.classList.toggle('hidden');
            });
        });

        // flyout element
        const fly = document.getElementById('flyout');

        // helper: set HTML content and position the flyout next to the clicked button
        function showFlyoutFor(button, html) {
            if (!fly) return;

            // set content first so we can measure its size
            fly.querySelector('div').innerHTML = html;
            fly.style.display = 'block'; // make it measurable (use inline style)
            fly.classList.remove('hidden');

            // get button position
            const rect = button.getBoundingClientRect();
            const flyRect = fly.getBoundingClientRect();

            // compute desired left (right side of button)
            let left = rect.right + 8 + window.scrollX; // 8px gap
            let top = rect.top + window.scrollY; // align top to button's top

            // if flyout would overflow right viewport, position it to the left of the button
            const viewportWidth = document.documentElement.clientWidth;
            if (left + flyRect.width > viewportWidth - 8) {
                left = rect.left - flyRect.width - 8 + window.scrollX;
            }

            // if flyout would overflow bottom, adjust upward
            const viewportHeight = document.documentElement.clientHeight;
            if (top + flyRect.height > window.scrollY + viewportHeight - 8) {
                // try aligning bottom of flyout with bottom of button
                top = rect.bottom - flyRect.height + window.scrollY;
                // if still overflow top, clamp to viewport
                if (top < window.scrollY + 8) top = window.scrollY + 8;
            }

            // apply position
            fly.style.left = `${left}px`;
            fly.style.top = `${top}px`;
            fly.style.position = 'absolute';
            fly.style.zIndex = 9999;

            // ensure visible
            fly.classList.remove('hidden');
            fly.style.display = '';
        }

        // new flyout click handlers: find elements with data-fly
        document.querySelectorAll('[data-fg]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();

                const key = btn.getAttribute('data-fg');
                let html = '';

                if (key === 'finishgoods') {
                    html = `
          <div class="text-sm text-gray-600">Production Output</div>
          <div class="text-sm text-gray-600 mt-2">
          <a href="{{ route('fg.storage') }}" class="block py-1 hover:underline">FG Storage</a></div>
          <div class="text-sm text-gray-600 mt-2">Customer Receive</div>
          <div class="text-sm text-gray-600 mt-2">FG Delivery Plan</div>
        `;
                } else {
                    html = `<div class="text-sm text-gray-600">No items</div>`;
                }

                showFlyoutFor(btn, html);
            });
        });

        // click outside flyout to close
        document.addEventListener('click', function(e) {
            const target = e.target;
            // close if click outside fly and outside any data-fly buttons
            if (!target.closest('#flyout') && !target.closest('[data-fly]')) {
                if (fly) {
                    fly.classList.add('hidden');
                }
            }
        });

        // optional: reposition flyout on window resize / scroll (keeps it next to the original button)
        let currentTrigger = null;
        // modify showFlyoutFor to remember trigger: (simple approach)
        document.querySelectorAll('[data-fly]').forEach(btn => {
            btn.addEventListener('click', () => currentTrigger = btn);
        });
        window.addEventListener('resize', () => {
            if (currentTrigger && !fly.classList.contains('hidden')) {
                // recompute by reusing same HTML content
                showFlyoutFor(currentTrigger, fly.querySelector('div').innerHTML);
            }
        });
        window.addEventListener('scroll', () => {
            if (currentTrigger && !fly.classList.contains('hidden')) {
                showFlyoutFor(currentTrigger, fly.querySelector('div').innerHTML);
            }
        });


        // click outside flyout to close
        document.addEventListener('click', function(e) {
            const target = e.target;
            if (!target.closest('#flyout') && !target.closest('[data-fly]')) {
                fly.classList.add('hidden');
            }
        });

        // Toggle user dropdown LogOut
        document.getElementById('btnUserMenu')?.addEventListener('click', function(e) {
            e.stopPropagation();
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
        });

        // Hide menu checkbox (collapse left bar)
        const shell = document.getElementById('appShell');
        const sidebarToggle = document.getElementById('chkHide');
        const sidebarStateKey = 'gapuro_sidebar_collapsed';

        function applySidebarCollapsed(collapsed) {
            if (!shell) return;

            shell.classList.toggle('sidebar-collapsed', collapsed);
            if (sidebarToggle) {
                sidebarToggle.checked = collapsed;
            }

            // Close flyout when sidebar changes state.
            if (collapsed && fly) {
                fly.classList.add('hidden');
            }
        }

        const savedSidebarState = localStorage.getItem(sidebarStateKey);
        if (savedSidebarState === '1' || savedSidebarState === '0') {
            applySidebarCollapsed(savedSidebarState === '1');
        } else {
            applySidebarCollapsed(false);
        }

        sidebarToggle?.addEventListener('change', function() {
            const collapsed = Boolean(this.checked);
            applySidebarCollapsed(collapsed);
            localStorage.setItem(sidebarStateKey, collapsed ? '1' : '0');
        });

        // Client-side table sorting for static/mock pages.
        document.querySelectorAll('table[data-client-sort="true"]').forEach((table) => {
            const tbody = table.tBodies[0];
            if (!tbody) return;

            const headers = Array.from(table.querySelectorAll('thead th'));
            headers.forEach((th) => {
                if (th.dataset.sortable === 'false') return;

                th.classList.add('cursor-pointer');
                th.addEventListener('click', () => {
                    const colIndex = th.cellIndex;
                    const currentIndex = Number(table.dataset.sortIndex ?? '-1');
                    const currentDir = table.dataset.sortDir === 'asc' ? 'asc' : 'desc';
                    const nextDir = currentIndex === colIndex && currentDir === 'asc' ? 'desc' : 'asc';
                    sortTableRows(tbody, colIndex, nextDir);
                    table.dataset.sortIndex = String(colIndex);
                    table.dataset.sortDir = nextDir;
                });
            });

            const defaultIndex = Number(table.dataset.defaultSortIndex ?? '-1');
            const defaultDir = table.dataset.defaultSortDir === 'asc' ? 'asc' : 'desc';
            if (defaultIndex >= 0) {
                sortTableRows(tbody, defaultIndex, defaultDir);
                table.dataset.sortIndex = String(defaultIndex);
                table.dataset.sortDir = defaultDir;
            }
        });

        function sortTableRows(tbody, colIndex, direction) {
            const allRows = Array.from(tbody.querySelectorAll('tr'));
            const dataRows = [];
            const nonDataRows = [];

            allRows.forEach((row) => {
                const cells = row.querySelectorAll('td');
                if (cells.length === 0) {
                    nonDataRows.push(row);
                    return;
                }

                if (cells.length === 1 && cells[0].hasAttribute('colspan')) {
                    nonDataRows.push(row);
                    return;
                }

                dataRows.push(row);
            });

            dataRows.sort((leftRow, rightRow) => {
                const leftText = normalizeSortText(leftRow.cells[colIndex]?.innerText ?? '');
                const rightText = normalizeSortText(rightRow.cells[colIndex]?.innerText ?? '');

                const leftNumber = toNumber(leftText);
                const rightNumber = toNumber(rightText);
                if (leftNumber !== null && rightNumber !== null) {
                    return direction === 'asc' ? leftNumber - rightNumber : rightNumber - leftNumber;
                }

                const leftDate = toDate(leftText);
                const rightDate = toDate(rightText);
                if (leftDate !== null && rightDate !== null) {
                    return direction === 'asc' ? leftDate - rightDate : rightDate - leftDate;
                }

                const compare = leftText.localeCompare(rightText, undefined, {
                    numeric: true,
                    sensitivity: 'base',
                });

                return direction === 'asc' ? compare : -compare;
            });

            dataRows.forEach((row) => tbody.appendChild(row));
            nonDataRows.forEach((row) => tbody.appendChild(row));
        }

        function normalizeSortText(value) {
            return String(value).replace(/\\s+/g, ' ').trim();
        }

        function toNumber(value) {
            const cleaned = value.replace(/,/g, '');
            if (!/^-?\\d+(\\.\\d+)?$/.test(cleaned)) {
                return null;
            }

            const numberValue = Number(cleaned);
            return Number.isNaN(numberValue) ? null : numberValue;
        }

        function toDate(value) {
            if (!/^\\d{4}-\\d{2}-\\d{2}(\\s+\\d{2}:\\d{2}(:\\d{2})?)?$/.test(value)) {
                return null;
            }

            const dateValue = Date.parse(value.replace(' ', 'T'));
            return Number.isNaN(dateValue) ? null : dateValue;
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const menu = document.getElementById('userMenu');
            const btn = document.getElementById('btnUserMenu');
            if (!menu.contains(e.target) && !btn.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        // Prevent href="#" links from scrolling to top (e.g. disabled pagination buttons)
        document.addEventListener('click', function(e) {
            const a = e.target.closest('a');
            if (a && a.getAttribute('href') === '#') {
                e.preventDefault();
            }
        }, true);

        // Lock/unlock body scroll for modals while preserving user's scroll position
        let _gapuroScrollY = 0;

        function gapuroLockScroll() {
            _gapuroScrollY = window.scrollY;
            document.body.style.overflow = 'hidden';
        }

        function gapuroUnlockScroll() {
            document.body.style.overflow = '';
            window.scrollTo(0, _gapuroScrollY);
        }
    </script>

    {{-- Site-wide FG storage capacity notifier: polls capacity metrics from every page (not just the
         dashboard) and raises a toast + sound the moment any item type or the warehouse as a whole
         goes over capacity. Dashboard.blade.php listens for the 'fg-metrics-updated'/'fg-metrics-error'
         events this dispatches to refresh its own widgets instead of polling a second time. --}}
    <script>
        (function() {
            const container = document.getElementById('capacity-notif-container');
            if (!container) return;

            const endpoint = container.dataset.metricsEndpoint || '';
            if (!endpoint) return;

            const pollingMs = Math.max(1000, Number(container.dataset.pollingMs || '2000'));
            const formatter = new Intl.NumberFormat('id-ID');
            const WARN_THRESHOLD = 80;
            const prevState = {};
            let soundMuted = localStorage.getItem('fg_sound_muted') === 'true';

            function playSound(type) {
                if (soundMuted) return;
                try {
                    const AudioCtx = window.AudioContext || window.webkitAudioContext;
                    if (!AudioCtx) return;
                    const ctx = new AudioCtx();
                    const notes = type === 'over' ?
                        [
                            [880, 0.12],
                            [880, 0.12],
                            [880, 0.22]
                        ] :
                        [
                            [520, 0.15],
                            [520, 0.15]
                        ];
                    let t = ctx.currentTime + 0.05;
                    notes.forEach(([freq, dur]) => {
                        const osc = ctx.createOscillator();
                        const gain = ctx.createGain();
                        osc.connect(gain);
                        gain.connect(ctx.destination);
                        osc.type = 'sine';
                        osc.frequency.value = freq;
                        gain.gain.setValueAtTime(0.45, t);
                        gain.gain.exponentialRampToValueAtTime(0.001, t + dur);
                        osc.start(t);
                        osc.stop(t + dur);
                        t += dur + 0.07;
                    });
                    setTimeout(() => ctx.close(), (t + 0.5) * 1000);
                } catch (_) {}
            }

            function showToast(level, title, body) {
                const isOver = level === 'over';
                const id = `cap-n-${Date.now()}-${Math.random().toString(36).slice(2, 6)}`;
                const bg = isOver ? '#fff1f2' : '#fffbeb';
                const border = isOver ? '#f43f5e' : '#f59e0b';
                const textColor = isOver ? '#881337' : '#78350f';
                const toast = document.createElement('div');
                toast.id = id;
                toast.style.cssText =
                    `pointer-events:auto;background:${bg};border:1.5px solid ${border};border-radius:12px;padding:12px 14px;box-shadow:0 4px 24px rgba(0,0,0,.13);display:flex;gap:10px;align-items:flex-start;animation:notif-in 0.3s ease`;
                const closeBtn = document.createElement('button');
                closeBtn.type = 'button';
                closeBtn.title = 'Close';
                closeBtn.style.cssText =
                    `flex-shrink:0;background:none;border:none;cursor:pointer;font-size:18px;color:${textColor};opacity:.45;padding:0 2px;line-height:1`;
                closeBtn.textContent = '×';
                closeBtn.addEventListener('click', function() {
                    toast.style.animation = 'notif-out 0.25s ease forwards';
                    setTimeout(function() {
                        toast.remove();
                    }, 250);
                });
                const icon = document.createElement('span');
                icon.style.cssText = 'font-size:22px;line-height:1.1;flex-shrink:0';
                icon.textContent = isOver ? '🚨' : '⚠️';
                const content = document.createElement('div');
                content.style.cssText = 'flex:1;min-width:0';
                content.innerHTML = `
                    <div style="font-weight:700;font-size:13px;color:${textColor};line-height:1.3">${title}</div>
                    <div style="font-size:11.5px;color:${textColor};opacity:.8;margin-top:3px;line-height:1.4">${body}</div>
                `;
                toast.appendChild(icon);
                toast.appendChild(content);
                toast.appendChild(closeBtn);
                container.appendChild(toast);
            }

            function check(payload) {
                const items = payload?.item_type_capacities ?? [];
                const pct = Number(payload?.stock?.used_percent ?? 0);
                const over = Boolean(payload?.stock?.over_capacity);

                // warehouse-wide check
                const wLvl = over ? 'over' : pct >= WARN_THRESHOLD ? 'warn' : 'ok';
                const wPrev = prevState['__wh__'] ?? 'ok';
                if (wLvl !== wPrev) {
                    if (wLvl === 'over') {
                        playSound('over');
                        showToast('over', 'WAREHOUSE OVER CAPACITY!', `Total warehouse capacity has been exceeded — ${pct.toFixed(1)}% used`);
                    } else if (wLvl === 'warn') {
                        playSound('warn');
                        showToast('warn', 'Warehouse Capacity Warning', `Warehouse capacity is approaching its limit — ${pct.toFixed(1)}% used`);
                    }
                }
                prevState['__wh__'] = wLvl;

                // per item type check
                items.forEach(function(item) {
                    if (!item.has_capacity) return;
                    const k = item.key;
                    const ip = Number(item.used_percent ?? 0);
                    const io = Boolean(item.over_capacity);
                    const lvl = io ? 'over' : ip >= WARN_THRESHOLD ? 'warn' : 'ok';
                    const prev = prevState[k] ?? 'ok';
                    if (lvl !== prev) {
                        if (lvl === 'over') {
                            playSound('over');
                            showToast('over', `${item.label} — OVER CAPACITY`, `Stock exceeds capacity by ${formatter.format(item.excess_qty ?? 0)} pcs (${ip.toFixed(1)}% used)`);
                        } else if (lvl === 'warn') {
                            playSound('warn');
                            showToast('warn', `${item.label} — Approaching Limit`, `Capacity almost full — ${ip.toFixed(1)}% used`);
                        }
                    }
                    prevState[k] = lvl;
                });
            }

            function syncMuteUI() {
                const btn = document.getElementById('btn-notif-mute');
                const icon = document.getElementById('btn-notif-mute-icon');
                const lbl = document.getElementById('btn-notif-mute-label');
                if (btn) btn.title = soundMuted ? 'Enable sound notifications' : 'Mute sound notifications';
                if (icon) icon.textContent = soundMuted ? '🔇' : '🔔';
                if (lbl) lbl.textContent = soundMuted ? 'Sound Off' : 'Sound On';
            }

            document.getElementById('btn-notif-mute')?.addEventListener('click', function() {
                soundMuted = !soundMuted;
                localStorage.setItem('fg_sound_muted', soundMuted);
                syncMuteUI();
            });
            syncMuteUI();

            const poll = async () => {
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
                    check(payload);
                    window.dispatchEvent(new CustomEvent('fg-metrics-updated', {
                        detail: payload
                    }));
                } catch (error) {
                    window.dispatchEvent(new CustomEvent('fg-metrics-error'));
                }
            };

            poll();
            const timer = setInterval(poll, pollingMs);
            window.addEventListener('beforeunload', () => clearInterval(timer));
        })();
    </script>
    @stack('scripts')
</body>

</html>