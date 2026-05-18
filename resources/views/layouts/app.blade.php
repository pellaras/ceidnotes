<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>

<body>
  <div id="app">
    @if(! isset($hideNav))
      @include('layouts._navbar')
    @endif

    @yield('content')

    @include('layouts._footer')
  </div>
</body>

</html>
