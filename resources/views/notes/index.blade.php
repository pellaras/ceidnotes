@extends('layouts.app')

@section('content')
<section class="section">
    <div class="container">

        <table class="table is-bordered is-striped is-narrow is-fullwidth is-hoverable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($files as $file)
                <tr>
                    <td>{{ $file->name }}</td>

                    <td>
                        <a class="button is-small is-primary" href="{{ route('notes.show', $file->path) }}">
                            Visit
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</section>
@endsection
