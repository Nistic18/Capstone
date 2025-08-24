@auth
<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <!-- Sidebar Toggle Button on the Left -->
    <form class="form-inline mr-auto">
          <ul class="navbar-nav mr-3">
            <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg">
        <i class="fas fa-bars"></i></a>
          </ul>
        </form>
    <!-- Right Side Navbar Items -->
    <ul class="navbar-nav ml-auto">
          <ul class="navbar-nav ml-auto">
            @php
            $cart = session()->get('cart', []);
            $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
            @endphp
          <li class="nav-item">
            <a href="{{ route('cart.index') }}" class="nav-link nav-link-lg beep beep-sidebar position-relative">
            <i class="fas fa-shopping-cart fa-lg"></i>
            @if($cartCount > 0)
              <span class="badge badge-danger position-absolute top-0 start-100 translate-middle rounded-circle"
                style="font-size: 0.6rem; padding: 5px 7px;">
                {{ $cartCount }}
              </span>
            @endif
            </a>
          </li>
          <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="image" src="{{ asset('img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
                <div class="d-sm-none d-lg-inline-block">
                    Hello, {{ substr(auth()->user()->name, 0, 10) }}
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-title">
                    Welcome, {{ substr(auth()->user()->name, 0, 10) }}
                </div>
                <a class="dropdown-item has-icon edit-profile" href="{{ route('profile.edit') }}">
                    <i class="fa fa-user"></i> Edit Profile
                </a>
                <a class="dropdown-item has-icon edit-profile" href="{{ route('profile.change-password') }}">
                    <i class="fa fa-key"></i> Change Password
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
          </li>
    </ul>
</nav>
@endauth
