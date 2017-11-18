<nav class="navbar is-info">
  <div class="container">
    <div class="navbar-brand">
      <a class="navbar-item" href="{{ url('/') }}">
        <img src="https://bulma.io/images/bulma-logo.png" alt="Bulma: a modern CSS framework based on Flexbox" width="112" height="28">
      </a>
      <div class="navbar-burger burger" data-target="navbarExampleTransparentExample">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>

    <div id="navbarExampleTransparentExample" class="navbar-menu">
      <div class="navbar-start">
      </div>

      <div class="navbar-end">
        <a class="navbar-item" href="{{ url('/') }}">
          Home
        </a>
        @auth
        <div class="navbar-item has-dropdown is-hoverable">
          <a class="navbar-link" href="#">
            {{ Auth::user()->name }}
          </a>
          <div class="navbar-dropdown is-boxed">
            <a class="navbar-item" href="#">
              Settings
            </a>

            <hr class="navbar-divider">

            <a class="navbar-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
              Logout
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              {{ csrf_field() }}
            </form>
          </div>
        </div>
        @else
        <div class="navbar-item">
          <div class="field is-grouped">
            <p class="control">
              <a class="button is-white is-outlined" href="{{ route('login') }}">
                <span class="icon">
                  <i class="fa fa-sign-in"></i>
                </span>
                <span>
                  Login
                </span>
              </a>
            </p>
            <p class="control">
              <a class="button is-primary" href="{{ route('register') }}">
                <span class="icon">
                  <i class="fa fa-user"></i>
                </span>
                <span>Register</span>
              </a>
            </p>
          </div>
        </div>
        @endauth
      </div>
    </div>
  </div>
</nav>
