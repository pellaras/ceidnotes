@extends('layouts.app')

@section('content')
<section class="section">
  <div class="container">

    <div class="tile is-ancestor">
      <div class="tile is-2"></div>
      <div class="tile box is-vertical is-8">
        <h1 class="title">Σύνδεση</h1>

        <form class="" method="POST" action="{{ route('login') }}">
          {{ csrf_field() }}

          <div class="field is-horizontal">
            <div class="field-label is-normal">
              <label class="label">Username</label>
            </div>
            <div class="field-body">
              <div class="field">
                <div class="control has-icons-left">
                  <input class="input{{ $errors->has('username') ? ' is-danger' : '' }}" id="username" type="text" name="username" placeholder="Enter your username"
                    value="{{ old('username') }}" autofocus>
                  <span class="icon is-small is-left">
                    <i class="fa fa-user"></i>
                  </span>
                </div>
                @if ($errors->has('username'))
                <p class="help is-danger">
                  {{ $errors->first('username') }}
                </p>
                @endif
              </div>
            </div>
          </div>

          <div class="field is-horizontal">
            <div class="field-label is-normal">
              <label class="label">Κωδικός</label>
            </div>
            <div class="field-body">
              <div class="field">
                <div class="control has-icons-left">
                  <input class="input{{ $errors->has('password') ? ' is-danger' : '' }}" id="password" type="password" name="password" placeholder="Enter a password">
                  <span class="icon is-small is-left">
                    <i class="fa fa-key"></i>
                  </span>
                </div>
                @if ($errors->has('password'))
                <p class="help is-danger">
                  {{ $errors->first('password') }}
                </p>
                @endif
              </div>
            </div>
          </div>

          <div class="field is-horizontal">
            <div class="field-label">
              {{-- Left empty for spacing --}}
            </div>
            <div class="field-body">
              <div class="field">
                <div class="control">
                  <label class="checkbox">
                    <input type="checkbox" name="remember" {{ old( 'remember') ? 'checked' : '' }}>
                    Να με θυμάσαι
                  </label>
                </div>
              </div>
            </div>
          </div>

          <div class="field is-horizontal">
            <div class="field-label">
              <!-- Left empty for spacing -->
            </div>
            <div class="field-body">
              <div class="field is-grouped">
                <div class="control">
                  <button type="submit" class="button is-primary">
                    Σύνδεση
                  </button>
                </div>
                <div class="control">
                  <a class="button is-white" href="{{ route('password.request') }}">
                    Ξέχασες τον κωδικό σου;
                  </a>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection
