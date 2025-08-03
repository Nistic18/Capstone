@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card card-body">
    <h2>Edit User</h2>

    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-select" required>
                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="buyer" {{ $user->role === 'buyer' ? 'selected' : '' }}>Buyer</option>
                <option value="reseller" {{ $user->role === 'reseller' ? 'selected' : '' }}>Reseller</option>
                <option value="supplier" {{ $user->role === 'supplier' ? 'selected' : '' }}>Supplier</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Update User</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
    </form>
    </div>
</div>
@endsection
