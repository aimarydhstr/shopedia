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
                                    <th>Pembeli</th>
                                    <th>Total Harga</th>
                                    <th class="text-center">Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr valign="middle">
                                        <td>{{ ++$i }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <img src="{{ asset('images/products/'.$transaction->orders->first()->product->image) }}" class="me-2" width="50" alt="{{ $transaction->orders->first()->product->name }}">
                                                <div>
                                                    <p class="m-0">{{ $transaction->orders->first()->product->name }}</p>
                                                    <p class="m-0" style="font-size: 12px">{{ $transaction->orders->first()->qty }} barang</p>
                                                </div>
                                            </div>
                                            <p class=" m-0 mt-2" style="font-size: 12px">+{{ $transaction->orders->count() - 1 }} produk</p>
                                        </td>
                                        <td>{{ $transaction->user->name }}</td>
                                        <td>{{ 'Rp ' . number_format($transaction->total, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if($transaction->status == 'Selesai')
                                            <div class="p-1 rounded mt-3 alert alert-success" style="font-size: 12px">{{ $transaction->status }}</div>
                                            @elseif($transaction->status == 'Dibatalkan')
                                            <div class="p-1 rounded mt-3 alert alert-danger" style="font-size: 12px">{{ $transaction->status }}</div>
                                            @else
                                            <div class="p-1 rounded mt-3 alert alert-warning" style="font-size: 12px">{{ $transaction->status }}</div>
                                            @endif
                                        </td>
                                        <td>{{ date('d F Y', strtotime($transaction->created_at)) }}</td>
                                        <td>
                                            @if($transaction->status == 'Menunggu Verifikasi')
                                            <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-sm btn-success">Edit</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
