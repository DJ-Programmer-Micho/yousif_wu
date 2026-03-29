<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/logo-icon.png">
    <link rel="shortcut icon" href="../assets/images/logo-icon.png">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/images/big-logo.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/images/big-logo.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/images/big-logo.png">
    <link rel="apple-touch-icon-precomposed" sizes="57x57" href="../assets/images/big-logo.png">
    <link rel="apple-touch-icon-precomposed" href="../assets/images/big-logo.png">
    <link href="{{ asset('assets/extra-libs/c3/c3.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/chartist/dist/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/extra-libs/jvector/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/toaster.css') }}" rel="stylesheet" type="text/css">

<style>
/* ============================================================
   DESIGN TOKENS — BEVEL PURPLE SYSTEM
   ============================================================ */
:root {
    /* Purple ramp */
    --bv-1: #7c3aed;
    --bv-2: #6d28d9;
    --bv-3: #5b21b6;
    --bv-4: #4c1d95;
    --bv-5: #3b1674;
    --bv-6: #2e1060;

    /* Bevel light edges (simulate top-left highlight) */
    --bv-light-hi:   rgba(255, 255, 255, 0.30);
    --bv-light-mid:  rgba(255, 255, 255, 0.16);
    --bv-light-soft: rgba(255, 255, 255, 0.08);

    /* Bevel dark edges (simulate bottom-right shadow) */
    --bv-dark-hi:    rgba(15, 5, 40, 0.55);
    --bv-dark-mid:   rgba(15, 5, 40, 0.36);
    --bv-dark-soft:  rgba(15, 5, 40, 0.18);

    /* Raised button inset highlight */
    --bv-inset-top:    rgba(255, 255, 255, 0.22);
    --bv-inset-bottom: rgba(15, 5, 40, 0.28);

    /* Text */
    --bv-text:      #ffffff;
    --bv-text-soft: rgba(255, 255, 255, 0.82);
    --bv-text-mute: rgba(255, 255, 255, 0.54);
}

/* Ensure all topbar dropdowns render above sidebar and page content */
.app-topbar .dropdown-menu {
    z-index: 9999 !important;
    position: absolute !important;
}
.sidebar-link.disabled {
    pointer-events: none;
    opacity: .55;
    cursor: not-allowed;
}

body {
    background: linear-gradient(160deg, #f0ecff 0%, #f7f5ff 40%, #ece8fb 100%);
    background-attachment: fixed;
}

#main-wrapper,
.page-wrapper {
    background: transparent !important;
}

/* ============================================================
   TOP HEADER — THICK BEVEL SLAB
   ============================================================ */
.app-topbar {
    position: relative;
    z-index: 100;
}

.app-topbar .top-navbar {
    min-height: 80px;
    background: linear-gradient(
        175deg,
        var(--bv-1)  0%,
        var(--bv-2) 30%,
        var(--bv-3) 65%,
        var(--bv-4) 100%
    );

    /* 3-D bevel border: bright top/left, dark bottom/right */
    border-top:    2px solid var(--bv-light-hi);
    border-left:   1px solid var(--bv-light-mid);
    border-right:  1px solid var(--bv-dark-mid);
    border-bottom: 3px solid var(--bv-dark-hi);

    /* Inner emboss: bright ridge at top, dark channel at bottom */
    box-shadow:
        inset 0  3px  0   var(--bv-inset-top),
        inset 0 -3px  0   var(--bv-inset-bottom),
        inset 1px 0   0   var(--bv-light-soft),
        inset -1px 0  0   var(--bv-dark-soft),
        0 8px 32px rgba(76, 29, 149, 0.24),
        0 2px  6px rgba(15,  5, 40,  0.18);

    /* Subtle sheen overlay via pseudo */
    position: relative;
    /* overflow: hidden; */
}

.app-topbar .top-navbar::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background:
        linear-gradient(180deg,
            rgba(255,255,255,.14) 0%,
            rgba(255,255,255,.04) 28%,
            rgba(255,255,255,0)   50%,
            rgba(15,5,40,.06)     80%,
            rgba(15,5,40,.12)     100%
        );
    z-index: 0;
}

.app-topbar .top-navbar > * { position: relative; z-index: 1; }

/* Logo area */
.app-topbar .navbar-header {
    width: 270px;
    background: transparent !important;
    box-shadow: none !important;
    -webkit-box-shadow: none !important;
}

/* Force white text/icons in header */
.app-topbar,
.app-topbar .nav-link,
.app-topbar .navbar-brand span,
.app-topbar .navbar-brand img,
.app-topbar .svg-icon,
.app-topbar .feather,
.app-topbar i {
    color: #cea9ff !important;
}

/* ============================================================
   LOGO BLOCK (white card style)
   ============================================================ */
.app-navbar-brand {
    height: 80px;
    display: flex;
    align-items: center;
    padding: 0 12px 0 6px;
}

.app-navbar-brand > a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    border-radius: 12px;
    background: #ffffff;
    border-top:    1px solid rgba(255,255,255,0.95);
    border-left:   1px solid rgba(255,255,255,0.90);
    border-right:  1px solid rgba(200,185,240,0.60);
    border-bottom: 2px solid rgba(160,130,220,0.50);
    box-shadow:
        inset 0  1px  0 rgba(255,255,255,0.9),
        inset 0 -1px  0 rgba(130,100,200,0.15),
        0 4px 14px rgba(76,29,149,0.22),
        0 1px  3px rgba(76,29,149,0.14);
    transition: transform .15s ease, box-shadow .15s ease;
}

.app-navbar-brand > a:hover {
    transform: translateY(-1px);
    box-shadow:
        inset 0  1px  0 rgba(255,255,255,0.9),
        inset 0 -1px  0 rgba(130,100,200,0.15),
        0 6px 18px rgba(76,29,149,0.28),
        0 2px  6px rgba(76,29,149,0.16);
}

.app-navbar-brand span {
    color: #1e1240 !important;
    font-weight: 800;
}

/* ============================================================
   HEADER ACTION BUTTONS — RAISED BEVEL CHIPS
   ============================================================ */
.app-topbar-action,
.app-topbar-profile,
.app-topbar-language,
.app-topbar .nav-toggler,
.app-topbar .topbartoggler {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 44px;
    padding: 0 14px !important;
    border-radius: 12px;
    background: linear-gradient(175deg,
        rgba(255,255,255,.16) 0%,
        rgba(255,255,255,.07) 45%,
        rgba(15,5,40,.04)    100%
    );

    /* Bevel edges */
    border-top:    1.5px solid var(--bv-light-mid);
    border-left:   1px   solid var(--bv-light-soft);
    border-right:  1px   solid var(--bv-dark-soft);
    border-bottom: 2px   solid var(--bv-dark-mid);

    /* Inner emboss */
    box-shadow:
        inset 0  2px  0 rgba(255,255,255,.14),
        inset 0 -2px  0 rgba(15,5,40,.18),
        inset 1px 0   0 rgba(255,255,255,.06),
        inset -1px 0  0 rgba(15,5,40,.10);

    transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
    cursor: pointer;
}

.app-topbar-action:hover,
.app-topbar-profile:hover,
.app-topbar-language:hover,
.app-topbar .nav-toggler:hover,
.app-topbar .topbartoggler:hover {
    background: linear-gradient(175deg,
        rgba(255,255,255,.22) 0%,
        rgba(255,255,255,.10) 50%,
        rgba(15,5,40,.04)    100%
    );
    transform: translateY(-2px);
    box-shadow:
        inset 0  2px  0 rgba(255,255,255,.18),
        inset 0 -2px  0 rgba(15,5,40,.20),
        0 6px 16px rgba(15,5,40,.22),
        0 2px  4px rgba(15,5,40,.14);
}

.app-topbar-action:active,
.app-topbar-profile:active,
.app-topbar-language:active,
.app-topbar .nav-toggler:active,
.app-topbar .topbartoggler:active {
    transform: translateY(1px);
    background: linear-gradient(175deg,
        rgba(15,5,40,.08)    0%,
        rgba(255,255,255,.04) 50%,
        rgba(255,255,255,.10) 100%
    );
    box-shadow:
        inset 0  3px  6px rgba(15,5,40,.28),
        inset 0 -1px  0   rgba(255,255,255,.08);
    border-top-color:    var(--bv-dark-soft);
    border-bottom-color: var(--bv-light-soft);
}

/* Language switcher pill */
.app-language-switcher {
    border-radius: 10px !important;
    background: #ffffff00 !important;
    color: #2398b6 !important;
    /* border: 1px solid rgba(180,160,230,.6) !important; */
    box-shadow:
        inset 0 1px 0 rgba(255,255,255,.9),
        0 1px 3px rgba(76,29,149,.12) !important;
    font-weight: 600;
    font-size: .87rem;
}

/* Notification badge */
.app-topbar .notify-no {
    background: linear-gradient(145deg, #fb7185, #ef4444) !important;
    color: #fff;
    border: 2px solid var(--bv-3);
    box-shadow: 0 2px 6px rgba(239,68,68,.40);
}

/* ============================================================
   SIDEBAR — DEEP BEVEL PANEL
   ============================================================ */
.app-sidebar {
    background: linear-gradient(
        185deg,
        var(--bv-2)  0%,
        var(--bv-3) 40%,
        var(--bv-4) 72%,
        var(--bv-5) 100%
    ) !important;

    /* Side bevel */
    border-top:   1px solid var(--bv-light-mid);
    border-right: 2px solid var(--bv-dark-mid);

    box-shadow:
        inset  1px 0 0 var(--bv-light-soft),
        inset -2px 0 0 var(--bv-dark-soft),
        inset  0  3px 0 var(--bv-inset-top),
        20px  0 40px rgba(15,5,40,.18),
        4px   0  8px rgba(76,29,149,.12);

    position: relative;
    overflow: hidden;
}

/* Sheen layer on sidebar */
.app-sidebar::before {
    content: "";
    position: absolute;
    inset: 0;
    pointer-events: none;
    background:
        linear-gradient(180deg,
            rgba(255,255,255,.10) 0%,
            rgba(255,255,255,.02) 20%,
            rgba(255,255,255,0)   50%
        ),
        linear-gradient(90deg,
            rgba(255,255,255,.06) 0%,
            rgba(255,255,255,0)   15%
        ),
        linear-gradient(270deg,
            rgba(15,5,40,.12) 0%,
            rgba(15,5,40,0)   18%
        );
    z-index: 0;
}

.app-sidebar .scroll-sidebar {
    background: transparent !important;
    padding-bottom: 20px;
    position: relative;
    z-index: 1;
}


/* Section labels */
.app-sidebar .nav-small-cap {
    color: var(--bv-text-mute) !important;
    font-size: .70rem;
    letter-spacing: .10em;
    text-transform: uppercase;
    font-weight: 700;
    padding-left: 14px;
    margin: 18px 0 8px;
}

/* Dividers */
.app-sidebar .list-divider {
    border-color: rgba(255,255,255,.08);
    margin: 10px 8px;
}

/* ============================================================
   SIDEBAR NAV ITEMS — RAISED BEVEL BUTTONS
   ============================================================ */
.app-sidebar #sidebarnav .sidebar-item {
    margin-bottom: 4px;
}

.app-sidebar #sidebarnav .sidebar-link {
    min-height: 48px;
    border-radius: 12px;
    color: var(--bv-text-soft) !important;
    padding: 11px 14px;

    background: linear-gradient(175deg,
        rgba(255,255,255,.10) 0%,
        rgba(255,255,255,.04) 50%,
        rgba(15,5,40,.04)    100%
    );

    border-top:    1.5px solid rgba(255,255,255,.16);
    border-left:   1px   solid rgba(255,255,255,.10);
    border-right:  1px   solid rgba(15,5,40,.18);
    border-bottom: 2px   solid rgba(15,5,40,.32);

    box-shadow:
        inset 0  2px  0 rgba(255,255,255,.10),
        inset 0 -2px  0 rgba(15,5,40,.18),
        0 2px  4px rgba(15,5,40,.10);

    transition: transform .14s ease, box-shadow .14s ease, background .14s ease, color .14s ease;
}

.app-sidebar #sidebarnav .sidebar-link i,
.app-sidebar #sidebarnav .sidebar-link svg {
    color: currentColor;
    stroke: currentColor;
    transition: transform .14s ease;
}

/* Hover — rise up */
.app-sidebar #sidebarnav .sidebar-link:hover {
    color: #fff !important;
    background: linear-gradient(175deg,
        rgba(255,255,255,.18) 0%,
        rgba(255,255,255,.08) 50%,
        rgba(15,5,40,.04)    100%
    );
    transform: translateY(-2px);
    box-shadow:
        inset 0  2px  0 rgba(255,255,255,.14),
        inset 0 -2px  0 rgba(15,5,40,.22),
        0 6px 14px rgba(15,5,40,.22),
        0 2px  4px rgba(15,5,40,.14);
}

/* Active press */
.app-sidebar #sidebarnav .sidebar-link:active {
    transform: translateY(1px);
    box-shadow:
        inset 0  4px  8px rgba(15,5,40,.32),
        inset 0 -1px  0   rgba(255,255,255,.06);
    border-top-color:    rgba(15,5,40,.20);
    border-bottom-color: rgba(255,255,255,.08);
}

/* Selected / Active state — pressed-in look */
.app-sidebar #sidebarnav .sidebar-item.selected > .sidebar-link,
.app-sidebar #sidebarnav .sidebar-link.active {
    color: #fff !important;
    background: linear-gradient(175deg,
        rgba(15,5,40,.14)    0%,
        rgba(255,255,255,.06) 40%,
        rgba(255,255,255,.12) 100%
    );

    border-top:    1.5px solid rgba(15,5,40,.28);
    border-left:   1px   solid rgba(15,5,40,.18);
    border-right:  1px   solid rgba(255,255,255,.12);
    border-bottom: 2px   solid rgba(255,255,255,.16);

    box-shadow:
        inset 0  4px  8px rgba(15,5,40,.28),
        inset 0 -1px  0   rgba(255,255,255,.10),
        inset 1px 0   0   rgba(15,5,40,.12),
        0 1px  3px rgba(15,5,40,.08);

    transform: translateY(1px);
}

/* Sub-menus */
.app-sidebar .first-level,
.app-sidebar .second-level {
    background: rgba(15,5,40,.14);
    border-radius: 12px;
    padding: 6px 6px;
    margin: 4px 0 8px;
    border-top:    1px solid rgba(15,5,40,.22);
    border-bottom: 1px solid rgba(255,255,255,.06);
    box-shadow:
        inset 0 3px 8px rgba(15,5,40,.20),
        inset 0 -1px 0  rgba(255,255,255,.04);
}

.app-sidebar .first-level .sidebar-link,
.app-sidebar .second-level .sidebar-link {
    min-height: 40px;
    font-size: .88rem;
    border-radius: 10px;
}

/* ============================================================
   SIDEBAR PROMO CARD
   ============================================================ */
.sidebar-promo-card {
    margin: 12px 12px 4px;
    padding: 16px 18px;
    border-radius: 16px;
    background: linear-gradient(155deg,
        rgba(255,255,255,.12) 0%,
        rgba(255,255,255,.05) 50%,
        rgba(15,5,40,.08)   100%
    );
    border-top:    1.5px solid rgba(255,255,255,.18);
    border-left:   1px   solid rgba(255,255,255,.10);
    border-right:  1px   solid rgba(15,5,40,.20);
    border-bottom: 2px   solid rgba(15,5,40,.36);
    box-shadow:
        inset 0  2px  0 rgba(255,255,255,.10),
        inset 0 -2px  0 rgba(15,5,40,.16),
        0 4px 12px rgba(15,5,40,.14);
    color: #fff;
}

.sidebar-promo-label {
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .10em;
    text-transform: uppercase;
    color: var(--bv-text-mute);
    margin-bottom: 6px;
}

.sidebar-promo-title {
    font-size: .98rem;
    font-weight: 800;
    margin-bottom: 4px;
    color: #fff;
}

.sidebar-promo-text {
    font-size: .80rem;
    line-height: 1.55;
    color: var(--bv-text-soft);
}

/* ============================================================
   MOBILE FIXES
   ============================================================ */
@media (max-width: 767.98px) {
    .app-topbar .navbar-header {
        width: auto;
    }

    .app-navbar-brand {
        height: 72px;
    }

    .app-topbar .top-navbar {
        overflow: visible !important;
        min-height: 72px;
    }

    /* ── Collapsed panel: drop below header ── */
    #navbarSupportedContent {
        position: absolute !important;
        top: 72px !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 9999 !important;
        background: linear-gradient(
            175deg,
            var(--bv-1) 0%,
            var(--bv-2) 40%,
            var(--bv-3) 100%
        ) !important;
        border-bottom: 3px solid var(--bv-dark-hi) !important;
        box-shadow: 0 8px 24px rgba(15, 5, 40, 0.32) !important;
        padding: 12px 16px !important;
        border-radius: 0 0 16px 16px !important;
        /* NO display:flex here — let Bootstrap's collapse control display */
    }

    /* Only apply flex layout when Bootstrap has opened it (.show) */
    #navbarSupportedContent.show {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 8px !important;
    }

    /* Both ul groups sit side by side */
    #navbarSupportedContent.show .navbar-nav {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: center !important;
        justify-content: flex-start !important;
        float: none !important;
        margin: 0 !important;
        padding: 0 !important;
        gap: 6px !important;
    }

    /* Left group: bell + settings — push to left */
    #navbarSupportedContent.show .navbar-nav.float-left {
        flex: 1 1 auto !important;
        justify-content: flex-start !important;
    }

    /* Right group: profile — push to right */
    #navbarSupportedContent.show .navbar-nav.float-right {
        flex: 0 0 auto !important;
        justify-content: flex-end !important;
    }

    /* Each nav item */
    #navbarSupportedContent .navbar-nav .nav-item {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
    }

    /* Hide language switcher on mobile */
#navbarSupportedContent.show .nav-item.d-none.d-md-block {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

    /* Action buttons */
    .app-topbar-action {
        padding: 0 12px !important;
        min-height: 42px;
        border-radius: 10px;
    }

    /* Profile chip */
    .app-topbar-profile {
        padding: 0 12px !important;
        min-height: 42px;
        border-radius: 10px;
        gap: 6px !important;
    }

    /* Hide profile name on mobile */
    .app-topbar-profile .d-none.d-lg-inline-flex,
    .app-topbar-profile .d-none.d-lg-inline-block {
        display: none !important;
    }

    /* Avatar size */
    .app-topbar-profile img.rounded-circle {
        width: 30px !important;
        height: 30px !important;
    }

    /* Show the toggler on mobile */
    .app-topbar .topbartoggler {
        display: inline-flex !important;
    }
}

@media (max-width: 480px) {
    .app-topbar-language {
        display: none !important;
    }
}
.app-topbar-collapse {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex: 1 1 auto;
    min-width: 0;
    padding: 0 10px 0 0;
}

.app-topbar-collapse > .navbar-nav {
    display: flex;
    align-items: center;
    flex-direction: row;
    margin-bottom: 0;
}


</style>

    <title>Iraq Remit</title>
    @stack('css')
    @livewireStyles
</head>
<body>
    <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
    <x-header/>
    <x-navbar/>
    <div class="page-wrapper">
    @yield('app')
    <x-footer/>
    </div>
    </div>

    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/app-style-switcher.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>
    <script src="{{ asset('assets/extra-libs/c3/d3.min.js') }}"></script>
    <script src="{{ asset('assets/extra-libs/c3/c3.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js" integrity="sha512-6sSYJqDreZRZGkJ3b+YfdhB3MzmuP9R7X1QZ6g5aIXhRvR1Y/N/P47jmnkENm7YL3oqsmI6AK+V6AD99uWDnIw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        window.addEventListener('alert', event => { 
            toastr[event.detail.type](event.detail.message, 
            event.detail.title ?? ''), toastr.options = {
                "closeButton": true,
                "progressBar": true,
            }
        });
    </script>
    <form id="languageForm" action="{{ route('setLocale') }}" method="post">
        @csrf
        <input type="hidden" name="locale" id="selectedLocale" value="{{ app()->getLocale() }}">
    </form>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('language-switcher').addEventListener('change', function() {
            var selectedLocale = this.value;
            document.getElementById('selectedLocale').value = selectedLocale;
            document.getElementById('languageForm').submit();
        });

        // Disable Bootstrap topbar collapse toggler entirely
function syncTopbarToggler() {
    var toggler = document.querySelector('.topbartoggler');
    if (!toggler) return;
    // Only hide on desktop (md+), keep visible on mobile
    if (window.innerWidth >= 768) {
        toggler.style.display = 'none';
    } else {
        toggler.style.display = '';  // let CSS/Bootstrap control it
    }
}
        syncTopbarToggler();
        window.addEventListener('resize', syncTopbarToggler);

        // ── Profile dropdown ──────────────────────────────────────────
        // Move the menu to <body> so it fully escapes the header's
        // stacking context / overflow, then position with fixed coords.
        var toggle = document.getElementById('profileDropdownToggle');
        var menu   = document.getElementById('profileDropdownMenu');

        if (toggle && menu) {
            // Re-parent to body so no ancestor clips or traps it
            document.body.appendChild(menu);

            // Re-run feather on the moved icons
            if (typeof feather !== 'undefined') feather.replace();

            function openMenu() {
                var rect = toggle.getBoundingClientRect();
                // Align right edge of menu with right edge of toggle
                var menuWidth = 210;
                var left = rect.right - menuWidth;
                // Clamp so it never goes off the left edge
                if (left < 8) left = 8;
                menu.style.top  = (rect.bottom + 6) + 'px';
                menu.style.left = left + 'px';
                menu.style.display = 'block';
                toggle.setAttribute('aria-expanded', 'true');
            }

            function closeMenu() {
                menu.style.display = 'none';
                toggle.setAttribute('aria-expanded', 'false');
            }

            function isOpen() {
                return menu.style.display === 'block';
            }

            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                isOpen() ? closeMenu() : openMenu();
            });

            // Close on outside click
            document.addEventListener('click', function(e) {
                if (isOpen() && !menu.contains(e.target)) {
                    closeMenu();
                }
            });

            // Reposition on scroll or resize
            window.addEventListener('resize',  function() { if (isOpen()) openMenu(); });
            window.addEventListener('scroll',  function() { if (isOpen()) openMenu(); }, true);
        }
    });
    </script>
    @livewireScripts
    @stack('scripts')
</body>
</html>