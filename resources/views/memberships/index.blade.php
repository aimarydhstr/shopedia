@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Memberships</div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="mb-3">
                            <a href="{{ route('memberships.create') }}" class="btn btn-primary">Add New</a>
                        </div>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Duration</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($memberships as $membership)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $membership->name }}</td>
                                        <td>{{ 'Rp ' . number_format($membership->price, 0, ',', '.') }}</td>
                                        <td>{{ $membership->duration }} Bulan</td>
                                        <td>
                                            <a href="{{ route('memberships.edit', $membership->id) }}" class="btn btn-secondary">Edit</a>
                                            <form action="{{ route('memberships.destroy', $membership->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this membership?')">Delete</button>
                                            </form>
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
