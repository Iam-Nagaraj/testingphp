<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('admin.dashboard')}}">
        <div class="sidebar-brand-icon">
            <img src="{{asset('/asset/img/site_logo.png')}}">
        </div>
        <div class="sidebar-brand-text mx-3">SCASH</div>
    </a>
    <li class="nav-item {{ isRouteActive('admin.dashboard') }}">
        <a class="nav-link" href="{{route('admin.dashboard')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>
    <!-- Divider -->
    <hr class="sidebar-divider my-0" />
    <li class="nav-item {{ isRouteActive('admin.user') }}">
        <a class="nav-link" href="{{route('admin.user')}}">
            <i class="fa fa-users" aria-hidden="true"></i>
            <span>Users</span></a>
    </li>
    <hr class="sidebar-divider my-0" />
    <li class="nav-item {{ isRouteActive('admin.merchant') }}">
        <a class="nav-link" href="{{route('admin.merchant')}}">
            <i class="fa fa-mars-double" aria-hidden="true"></i>
            <span>Merchants</span></a>
    </li>
    <li class="nav-item ">
        <a class="nav-link" href="{{route('admin.transactions')}}">
            <i class="fa fa-credit-card" aria-hidden="true"></i>
            <span>Transactions</span></a>
    </li>
    <li class="nav-item ">
        <a class="nav-link" href="{{route('admin.wallet')}}">
            <i class="fa fa-credit-card" aria-hidden="true"></i>
            <span>Wallet</span></a>
    </li>
    <hr class="sidebar-divider my-0" />
    <li class="nav-item {{ isRouteActive('admin.businessCategory') }}">
        <a class="nav-link" href="{{route('admin.businessCategory')}}">
            <i class="fa fa-building" aria-hidden="true"></i>
            <span>Business Category</span></a>
    </li> 
    <hr class="sidebar-divider my-0" />
    <li class="nav-item {{ isRouteActive('admin.businessSubCategory') }}">
        <a class="nav-link" href="{{route('admin.businessSubCategory')}}">
            <i class="fa fa-building" aria-hidden="true"></i>
            <span>Business Subcategory</span></a>
    </li>
    <hr class="sidebar-divider my-0" />
    <li class="nav-item {{ isRouteActive('admin.banner') }}">
        <a class="nav-link" href="{{route('admin.banner')}}">
            <i class="fa fa-building" aria-hidden="true"></i>
            <span>Banner Management</span></a>
    </li>
    <hr class="sidebar-divider my-0" />
    <li class="nav-item {{ isRouteActive('admin.businessType') }}">
        <a class="nav-link" href="{{route('admin.businessType')}}">
            <i class="fa fa-credit-card" aria-hidden="true"></i>
            <span>Business Type</span></a>
    </li>
    <hr class="sidebar-divider my-0" />
    <li class="nav-item {{ isRouteActive('admin.cashback') }}">
        <a class="nav-link" href="{{route('admin.cashback')}}">
            <i class="fa fa-credit-card" aria-hidden="true"></i>
            <span>Cash Back</span></a>
    </li>
    <hr class="sidebar-divider my-0" />
    <!-- <li class="nav-item {{ isRouteActive('admin.state') }}">
        <a class="nav-link" href="{{route('admin.state')}}">
            <i class="fa fa-university" aria-hidden="true"></i>
            <span>States</span></a>
    </li>
    <li class="nav-item {{ isRouteActive('admin.city') }}">
        <a class="nav-link" href="{{route('admin.city')}}">
            <i class="fa fa-university" aria-hidden="true"></i>
            <span>City</span></a>
    </li> -->
    <li class="nav-item {{ isRouteActive('admin.promotionalNotification') }}">
        <a class="nav-link" href="{{route('admin.promotionalNotification')}}">
            <i class="fa fa-university" aria-hidden="true"></i>
            <span>Promotional Notification</span></a>
    </li>
    <!-- Nav Item - Dashboard -->

    <!-- Divider -->
    <!-- <hr class="sidebar-divider" /> -->
    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item {{ isRouteActive('admin.configuration.walkthrough.video') }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-cog"></i>
            <span>Configurations</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="py-2 collapse-inner rounded">
                <!-- <h6 class="collapse-header">Custom Components:</h6> -->
                <a class="collapse-item" href="{{route('admin.configuration.walkthrough.video')}}">Walkthrough</a>
                <a class="collapse-item" href="{{route('admin.configuration.tax')}}">Tax</a>
                <a class="collapse-item" href="{{route('admin.configuration.referral')}}">Referral</a>
                <a class="collapse-item" href="{{route('admin.configuration.platformFee')}}">Platform Fee</a>
                <a class="collapse-item" href="{{route('admin.configuration.achFee')}}">ACH Fee</a>
                <a class="collapse-item" href="{{route('admin.configuration.supportEmail')}}">Support Email</a>
                <a class="collapse-item" href="{{route('admin.configuration.transactionLimit')}}">Payment Limit</a>
                <!-- <a class="collapse-item" href="cards.html">Cards</a> -->
            </div>
        </div>
    </li>

    <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item {{ isRouteActive('admin.cms.privacy-policy') }} {{ isRouteActive('admin.cms.term-condition') }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-wrench"></i>
            <span>CMS</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="py-2 collapse-inner rounded">
                <!-- <h6 class="collapse-header">Custom Utilities:</h6> -->
                <a class="collapse-item" href="{{route('admin.cms.privacy-policy')}}">Privacy Policy</a>
                <a class="collapse-item" href="{{route('admin.cms.term-condition')}}">Terms & Conditions</a>
                <a class="collapse-item" href="{{route('admin.faq')}}">FAQ</a>
                <!-- <a class="collapse-item" href="utilities-animation.html">Animations</a>
                    <a class="collapse-item" href="utilities-other.html">Other</a> -->
            </div>
        </div>
    </li>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>