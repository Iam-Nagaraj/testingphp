<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('merchant.dashboard')}}">
        <div class="sidebar-brand-icon">
            <img src="{{asset('/asset/img/site_logo.png')}}">
        </div>
        <div class="sidebar-brand-text mx-3">SCASH</div>
    </a>
    <li class="nav-item {{ isRouteActive('merchant.dashboard') }}">
        <a class="nav-link" href="{{route('merchant.dashboard')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <hr class="sidebar-divider my-0" />

    <li class="nav-item ">
        <a class="nav-link" href="{{route('merchant.cashback.rule.form')}}">
            <i class="fa fa-credit-card" aria-hidden="true"></i>
            <span>Cash back Rule</span></a>
    </li>
    <li class="nav-item ">
        <a class="nav-link" href="{{route('merchant.wallet')}}">
            <i class="fa fa-credit-card" aria-hidden="true"></i>
            <span>Wallet</span></a>
    </li>

    <li class="nav-item ">
        <a class="nav-link" href="{{route('merchant.bank')}}">
            <i class="fa fa-credit-card" aria-hidden="true"></i>
            <span>Bank</span></a>
    </li>

    <li class="nav-item ">
        <a class="nav-link" href="{{route('merchant.notifications')}}">
            <i class="fa fa-credit-card" aria-hidden="true"></i>
            <span>Notification</span></a>
    </li>

    <li class="nav-item ">
        <a class="nav-link" href="{{route('merchant.store')}}">
            <i class="fa fa-credit-card" aria-hidden="true"></i>
            <span>Store</span></a>
    </li>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
