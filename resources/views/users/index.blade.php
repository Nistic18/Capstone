@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card card-body">
        <h2>Manage Users</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th><th>Email</th><th>Role</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @switch($user->role)
                            @case('admin')
                                <span class="badge bg-success text-white">Admin</span>
                                @break
                            @case('reseller')
                                <span class="badge bg-info text-dark text-white">Reseller</span>
                                @break
                            @case('supplier')
                                <span class="badge bg-primary text-white">Supplier</span>
                                @break
                            @case('buyer')
                                <span class="badge bg-secondary text-white">Buyer</span>
                                @break
                            @default
                                <span class="badge bg-light text-dark">Unknown</span>
                        @endswitch
                    </td>
                    <td>
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection
