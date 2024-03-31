@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ $title }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('memberships.activate', $memberships->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-group row mt-3">
                                <label for="user_name" class="col-md-4 col-form-label text-md-right">Nama Pengguna</label>

                                <div class="col-md-6">
                                    <input id="user_name" type="text" class="form-control" value="{{ $memberships->user->name }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row mt-3">
                                <label for="membership_name" class="col-md-4 col-form-label text-md-right">Membership</label>

                                <div class="col-md-6">
                                    <input id="membership_name" type="text" class="form-control" value="{{ $memberships->membership->name }}" readonly>
                                </div>
                            </div>

                            <div class="form-group row mt-3">
                                <label for="image" class="col-md-4 col-form-label text-md-right">Bukti Transfer</label>

                                <div class="col-md-6">
                                    @if ($memberships->image)
                                        <img src="{{ asset('image/memberships/' . $memberships->image) }}" alt="Bukti Transfer" style="max-width: 200px;">
                                    @else
                                        Tidak ada bukti transfer
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row mt-3">
                                <label for="status" class="col-md-4 col-form-label text-md-right">Status</label>

                                <div class="col-md-6">
                                    <select name="status" id="status" class="form-control">
                                        <option value="" selected disabled>Pilih Status</option>
                                        <option value="Proses" {{ $memberships->status == 'Proses' ? 'selected' : '' }}>Proses</option>
                                        <option value="Selesai" {{ $memberships->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="Dibatalkan" {{ $memberships->status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mt-3 mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
