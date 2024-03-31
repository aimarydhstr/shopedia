@extends('layouts.app')
@section('content')

<div class="container my-5">
    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('failed'))
        <div class="alert alert-danger" role="alert">
            {{ session('failed') }}
        </div>
    @endif
    <h2>Selamat Datang, {{ $auth->name }}</h2>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
    <br><br>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Mollitia vel error voluptatum quasi ea laboriosam consequatur atque natus officia? Accusantium provident commodi blanditiis? Cum laborum fuga perspiciatis maiores? Earum, ducimus.</p>
</div>

@endsection