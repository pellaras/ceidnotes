@extends('layouts.app', ['hideNav' => 'true'])

@section('content')
<section class="hero is-large is-info is-bold">
      @include('layouts._navbar', ['hideLogo' => 'true'])
  <div class="hero-body">
    <div class="container">
      <div class="columns">
        <div class="column">
            <h1 class="title">
                {{ str(config('app.name'))->upper() }}
            </h1>
            <h2 class="subtitle">
                Σημειώσεις για τα μαθήματα της σχολής
            </h2>
        </div>
        <div class="column">
          <div class="box content recent-files">
            <h3>Πρόσφατα αρχεία</h3>
            <table class="table is-bordered is-striped is-narrow is-fullwidth is-hoverable">
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
  </div>
</section>
@endsection
