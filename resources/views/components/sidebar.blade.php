@auth
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
<div class="sidebar-brand">
    <a href="
        @if(auth()->user()->role === 'admin')
            {{ route('users.index') }}
        @elseif(auth()->user()->role === 'supplier')
            {{ route('supplier.dashboard') }}
        @else
            {{ url('home') }}
        @endif
    ">
        FISH MARKET
    </a>
</div>

<div class="sidebar-brand sidebar-brand-sm">
    <a href="
        @if(auth()->user()->role === 'admin')
            {{ route('users.index') }}
        @elseif(auth()->user()->role === 'supplier')
            {{ route('supplier.dashboard') }}
        @else
            {{ url('home') }}
        @endif
    ">
        FISH
    </a>
</div>

        <ul class="sidebar-menu">

            {{-- Buyer Sidebar --}}
            @if(auth()->user()->role === 'buyer')
            <li class="menu-header">Home</li> 
            <li class="{{ Request::is('home') ? 'active' : '' }}"> 
                <a class="nav-link" href="{{ url('home') }}">
                    <i class="fas fa-home"></i><span>Home</span>
                </a> 
                </li>
                <li class="menu-header">Buyer Menu</li>
                <li class="{{ Request::is('orders') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ url('orders') }}">
                        <i class="fas fa-box"></i><span>Orders</span>
                    </a>
                </li>
                <li class="{{ Request::is('myprofile') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('profile.myprofile', auth()->id()) }}">
                        <i class="fas fa-user-circle"></i><span>My Profile</span>
                    </a>
                </li>
                <li class="{{ Request::is('location-map') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('map') }}">
                        <i class="fas fa-map-marked-alt"></i><span>Map Location</span>
                    </a>
                </li>
                <li class="{{ Request::is('newsfeed') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('newsfeed.index') }}">
                        <i class="fas fa-newspaper"></i><span>Newsfeed</span>
                    </a>
                </li>
                <li class="{{ Request::is('buyer/dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('buyer.dashboard') }}">
                        <i class="fas fa-chart-pie"></i><span>My Dashboard</span>
                    </a>
                </li>
                <li class="{{ Request::is('buyer/reviews') ? 'active' : '' }}"> 
                    <a class="nav-link" href="{{ route('buyer.reviews') }}">
                        <i class="fas fa-star"></i><span>My Reviews</span>
                    </a> 
                </li>
                <li class="{{ Request::is('reseller/apply') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('reseller.create') }}">
                        <i class="fas fa-user-plus"></i><span>Apply as Supplier</span>
                    </a>
                </li>
            @endif

            {{-- Admin Sidebar --}}
            @if(auth()->user()->role === 'admin')
            <li class="menu-header">Manage</li> 
            <li class="{{ Request::routeIs('users.index') ? 'active' : '' }}"> 
                <a class="nav-link" href="{{ route('users.index') }}">
                    <i class="far fa-user"></i> <span>Admin User Manage</span>
                </a> 
            </li>
                <li class="{{ Request::is('admin/landing*') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('admin.landing.index') }}">
                        <i class="fas fa-globe"></i><span>Landing Page Content</span>
                    </a>
                </li>
            {{-- <li class="{{ Request::is('orders') ? 'active' : '' }}"> 
                <a class="nav-link" href="{{ url('orders') }}">
                    <i class="fas fa-box"></i><span>Orders</span>
                </a> 
            </li> --}}
            {{-- <li class="menu-header">Account</li> <li class="{{ Request::is('myprofile') ? 'active' : '' }}"> 
                <a class="nav-link" href="{{ route('profile.myprofile', auth()->id()) }}">
                    <i class="fas fa-user-circle"></i><span>My Profile</span>
                </a>
            </li> --}}
            {{-- <li class="{{ Request::is('products/index') ? 'active' : '' }}"> 
                <a class="nav-link" href="{{ url('products/index') }}">
                    <i class="fas fa-table"></i> <span>Reseller Manage Products</span>
                </a> 
            </li>
            <li class="{{ Request::is('supplier/orders') ? 'active' : '' }}"> 
                <a class="nav-link" href="{{ route('supplier.orders') }}">
                    <i class="fas fa-fire"></i><span>Reseller Orders</span>
                </a> 
            </li> --}}
            {{-- <li class="{{ Request::is('supplierproduct/index') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('supplierproduct/index') }}">
                    <i class="fas fa-table"></i> <span>Supplier Manage Products</span>
                </a>
            </li> --}}
            {{-- <li class="{{ Request::is('supplier/orders') ? 'active' : '' }}"> 
                <a class="nav-link" href="{{ route('supplier.orders') }}">
                    <i class="fas fa-fire"></i><span>Supplier Orders</span>
                </a>
             </li> --}}
            <li class="{{ Request::is('location-map') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('map') }}">
                    <i class="fas fa-map-marked-alt"></i><span>Map Location</span>
            </a>
            </li>
            <li class="menu-header">Community Management</li>
            <li class="{{ Request::is('admin/posts/review') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.posts.review') }}">
                    <i class="fas fa-users"></i> <span>Review Posts</span>
                </a>
            </li>
            <li class="{{ Request::is('newsfeed') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('newsfeed.index') }}">
                    <i class="fas fa-newspaper"></i><span>Community Newsfeed</span>
             </a>
            </li>
            <li class="{{ Request::is('newsfeedsupplier') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('newsfeedsupplier.index') }}">
                    <i class="fas fa-newspaper"></i><span>Supplier Community Newsfeed</span>
             </a>
            </li>
            {{-- <li class="{{ Request::is('notifications') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('notifications.index') }}">
                    <i class="fas fa-bell"></i><span>Notifications</span>
            @php
            $unread = auth()->user()->unreadNotifications()->count();
            @endphp
            @if($unread > 0)
            <span class="badge bg-danger">{{ $unread }}</span>
            @endif
                </a>
            </li> --}}
            <li class="{{ Request::is('admin/reports') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('admin.reports.index') }}">
        <i class="fas fa-chart-bar"></i><span>Reports & Analytics</span>
    </a>
</li>
            @endif

            {{-- Reseller Sidebar --}}
            @if(auth()->user()->role === 'reseller')
                <li class="menu-header">Home</li> 
                <li class="{{ Request::is('home') ? 'active' : '' }}"> 
                    <a class="nav-link" href="{{ url('home') }}">
                        <i class="fas fa-home"></i><span>Home</span>
                    </a> 
                </li>
            <li class="{{ Request::is('orders') ? 'active' : '' }}"> 
                <a class="nav-link" href="{{ url('orders') }}">
                    <i class="fas fa-box"></i><span>Orders</span>
                </a> 
            </li>
            <li class="menu-header">Supplier</li>
            <li class="{{ Request::is('products/index') ? 'active' : '' }}"> 
                <a class="nav-link" href="{{ url('products/index') }}">
                    <i class="fas fa-table"></i> <span>Reseller Manage Products</span>
                </a> 
            </li>
            <li class="{{ Request::is('supplier/orders') ? 'active' : '' }}"> 
                <a class="nav-link" href="{{ route('supplier.orders') }}">
                    <i class="fas fa-fire"></i><span>Reseller Orders</span>
                </a> 
            </li>
            <li class="menu-header">Account</li>
            <li class="{{ Request::is('myprofile') ? 'active' : '' }}"> 
                    <a class="nav-link" href="{{ route('profile.myprofile', auth()->id()) }}">
                        <i class="fas fa-user-circle"></i><span>My Profile</span>
                </a>
            </li>
            <li class="{{ Request::is('location-map') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('map') }}">
                    <i class="fas fa-map-marked-alt"></i><span>Map Location</span>
            </a>
            </li>
            <li class="{{ Request::is('newsfeed') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('newsfeed.index') }}">
                    <i class="fas fa-newspaper"></i><span>Newsfeed</span>
             </a>
            </li>
            <li class="{{ Request::is('supplier/dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('supplier.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i><span>Analytics Dashboard</span>
                </a>
            </li>
            {{-- <li class="{{ Request::is('notifications') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('notifications.index') }}">
                    <i class="fas fa-bell"></i><span>Notifications</span>
            @php
            $unread = auth()->user()->unreadNotifications()->count();
            @endphp
            @if($unread > 0)
            <span class="badge bg-danger">{{ $unread }}</span>
            @endif
                </a>
            </li> --}}
            @endif

            {{-- Supplier Sidebar --}}
            @if(auth()->user()->role === 'supplier')
                <li class="menu-header">Home</li> 
                <li class="{{ Request::is('supplier/dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('supplier.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i><span>Home Dashboard</span>
                    </a>
                </li>
                <li class="menu-header">Products</li>
            <li class="{{ Request::is('products/index') ? 'active' : '' }}"> 
                <a class="nav-link" href="{{ url('products/index') }}">
                    <i class="fas fa-table"></i> <span>Manage Products</span>
                </a> 
            </li>
            <li class="{{ Request::is('supplier/orders') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('supplier.orders') }}">
                        <i class="fas fa-fire"></i><span>Orders</span>
                    </a>
                </li>
                <li class="menu-header">Inventory</li>
                    <li class="{{ Request::is('inventory*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('inventory.index') }}">
                        <i class="fas fa-boxes"></i><span>Inventory Management</span>
                     </a>
                </li>
                <li class="menu-header">Account</li> 
            <li class="{{ Request::is('myprofile') ? 'active' : '' }}"> 
                    <a class="nav-link" href="{{ route('profile.myprofile', auth()->id()) }}">
                        <i class="fas fa-user-circle"></i><span>My Profile</span>
                </a>
            </li>
            <li class="{{ Request::is('profile/reviews') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('profile.reviews') }}">
        <i class="fas fa-star"></i>
        <span>Reviews</span>
    </a>
</li>
                <li class="{{ Request::is('location-map') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('map') }}">
                    <i class="fas fa-map-marked-alt"></i><span>Map Location</span>
            </a>
            </li>
            <li class="menu-header">Community</li>
            <li class="{{ Request::is('newsfeed') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('newsfeed.index') }}">
                    <i class="fas fa-newspaper"></i><span>Community Newsfeed</span>
             </a>
            </li>
            {{-- <li class="{{ Request::is('newsfeedsupplier') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('newsfeedsupplier.index') }}">
                    <i class="fas fa-newspaper"></i><span>Supplier Community Newsfeed</span>
             </a> --}}
            </li>
            <li class="menu-header">Reports & Analytics</li>
    <li class="{{ Request::is('supplier/reports*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('supplier.reports.index') }}">
            <i class="fas fa-chart-line"></i><span>Reports Dashboard</span>
        </a>
    </li>
            @endif
        </ul>
    </aside>
</div>
@endauth
