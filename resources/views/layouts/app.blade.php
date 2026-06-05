<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FIL EATS') — {{ config('app.name') }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 250px;
            --topbar-height: 60px;
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --sidebar-bg: #1e1b4b;
            --sidebar-text: #c7d2fe;
            --sidebar-hover: rgba(255,255,255,.08);
            --sidebar-active: rgba(99,102,241,.35);
        }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; margin: 0; }

        /* Sidebar */
        #sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-width); height: 100vh;
            background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            z-index: 1000; transition: transform .25s ease;
            overflow-y: auto;
        }
        #sidebar .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }
        #sidebar .sidebar-brand a {
            text-decoration: none;
            color: #fff; font-weight: 700; font-size: 1.15rem;
            display: flex; align-items: center; gap: .5rem;
        }
        #sidebar .sidebar-brand span.badge-brand {
            background: var(--primary); color: #fff;
            font-size: .6rem; padding: .25rem .45rem;
            border-radius: .3rem; font-weight: 600;
        }
        #sidebar .nav-section {
            padding: .75rem 1rem .25rem;
            font-size: .68rem; text-transform: uppercase; letter-spacing: .08em;
            color: rgba(199,210,254,.4); font-weight: 600;
        }
        #sidebar .nav-link {
            color: var(--sidebar-text); padding: .55rem 1.25rem;
            border-radius: .5rem; margin: .1rem .5rem;
            display: flex; align-items: center; gap: .65rem;
            font-size: .875rem; transition: all .15s;
        }
        #sidebar .nav-link:hover { background: var(--sidebar-hover); color: #fff; }
        #sidebar .nav-link.active { background: var(--sidebar-active); color: #fff; font-weight: 600; }
        #sidebar .nav-link i { font-size: 1rem; width: 1.2rem; text-align: center; }
        #sidebar .sidebar-footer {
            margin-top: auto; padding: 1rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        /* Topbar */
        #topbar {
            position: fixed; top: 0; left: var(--sidebar-width); right: 0;
            height: var(--topbar-height); background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.5rem; z-index: 999; gap: 1rem;
        }

        /* Main content */
        #main-content {
            margin-left: var(--sidebar-width);
            padding-top: calc(var(--topbar-height) + 1.5rem);
            padding-bottom: 2rem;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
            min-height: 100vh;
        }

        /* Cards */
        .card { border: none; border-radius: .75rem; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .card-header { background: transparent; border-bottom: 1px solid #f1f5f9; font-weight: 600; }

        /* Stat cards */
        .stat-card { border-radius: .75rem; padding: 1.25rem 1.5rem; color: #fff; position: relative; overflow: hidden; }
        .stat-card .stat-icon {
            position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
            font-size: 3rem; opacity: .2;
        }
        .stat-card h3 { font-size: 1.75rem; font-weight: 700; margin: 0; }
        .stat-card p { margin: 0; font-size: .8rem; opacity: .85; }

        /* Toast container */
        #toast-container {
            position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999;
            display: flex; flex-direction: column; gap: .5rem;
        }

        /* Page header */
        .page-header { margin-bottom: 1.5rem; }
        .page-header h1 { font-size: 1.4rem; font-weight: 700; margin: 0; color: #1e293b; }
        .page-header p  { color: #64748b; font-size: .875rem; margin: .25rem 0 0; }

        /* Table */
        .table th { font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; color: #64748b; font-weight: 600; }
        .table td { vertical-align: middle; font-size: .875rem; }

        /* Mobile */
        @media(max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #topbar { left: 0; }
            #main-content { margin-left: 0; }
        }

        /* Order builder */
        .menu-card {
            cursor: pointer; border: 2px solid transparent;
            transition: all .15s; border-radius: .75rem;
        }
        .menu-card:hover { border-color: var(--primary); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(79,70,229,.15); }
        .menu-card.selected { border-color: var(--primary); background: #eef2ff; }
        .category-badge { font-size: .7rem; }

        /* Avatar */
        .avatar-circle {
            width: 40px; height: 40px; border-radius: 50%;
            background: var(--primary); color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-weight: 600; font-size: .875rem; flex-shrink: 0;
        }
        .avatar-lg {
            width: 100px; height: 100px; font-size: 2rem;
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <a href="{{ route('dashboard') }}">
            <i class="bi bi-clipboard2-check-fill text-indigo-400"></i>
            FIL EATS
            <span class="badge-brand">APP</span>
        </a>
    </div>

    <div class="py-2">
        <div class="nav-section">Main</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <div class="nav-section">Menu & Orders</div>
        <a href="{{ route('menu.index') }}" class="nav-link {{ request()->routeIs('menu.*') ? 'active' : '' }}">
            <i class="bi bi-menu-button-wide-fill"></i> Menu Items
        </a>
        <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.index','orders.show','orders.edit') ? 'active' : '' }}">
            <i class="bi bi-bag-check-fill"></i> Orders
        </a>
        <a href="{{ route('orders.create') }}" class="nav-link {{ request()->routeIs('orders.create') ? 'active' : '' }}">
            <i class="bi bi-plus-circle-fill"></i> New Order
        </a>

        <div class="nav-section">Administration</div>
        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Users
        </a>

        <div class="nav-section">Account</div>
        <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i> My Profile
        </a>
    </div>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                <i class="bi bi-box-arrow-left me-1"></i> Logout
            </button>
        </form>
    </div>
</nav>

<!-- Topbar -->
<header id="topbar">
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-sm btn-light d-md-none" id="sidebarToggle">
            <i class="bi bi-list fs-5"></i>
        </button>
        <span class="text-muted small d-none d-sm-inline">
            <i class="bi bi-house me-1"></i> @yield('breadcrumb', 'Dashboard')
        </span>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="dropdown">
            <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                @if(Auth::user()->avatar)
                    <img src="{{ \App\Helpers\ImageHelper::url(Auth::user()->avatar) }}" class="rounded-circle" width="30" height="30" style="object-fit:cover">
                @else
                    <div class="avatar-circle" style="width:30px;height:30px;font-size:.75rem;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
                <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-left me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- Main Content -->
<main id="main-content">
    @yield('content')
</main>

<!-- Toast Container -->
<div id="toast-container"></div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Sidebar toggle (mobile)
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('show');
    });

    // Toast helper
    function showToast(message, type = 'success') {
        const colors = {
            success: '#22c55e',
            danger:  '#ef4444',
            warning: '#f59e0b',
            info:    '#3b82f6',
        };
        const icons = {
            success: 'bi-check-circle-fill',
            danger:  'bi-x-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info:    'bi-info-circle-fill',
        };
        const id = 'toast-' + Date.now();
        const html = `
            <div id="${id}" style="
                background:#fff; border-radius:.6rem; padding:.75rem 1rem;
                box-shadow:0 4px 20px rgba(0,0,0,.12); display:flex;
                align-items:center; gap:.65rem; min-width:260px; max-width:360px;
                border-left:4px solid ${colors[type] ?? colors.success};
                animation: slideIn .25s ease;
            ">
                <i class="bi ${icons[type] ?? icons.success}" style="color:${colors[type]};font-size:1.1rem;flex-shrink:0"></i>
                <span style="font-size:.875rem;color:#1e293b;flex:1">${message}</span>
                <button onclick="document.getElementById('${id}').remove()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:1rem;padding:0 0 0 .5rem">
                    <i class="bi bi-x"></i>
                </button>
            </div>`;
        document.getElementById('toast-container').insertAdjacentHTML('beforeend', html);
        setTimeout(() => document.getElementById(id)?.remove(), 5000);
    }

    // Fire toasts from session
    @if(session('toast_success'))
        showToast(@json(session('toast_success')), 'success');
    @endif
    @if(session('toast_danger'))
        showToast(@json(session('toast_danger')), 'danger');
    @endif
    @if(session('toast_warning'))
        showToast(@json(session('toast_warning')), 'warning');
    @endif
    @if(session('toast_info'))
        showToast(@json(session('toast_info')), 'info');
    @endif
</script>
<style>
@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}
</style>
@stack('scripts')
</body>
</html>
