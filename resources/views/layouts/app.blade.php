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
    </style>
</head>

<body class="h-full bg-gray-100 text-gray-800">

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

                        @if (auth()->user()?->isAdmin())
                            <li class="border-t">
                                <a href="{{ route('admin.users.index') }}" title="Manajemen User" class="w-full text-left px-4 py-3 flex items-center justify-between hover:bg-gray-50 sidebar-item-on-collapse">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-3 h-3 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                        <span class="sidebar-hide-on-collapse">Manajemen User</span>
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
                            <div class="text-gray-700 font-medium">{{ auth()->user()->username ?? 'guest' }}</div>
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
                <div class="bg-white rounded shadow-sm p-6 min-h-[60vh] relative">
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
    </script>
</body>

</html>
