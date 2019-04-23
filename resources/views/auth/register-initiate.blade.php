@extends('layouts.app')

@section('content')
<section class="section">
  <div class="container">

    <div class="tile is-ancestor">
      <div class="tile is-2"></div>
      <div class="tile box is-vertical is-8">
        <h1 class="title">Εγγραφή</h1>

        @include('alerts.success')

        @if (!session()->get('success'))

          <article class="message is-info">
            <div class="message-body">
              Για ολοκλήρωση της εγγραφής, θα σου αποσταλεί email με περαιτέρω οδηγίες.
            </div>
          </article>

          <form class="" method="POST" action="{{ route('register.initiate') }}">
            @csrf

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
              <div class="field-label">
                <!-- Left empty for spacing -->
              </div>
              <div class="field-body">
                <div class="field">
                  <div class="control">
                    <button type="submit" class="button is-primary">
                      Αποστολή email
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        @endif
      </div>
    </div>
  </div>
</section>
@endsection
