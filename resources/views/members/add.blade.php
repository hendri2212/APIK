@extends('welcome')

@section('title', 'Add Member')

@section('navbar')
    <div class="container bg-light border-bottom py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="window.location='{{ route('members.index') }}'" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Members Page</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white container" style="min-height: calc(100vh - 54px)">
        <p class="text-start py-2 mb-0 text-black fw-bold">Add New Member</p>
        <form action="{{ route('members.store') }}" method="post">
            @csrf
            <div class="mb-3">
                <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Full Name" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <input type="text" name="username" value="{{ old('username') }}" class="form-control @error('username') is-invalid @enderror" placeholder="Username" required>
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <input type="text" name="telegram_id" value="{{ old('telegram_id') }}" class="form-control @error('telegram_id') is-invalid @enderror" placeholder="Telegram ID" required>
                @error('telegram_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <input type="date" name="expired" value="{{ old('expired') }}" class="form-control @error('expired') is-invalid @enderror" required>
                @error('expired')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <input type="submit" name="submit" value="Add" class="btn btn-primary mb-2">
        </form>
    </div>
@endsection
