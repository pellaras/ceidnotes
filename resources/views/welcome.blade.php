@extends('layouts.app', ['hideNav' => 'true'])

@section('content')
<section class="hero is-large is-info is-bold">
      @include('layouts._navbar', ['hideLogo' => 'true'])
  <div class="hero-body">
    <div class="container">
      <h1 class="title">
        CEIDNOTES.NET
      </h1>
      <h2 class="subtitle">
        Σημειώσεις για τα μαθήματα της σχολής
      </h2>
    </div>
  </div>
</section>
@endsection
