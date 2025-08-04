@auth
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
        <a href="">FISH MARKET</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
        <a href="">FISH</a>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Home</li>
            <li class="{{ Request::is('home') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('home') }}"><i class="fas fa-home"></i><span>Home</span></a>
            </li>
             <li class="{{ Request::is('orders') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('orders') }}"><i class="fas fa-box"></i><span>Orders</span></a>
            </li>
            <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('dashboard') }}"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            </li>
            <li class="menu-header">Manage</li>
            <li class="{{ Request::routeIs('users.index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('users.index') }}"><i class="far fa-user"></i> <span>User Manage</span></a>
            </li>
             <li class="{{ Request::is('products/index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('products/index') }}"><i class="fas fa-table"></i> <span>Manage Products</span></a>
            </li>
            <li class="{{ Request::is('supplier/orders') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('supplier.orders') }}"><i class="fas fa-fire"></i><span>Supplier Orders</span></a>
            </li>
            <li class="{{ Request::is('location-map') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('map') }}"><i class="fas fa-map-marked-alt"></i><span>Map Location</span></a>
            </li>
            <li class="{{ Request::is('profile/change-password') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('profile/change-password') }}"><i class="fas fa-key"></i> <span>Change Password</span></a>
            </li>
            <li class="menu-header">Starter</li>
            <li class="{{ Request::is('blank-page') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('blank-page') }}"><i class="far fa-square"></i> <span>Blank Page</span></a>
            </li>
        </ul>
    </aside>
</div>
@endauth
