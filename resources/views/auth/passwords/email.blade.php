@extends('layouts.app')

@section('content')
<section class="section">
  <div class="container">

    <div class="tile is-ancestor">
      <div class="tile is-2"></div>
      <div class="tile box is-vertical is-8">
        <h1 class="title">Επαναφορά Κωδικού</h1>

        @include('alerts.success')

        <form class="" method="POST" action="{{ route('password.email') }}">
          @csrf

          <div class="field is-horizontal">
            <div class="field-label is-normal">
              <label class="label">E-Mail Address</label>
            </div>
            <div class="field-body">
              <div class="field">
                <div class="control has-icons-left">
                  <input class="input{{ $errors->has('email') ? ' is-danger' : '' }}" id="email" type="email" name="email" placeholder="Enter your e-mail address" value="{{ old('email') }}" required autofocus>
                  <span class="icon is-small is-left">
                    <i class="fa fa-envelope-o"></i>
                  </span>
                </div>
                @if ($errors->has('email'))
                <p class="help is-danger">
                  {{ $errors->first('email') }}
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
                  <button type="submit" class="button is-primary">
                    Αποστολή συνδέσμου για επαναφορά κωδικού
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
