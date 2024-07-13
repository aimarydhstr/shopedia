@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <!-- Search form -->
                <form action="{{ route('shops.search') }}" method="GET">
                    <div class="input-group mb-3">
                        <input type="text" name="query" class="form-control" placeholder="Search products">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">Search</button>
                        </div>
                    </div>
                </form>

                <!-- Category list -->
                <div class="list-group">
                    <a class="list-group-item list-group-item-action fw-bold">Daftar Kategori</a>
                    @foreach ($categories as $category)
                        <a href="{{ route('shops.category', $category->slug) }}" class="list-group-item list-group-item-action">{{ $category->name }}</a>
                    @endforeach
                </div>
            </div>
            <div class="col-md-8">
                <h2>Search Results</h2>
                @if ($query)
                    <p>Search results for: <strong>{{ $query }}</strong></p>
                @else
                    <p>No search query provided</p>
                @endif

                <div class="row">
                    @foreach ($products as $product)
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <img src="{{ asset('images/products/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" height="200">
                                <div class="card-body">
                                    <a href="{{ route('shops.category', $product->category->slug) }}" class="card-text text-decoration-none fw-bold text-uppercase" style="font-size: 11px">{{ $product->category->name }}</a>
                                    <h5 class="card-title"><a href="{{ route('shops.product', $product->slug) }}" class="text-decoration-none text-dark">{{ $product->name }}</a></h5>
                                    @if($member_check)
                                    <p class="card-text" style="font-size: 14px">
                                        <del>{{ 'Rp ' . number_format($product->member_price, 0, ',', '.') }}</del> 
                                        <b>{{ 'Rp ' . number_format($product->member_price - ($product->member_price * ($product->discount / 100)), 0, ',', '.') }}</b>
                                    </p>
                                    @else
                                    <p class="card-text" style="font-size: 14px">
                                        <del>{{ 'Rp ' . number_format($product->price, 0, ',', '.') }}</del> 
                                        <b>{{ 'Rp ' . number_format($product->price - ($product->price * ($product->discount / 100)), 0, ',', '.') }}</b>
                                    </p>
                                    @endif
                                    <p class="card-text" style="font-size: 14px"><span class="text-warning">★</span> {{$rate[$product->id]}} | Diskon {{$product->discount}}%</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
