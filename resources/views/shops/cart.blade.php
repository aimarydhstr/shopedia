@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $title }}</div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($carts as $cart)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $cart->product->name }}</td>
                                        @if($member_check)
                                        <td>{{ 'Rp ' . number_format($cart->product->member_price - ($cart->product->member_price * ($cart->product->discount / 100)), 0, ',', '.') }}</td>
                                        @else
                                        <td>{{ 'Rp ' . number_format($cart->product->price - ($cart->product->price * ($cart->product->discount / 100)), 0, ',', '.') }}</td>
                                        @endif
                                        <td>
                                        <div class="input-group">
                                            <button class="btn btn-outline-secondary" type="button" id="button-minus-{{ $cart->id }}" onclick="event.preventDefault(); document.getElementById('remove-{{ $cart->id }}').submit();">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <form id="remove-{{ $cart->id }}" action="{{ route('carts.remove', $cart->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('PUT')
                                            </form>
                                            <input type="text" class="form-control text-center" value="{{ $cart->qty }}" readonly style="width: 30px">
                                            <button class="btn btn-outline-secondary" type="button" id="button-plus-{{ $cart->id }}" onclick="event.preventDefault(); document.getElementById('add-{{ $cart->id }}').submit();">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                            <form id="add-{{ $cart->id }}" action="{{ route('carts.add', $cart->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('PUT')
                                            </form>
                                        </div>

                                        </td>
                                        @if($member_check)
                                        <td>{{ 'Rp ' . number_format($cart->qty * ($cart->product->member_price - ($cart->product->member_price * ($cart->product->discount / 100))), 0, ',', '.') }}</td>
                                        @else
                                        <td>{{ 'Rp ' . number_format($cart->qty * ($cart->product->price - ($cart->product->price * ($cart->product->discount / 100))), 0, ',', '.') }}</td>
                                        @endif
                                        <td>
                                            <button class="btn btn-danger" type="button" onclick="event.preventDefault(); document.getElementById('remove-form-{{ $cart->id }}').submit();">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="remove-form-{{ $cart->id }}" action="{{ route('carts.destroy', $cart->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">No items in the cart</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                            <p class="mb-0">Total Semua</p>
                            <p><b>{{ 'Rp ' . number_format($total, 0, ',', '.') }}</b></p>
                            </div>
                            <a href="#" class="btn btn-success">Bayar Transaksi</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
