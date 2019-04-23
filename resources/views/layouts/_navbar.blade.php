<nav class="navbar is-info">
  <div class="container">
    <div class="navbar-brand">
      @if(! isset($hideLogo))
        <a class="navbar-item" href="{{ url('/') }}">
          CEIDNOTES.NET
        </a>
      @endif
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
        <a class="navbar-item" href="{{ route('semesters.index') }}">
          Σημειώσεις
        </a>
        @auth
        <div class="navbar-item has-dropdown is-hoverable">
          <a class="navbar-link" href="#">
            {{ Auth::user()->name }}
          </a>
          <div class="navbar-dropdown is-boxed">
            {{-- <a class="navbar-item" href="#">
              Settings
            </a> --}}

            <hr class="navbar-divider">

            <a class="navbar-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
              Αποσύνδεση
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
            </form>
          </div>
        </div>
        @else
        <div class="navbar-item">
          <a class="button is-white is-outlined" href="{{ route('login') }}">
            <span class="icon">
              <i class="fa fa-sign-in"></i>
            </span>
            <span>Σύνδεση</span>
          </a>
        </div>
        <div class="navbar-item">
          <a class="button is-primary" href="{{ route('register.initiate') }}">
            <span class="icon">
              <i class="fa fa-user"></i>
            </span>
            <span>Εγγραφή</span>
          </a>
        </div>
        @endauth
      </div>
    </div>
  </div>
</nav>
