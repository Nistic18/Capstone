@auth
<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <!-- Right Side Navbar Items -->
    <ul class="navbar-nav ml-auto">
          <ul class="navbar-nav ml-auto">
            @php
            $cart = session()->get('cart', []);
            $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
            @endphp
            <li class="dropdown dropdown-list-toggle-show">
              <a href="#" class="nav-link nav-link-lg message-toggle beep" data-toggle="dropdown" aria-expanded="false">
                <i class="far fa-envelope"></i>
              </a>
                <div class="dropdown-menu dropdown-list dropdown-menu-right" id="messageDropdown">
                    <!-- Messages content here --> 
                      <div class="dropdown-header">Messages
                        <div class="float-right">
                          <a href="#">Mark All As Read</a>
                        </div>
                      </div>
                      <div class="dropdown-list-content dropdown-list-message" tabindex="2" style="overflow: hidden; outline: none;">
                        <a href="#" class="dropdown-item dropdown-item-unread">
                          <div class="dropdown-item-avatar">
                            <img alt="image" src="assets/img/avatar/avatar-1.png" class="rounded-circle">
                            <div class="is-online"></div>
                          </div>
                          <div class="dropdown-item-desc">
                            <b>Kusnaedi</b>
                            <p>Hello, Bro!</p>
                            <div class="time">10 Hours Ago</div>
                          </div>
                        </a>
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="assets/img/avatar/avatar-2.png" class="rounded-circle">
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Dedik Sugiharto</b>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit</p>
                    <div class="time">12 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item dropdown-item-unread">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="assets/img/avatar/avatar-3.png" class="rounded-circle">
                    <div class="is-online"></div>
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Agung Ardiansyah</b>
                    <p>Sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                    <div class="time">12 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="assets/img/avatar/avatar-4.png" class="rounded-circle">
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Ardian Rahardiansyah</b>
                    <p>Duis aute irure dolor in reprehenderit in voluptate velit ess</p>
                    <div class="time">16 Hours Ago</div>
                  </div>
                </a>
                <a href="#" class="dropdown-item">
                  <div class="dropdown-item-avatar">
                    <img alt="image" src="assets/img/avatar/avatar-5.png" class="rounded-circle">
                  </div>
                  <div class="dropdown-item-desc">
                    <b>Alfa Zulkarnain</b>
                    <p>Exercitation ullamco laboris nisi ut aliquip ex ea commodo</p>
                    <div class="time">Yesterday</div>
                  </div>
                </a>
                  </div>
                  <div class="dropdown-footer text-center">
                    <a href="#">View All <i class="fas fa-chevron-right"></i></a>
                  </div>
            </li>
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
