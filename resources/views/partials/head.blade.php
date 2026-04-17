<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title ?? 'MEGA EVENT' }} — {{ config('app.name', 'MEGA EVENT') }}</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

{{-- Tailwind CDN with custom config --}}
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                },
                colors: {
                    brand: {
                        DEFAULT: '#29ABE2',
                        dark:    '#0A7FA8',
                        darker:  '#065F80',
                        light:   '#4CBDE8',
                    },
                },
                backgroundColor: {
                    hero:    '#0A0A0A',
                    content: '#111111',
                    card:    'rgba(30,30,30,0.5)',
                },
            },
        },
    }
</script>

{{-- Anti-flash theme script: must run before body renders --}}
<script>
    (function () {
        var theme = localStorage.getItem('theme') || 'dark';
        if (theme === 'light') document.documentElement.classList.add('light');

        function updateIcons() {
            var isLight = document.documentElement.classList.contains('light');
            document.querySelectorAll('.theme-toggle-icon').forEach(function (el) {
                el.textContent = isLight ? '🌙' : '☀️';
            });
        }

        window.toggleTheme = function () {
            document.documentElement.classList.toggle('light');
            localStorage.setItem('theme', document.documentElement.classList.contains('light') ? 'light' : 'dark');
            updateIcons();
        };

        document.addEventListener('DOMContentLoaded', updateIcons);
    })();
</script>

<style>
    /* ── Theme variables ── */
    :root {
        --bg-primary:   #0A0A0A;
        --bg-secondary: #111111;
        --bg-card:      #1a1a2e;
        --text-primary: #ffffff;
        --text-secondary: #94a3b8;
        --border-color: rgba(255,255,255,0.1);
        --bg-navbar:    rgba(10,10,10,0.95);
    }

    :root.light {
        --bg-primary:   #f8fafc;
        --bg-secondary: #ffffff;
        --bg-card:      #ffffff;
        --text-primary: #0f172a;
        --text-secondary: #64748b;
        --border-color: rgba(0,0,0,0.1);
        --bg-navbar:    rgba(255,255,255,0.95);
    }

    /* ── Global base ── */
    body { background-color: var(--bg-primary); color: var(--text-primary); }

    aside { background-color: var(--bg-secondary) !important; }

    /* ── Cards ── */
    .card-dark {
        background: rgba(30, 30, 30, 0.5);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 1rem;
    }
    :root.light .card-dark {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
    }

    /* ── Brand button ── */
    .btn-primary {
        background: linear-gradient(135deg, #29ABE2, #0A7FA8);
        color: white;
        border-radius: 0.75rem;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-primary:hover {
        box-shadow: 0 8px 25px rgba(41,171,226,0.3);
        transform: scale(1.02);
    }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: var(--bg-secondary); }
    ::-webkit-scrollbar-thumb { background: #29ABE2; border-radius: 3px; }

    /* ── Inputs ── */
    input, select, textarea {
        background-color: var(--bg-card) !important;
        color: var(--text-primary) !important;
        border-color: var(--border-color) !important;
    }
    input:focus, select:focus, textarea:focus {
        border-color: #29ABE2 !important;
        outline: none !important;
        box-shadow: 0 0 0 2px rgba(41,171,226,0.2) !important;
    }
    input::placeholder, textarea::placeholder {
        color: var(--text-secondary) !important;
    }
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
    }
    :root.light input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(0);
    }
    select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2329ABE2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 0.75rem center !important;
        background-size: 1rem !important;
        appearance: none !important;
    }
    [class*="border-dashed"] {
        background-color: var(--bg-card) !important;
        border-color: rgba(41,171,226,0.3) !important;
        color: var(--text-secondary) !important;
    }
    option {
        background-color: var(--bg-card) !important;
        color: var(--text-primary) !important;
    }

    /* ── Light mode structural overrides ── */
    :root.light aside { border-color: var(--border-color) !important; }
    :root.light .border-slate-800 { border-color: var(--border-color) !important; }
    :root.light .border-white\/10 { border-color: var(--border-color) !important; }
    :root.light .border-gray-700\/50 { border-color: var(--border-color) !important; }
    :root.light .border-gray-600 { border-color: var(--border-color) !important; }

    /* Text */
    :root.light .text-white { color: var(--text-primary) !important; }
    :root.light .text-slate-300,
    :root.light .text-slate-400,
    :root.light .text-slate-500 { color: var(--text-secondary) !important; }
    :root.light .text-gray-300,
    :root.light .text-gray-400 { color: var(--text-secondary) !important; }

    /* Hover / active bg */
    :root.light .hover\:bg-white\/5:hover  { background-color: rgba(0,0,0,0.05) !important; }
    :root.light .hover\:bg-white\/10:hover { background-color: rgba(0,0,0,0.07) !important; }
    :root.light .hover\:bg-gray-700:hover,
    :root.light .hover\:bg-gray-800:hover  { background-color: rgba(0,0,0,0.07) !important; }
    :root.light .bg-white\/10 { background-color: rgba(0,0,0,0.07) !important; }
    :root.light .bg-white\/5  { background-color: rgba(0,0,0,0.04) !important; }

    /* Tables */
    :root.light td, :root.light th {
        color: var(--text-primary) !important;
        border-color: var(--border-color) !important;
    }
    :root.light tr { border-color: var(--border-color) !important; }

    /* ── Light mode: override inline style attributes ── */
    :root.light body { background-color: #f8fafc !important; color: #0f172a !important; }
    :root.light [style*="#1a1a2e"] { background-color: #ffffff !important; color: #0f172a !important; }
    :root.light [style*="#111111"] { background-color: #f1f5f9 !important; color: #0f172a !important; }
    :root.light [style*="#0A0A0A"] { background-color: #f8fafc !important; color: #0f172a !important; }
    :root.light [style*="rgba(10,10,10"] { background-color: rgba(255,255,255,0.95) !important; }
    :root.light [style*="rgba(26, 26, 46"] { background-color: #ffffff !important; }
    :root.light header { background-color: rgba(255,255,255,0.95) !important; border-color: rgba(0,0,0,0.1) !important; }
    :root.light .divide-white\/5 > * { border-color: rgba(0,0,0,0.08) !important; }
    :root.light table thead { background-color: #f1f5f9 !important; color: #64748b !important; }
    :root.light table tbody tr { background-color: #ffffff !important; color: #0f172a !important; }
    :root.light table tbody tr:hover { background-color: #f8fafc !important; }
    :root.light .rounded-2xl, :root.light .rounded-xl {
        background-color: #ffffff;
        color: #0f172a;
    }
    :root.light nav a { color: #475569 !important; }
    :root.light nav a:hover { color: #0f172a !important; }

    /* Theme toggle button */
    .theme-toggle-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 0.5rem;
        font-size: 1rem;
        line-height: 1;
        cursor: pointer;
        background: transparent;
        border: none;
        transition: background 0.2s;
    }
    .theme-toggle-btn:hover { background-color: rgba(255,255,255,0.1); }
    :root.light .theme-toggle-btn:hover { background-color: rgba(0,0,0,0.07); }
</style>

{{-- Alpine.js (only once!) --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
