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
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('images/products/' . $product->image) }}" class="mb-4 w-100" alt="{{ $product->name }}">
                        <a href="{{ route('shops.category', $product->category->slug) }}" class="card-text text-decoration-none fw-bold text-uppercase" style="font-size: 11px">{{ $product->category->name }}</a>
                        <h2 style="font-size: 22px" class="mb-3 mt-1">{{ $product->name }}</h2>
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
                        <p class="card-text mb-5" style="font-size: 14px">Diskon {{$product->discount}}%</p>
                        <p><b>Deskripsi Produk :</b></p>
                        <p class="card-text">{{ $product->description }}</p>
                        <form action="{{ route('carts.store', $product->slug) }}" method="post" class="d-flex justify-content-end">
                            @csrf
                            <button type="submit" class="btn btn-primary mt-3">Add to Cart</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
