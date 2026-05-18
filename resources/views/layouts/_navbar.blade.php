<nav class="navbar is-info" x-data="{ open: false }">
  <div class="container">
    <div class="navbar-brand">
        @if(! isset($hideLogo))
            <a class="navbar-item" href="{{ url('/') }}">
                CEIDNOTES.NET
            </a>
        @endif
      <a role="button" class="navbar-burger burger"
         :class="{ 'is-active': open }"
         @click="open = !open"
         aria-label="menu" aria-expanded="false">
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
      </a>
    </div>

    <div class="navbar-menu" :class="{ 'is-active': open }">
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
