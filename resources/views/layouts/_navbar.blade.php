<nav class="navbar is-info">
  <div class="container">
    <div class="navbar-brand">
        @if(! isset($hideLogo))
            <a class="navbar-item" href="{{ url('/') }}">
                {{ str(config('app.name'))->upper() }}
            </a>
        @endif
      <div class="navbar-burger burger" data-target="navbarExampleTransparentExample">
        <span></span>
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
          Notes
        </a>
      </div>
    </div>
  </div>
</nav>
