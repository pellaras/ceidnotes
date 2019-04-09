@extends('layouts.app')

@section('content')
<section class="section">
  <div class="container">

    <div class="tile is-ancestor">
      <div class="tile is-2"></div>
      <div class="tile box is-vertical is-8">
        <h1 class="title">Register</h1>

        <form class="" method="POST" action="{{ route('register') }}">
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
              <label class="label">Password</label>
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
            <div class="field-label is-normal">
              <label class="label">Confirm Password</label>
            </div>
            <div class="field-body">
              <div class="field">
                <div class="control has-icons-left">
                  <input class="input" id="password-confirm" type="password" name="password_confirmation" placeholder="Confirm password">
                  <span class="icon is-small is-left">
                    <i class="fa fa-key"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="field is-horizontal">
            <div class="field-label">
              <!-- Left empty for spacing -->
            </div>
            <div class="field-body">
              <div class="field">
                <div class="control">
                  <button type="submit" class="button is-primary">
                    Register
                  </button>
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
