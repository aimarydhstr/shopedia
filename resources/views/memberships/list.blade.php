@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Daftar Transaksi Membership</div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Membership</th>
                                    <th>Pembeli</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                    <th>Aktif</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($memberships as $membership)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $membership->membership->name }}</td>
                                        <td>{{ $membership->user->name }}</td>
                                        <td>{{ 'Rp ' . number_format($membership->membership->price, 0, ',', '.') }}</td>
                                        <td>{{ $membership->status }}</td>
                                        <td>{{ $membership->end_date }}</td>
                                        <td>
                                            @if($membership->status == 'Proses')
                                            <a href="{{ route('memberships.activation', $membership->id) }}" class="btn btn-primary">Edit</a>
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
