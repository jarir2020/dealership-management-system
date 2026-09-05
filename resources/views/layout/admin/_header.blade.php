<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard') }}" class="nav-link">Home</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Contact</a>
        </li>
    </ul>

    <!-- SEARCH FORM -->
    <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
                <button class="btn btn-navbar" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        {{-- Theme picker — vanilla-JS dropdown, see public/assets/admin/dist/js/theme.js --}}
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false" title="Theme">
                <i class="fas fa-palette"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right" style="min-width: 180px;">
                <span class="dropdown-item-text"><strong>Theme</strong></span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item theme-pick" data-theme="light">
                    <i class="fas fa-sun mr-2"></i> Light
                </a>
                <a href="#" class="dropdown-item theme-pick" data-theme="dark">
                    <i class="fas fa-moon mr-2"></i> Dark
                </a>
                <a href="#" class="dropdown-item theme-pick" data-theme="purple">
                    <i class="fas fa-gem mr-2"></i> Purple
                </a>
                <a href="#" class="dropdown-item theme-pick" data-theme="green">
                    <i class="fas fa-leaf mr-2"></i> Green
                </a>
            </div>
        </li>

        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-th-large"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">{{(Auth::user()->action_table=='App\AreaManager')?'Area Manager':'Shopkeeper'}}</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class=""></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>
