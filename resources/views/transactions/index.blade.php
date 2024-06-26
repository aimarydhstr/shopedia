@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Transaksi Saya</div>

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
                                            <div class="d-flex align-items-center">
                                                <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-sm btn-primary">Detail</a>
                                                @if($transaction->status == 'Sedang Dikirim')
                                                <form method="POST" action="{{ route('transactions.done', $transaction->id) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success ms-2">Selesai</button>
                                                </form>
                                                @endif
                                            </div>
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
