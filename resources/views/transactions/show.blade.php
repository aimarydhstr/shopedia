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
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr valign="middle">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('images/products/'.$order->product->image) }}" class="me-2" width="50" alt="{{ $order->product->name }}">
                                                <div>
                                                    <p class="m-0">{{ $order->product->name }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        @if($member_check)
                                        <td>{{ 'Rp ' . number_format($order->product->member_price - ($order->product->member_price * ($order->product->discount / 100)), 0, ',', '.') }}</td>
                                        @else
                                        <td>{{ 'Rp ' . number_format($order->product->price - ($order->product->price * ($order->product->discount / 100)), 0, ',', '.') }}</td>
                                        @endif
                                        <td>{{ $order->qty }} barang</td>
                                        @if($member_check)
                                        <td>{{ 'Rp ' . number_format($order->qty * ($order->product->member_price - ($order->product->member_price * ($order->product->discount / 100))), 0, ',', '.') }}</td>
                                        @else
                                        <td>{{ 'Rp ' . number_format($order->qty * ($order->product->price - ($order->product->price * ($order->product->discount / 100))), 0, ',', '.') }}</td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">No items in the order</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between my-4 col-md-8 mx-auto">
                            <div>
                                <p>Subtotal</p>
                                <p>Pajak</p>
                                <p>Total Semua</p>
                            </div>
                            <div>
                                <p><b>{{ 'Rp' . number_format($total, 0, ',', '.') }}</b></p>
                                <p><b>{{ 'Rp' . number_format(2500, 0, ',', '.') }}</b></p>
                                <p><b>{{ 'Rp' . number_format($total + 2500, 0, ',', '.') }}</b></p>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-start">
                            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
