@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Pembelian Membership</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('memberships.purchase') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="membership_id">Pilih Membership</label>
                                <select name="membership_id" id="membership_id" class="form-control @error('membership_id') is-invalid @enderror" required>
                                    <option value="" selected disabled>Pilih Membership</option>
                                    @foreach($memberships as $membership)
                                        <option value="{{ $membership->id }}">{{ $membership->name }} - {{ 'Rp ' . number_format($membership->price, 0, ',', '.') }}</option>
                                    @endforeach
                                </select>
                                @error('membership_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="image">Bukti Transfer</label>
                                <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" required>
                                @error('image')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary mt-3">Submit Pembelian</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
