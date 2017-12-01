@extends('layouts.app', ['hideNav' => 'true'])

@section('content')
<section class="hero is-large is-info is-bold">
      @include('layouts._navbar', ['hideLogo' => 'true'])
  <div class="hero-body">
    <div class="container">
      <div class="tile is-parent">
        <div class="tile is-child">
            <h1 class="title">
                CEIDNOTES.NET
            </h1>
            <h2 class="subtitle">
                Σημειώσεις για τα μαθήματα της σχολής
            </h2>
        </div>
        <div class="tile is-child box content recent-files">
            <h3>Πρόσφατα αρχεία</h3>
            <table class="table is-bordered is-striped is-narrow is-fullwidth is-hoverable">
                {{--  <thead>
                    <tr>
                        <th>Name</th>
                        <th></th>
                    </tr>
                </thead>  --}}
                <tbody>

                    @foreach($files ?? [] as $file)
                    <tr>
                        <td>{{ $file->name }} <span>[{{$file->created_at->diffForHumans()}}]</span></td>

                        <td>
                            <a class="button is-small is-primary" href="{{ route('notes.show', $file->path) }}">
                                Download
                            </a>
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
