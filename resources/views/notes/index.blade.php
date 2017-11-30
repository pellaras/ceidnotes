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

                @foreach($semesters ?? [] as $semester)
                <tr>
                    <td>{{ $semester->name }}</td>

                    <td>
                        <a class="button is-small is-primary" href="{{ route('notes.show', $semester->id) }}">
                            Visit
                        </a>
                    </td>
                </tr>
                @endforeach

                @foreach($lessons ?? [] as $lesson)
                <tr>
                    <td>{{ $lesson->name }}</td>

                    <td>
                        <a class="button is-small is-primary" href="{{ route('notes.show', $lesson->directory->path) }}">
                            Open
                        </a>
                    </td>
                </tr>
                @endforeach

                @foreach($directories ?? [] as $directory)
                <tr>
                    <td>{{ $directory->name }}</td>

                    <td>
                        <a class="button is-small is-primary" href="{{ route('notes.show', $directory->path) }}">
                            Browse
                        </a>
                    </td>
                </tr>
                @endforeach

                @foreach($files ?? [] as $file)
                <tr>
                    <td>{{ $file->name }}</td>

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
</section>
@endsection
