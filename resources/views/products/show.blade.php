@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Product Details</div>

                    <div class="card-body">
                        @if ($product->image)
                            <div class="mb-4">
                                <img src="{{ asset('images/products/' . $product->image) }}" alt="Product Image" style="max-width: 200px;">
                            </div>
                        @endif
                        <p><strong>Name:</strong> {{ $product->name }}</p>
                        <p><strong>Description:</strong> {{ $product->description }}</p>
                        <p><strong>Price:</strong> {{ 'Rp ' . number_format($product->price, 0, ',', '.') }}</p>
                        <p><strong>Member Price:</strong> {{ 'Rp ' . number_format($product->member_price, 0, ',', '.') }}</p>
                        <p><strong>Discount:</strong> {{ $product->discount }}%</p>
                        <p><strong>Category:</strong> {{ $product->category->name }}</p>
                        <p><strong>Stock:</strong> {{ $product->stock }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
