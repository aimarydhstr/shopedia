@extends('layouts.app')

@section('content')
    <style>
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
        }

        .rating > input {
            display: none;
        }

        .rating > label {
            position: relative;
            width: 1em;
            font-size: 3vw;
            color: #FFD700;
            cursor: pointer;
        }

        .rating > label::before {
            content: "★ ";
            position: absolute;
            opacity: 0.4;
        }

        .rating > label:hover::before,
        .rating > label:hover ~ label::before {
            opacity: 1 !important;
        }

        .rating > input:checked ~ label::before {
            opacity: 1;
        }

        .rating:hover > input:checked ~ label::before {
            opacity: 0.4;
        }
    </style>

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
                        <img src="{{ asset('images/products/' . $product->image) }}" class="mb-4 d-block" height="300" alt="{{ $product->name }}">
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
                        <p class="card-text mb-5" style="font-size: 14px"><span class="text-warning">★</span> {{$rate}} | Diskon {{$product->discount}}%</p>
                        <p><b>Deskripsi Produk :</b></p>
                        <p class="card-text">{{ $product->description }}</p>
                        <form action="{{ route('carts.store', $product->slug) }}" method="post" class="d-flex justify-content-end">
                            @csrf
                            <button type="submit" class="btn btn-primary mt-3">Add to Cart</button>
                        </form>
                    </div>
                    <hr>
                    <div class="p-4">
                        
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($auth && $reviews->where('user_id', '==', $auth->id)->count() == 0 && $auth->role == 'User' && $order)
                        <form action="{{ route('reviews.store', $product->slug) }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <h4 style="margin: 0 0 0 35px" class="text-center">RATE THIS</h4>
                            <div class="rating mb-5 text-center pb-5">
                                <input type="radio" id="star5" name="rate" value="5" /><label for="star5" title="5 stars"></label>
                                <input type="radio" id="star4" name="rate" value="4" /><label for="star4" title="4 stars"></label>
                                <input type="radio" id="star3" name="rate" value="3" /><label for="star3" title="3 stars"></label>
                                <input type="radio" id="star2" name="rate" value="2" /><label for="star2" title="2 stars"></label>
                                <input type="radio" id="star1" name="rate" value="1" /><label for="star1" title="1 star"></label>
                            </div>
                            <div>
                                <label for="comment">Komentar</label>
                                <textarea class="form-control my-2 mb-3" name="comment" rows="3" required></textarea>
                            </div>
                            <div class="text-end">
                                <button class="btn btn-success" type="submit">Submit</button>
                            </div>
                        </form>
                        @endif

                        <h3>Reviews</h3>
                        @forelse($reviews as $review)
                            <div class="border p-3 my-3">
                                <h4 style="font-size: 18px;">{{ $review->user->name }}</h4>
                                <div style="color: #FFD700">
                                @for($i=1; $i <= $review->rate; $i++)
                                    ★
                                @endfor
                                @for($i=5; $i > $review->rate; $i--)
                                    ☆
                                @endfor
                                </div>
                                <p>{{ $review->comment }}</p>
                            </div>
                            @if($review->reply)
                            <div class="border p-3 my-3 ms-5">
                                <h4 style="font-size: 18px;">{{ $admin->name }}</h4>
                                <p>{{ $review->reply?->reply }}</p>
                            </div>
                            @endif
                            @if($auth && $review?->reply?->count() == 0 && $auth->id == $admin->id)
                                <form action="{{ route('reviews.reply', $review->id) }}" method="POST" class="ms-5">
                                    @csrf
                                    <div>
                                        <label for="reply">Balas Komentar</label>
                                        <textarea class="form-control my-2 mb-3" name="reply" rows="3" required></textarea>
                                    </div>
                                    <div class="text-end">
                                        <button class="btn btn-success" type="submit">Submit</button>
                                    </div>
                                </form>
                            @endif
                        @empty
                            <div class="border p-3 my-3 text-center bg-light">
                                Tidak ada review untuk produk ini
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
