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
                                            @if($cart->qty > 1)
                                            <button class="btn btn-outline-secondary" type="button" id="button-minus-{{ $cart->id }}" onclick="event.preventDefault(); document.getElementById('remove-{{ $cart->id }}').submit();">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            @else
                                            <button class="btn btn-outline-secondary" type="button" disabled>
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            @endif
                                            <form id="remove-{{ $cart->id }}" action="{{ route('carts.remove', $cart->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('PUT')
                                            </form>
                                            <input type="text" class="form-control text-center" value="{{ $cart->qty }}" readonly style="width: 30px">
                                            @if($cart->qty <= $cart->product->stock)
                                            <button class="btn btn-outline-secondary" type="button" id="button-plus-{{ $cart->id }}" onclick="event.preventDefault(); document.getElementById('add-{{ $cart->id }}').submit();">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                            @else
                                            <button class="btn btn-outline-secondary" type="button" disabled>
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            @endif
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
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-success" data-toggle="modal" data-target="#paymentModal">Bayar Transaksi</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Bukti Transfer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <p>Silakan transfer ke nomor rekening berikut:</p>
                        <p>Nomor Rekening: 1234567890</p>
                        <p>Jumlah yang harus dibayarkan: {{ 'Rp ' . number_format($total, 0, ',', '.') }}</p>
                    </div>
                    <div class="form-group">
                        <label for="image">Upload Bukti TF</label>
                        <input type="file" class="form-control mt-2" id="image" name="image" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

@endsection
