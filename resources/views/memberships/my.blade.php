@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Histori Membership</div>
                    
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if ($memberships->isEmpty())
                            <p>Tidak ada transaksi membership.</p>
                        @else
                            <p>Membership Aktif Sampai <b>{{ date('d F Y', strtotime($member->end_date)) }}</b></p>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Membership</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th>Aktif</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($memberships as $membership)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $membership->membership->name }}</td>
                                            <td>{{ 'Rp ' . number_format($membership->membership->price, 0, ',', '.') }}</td>
                                            <td>{{ $membership->status }}</td>
                                            <td>{{ $membership->end_date }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
