@extends('layouts.app')

@section('content')
<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-two-thirds">
                <div class="box">
                    <h1 class="title">Dashboard</h1>

                    @if (session('status'))
                        <div class="notification is-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>You are logged in!</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
