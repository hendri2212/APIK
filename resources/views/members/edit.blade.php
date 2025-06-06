@extends('welcome')

@section('title', 'Members List')

@section('navbar')
    <div class="container bg-light border-bottom py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="history.back()" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Members Page</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white container" style="min-height: calc(100vh - 54px)">
        <p class="text-start py-2 mb-0 text-black fw-bold">Edit Data Member</p>
        <form action="/members/{{ $member->id }}" method="post">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <input type="text" name="name" value="{{ old('name', $member->name) }}" class="form-control @error('name') is-invalid @enderror" placeholder="Nama Lengkap" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <input type="text" name="username" value="{{ old('username', $member->username) }}" class="form-control @error('username') is-invalid @enderror" placeholder="Username" required>
                @error('username')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password (kosongkan jika tidak ingin mengubah)">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <input type="text" name="uuid" value="{{ old('uuid', $member->uuid) }}" class="form-control @error('uuid') is-invalid @enderror" placeholder="UUID" required>
                @error('uuid')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <input type="text" name="telegram_id" value="{{ old('telegram_id', $member->telegram_id) }}" class="form-control @error('telegram_id') is-invalid @enderror" placeholder="ID Telegram" required>
                @error('telegram_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <input type="date" name="expired" value="{{ old('expired', $member->expired->format('Y-m-d')) }}" class="form-control @error('expired') is-invalid @enderror" required>
                @error('expired')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <input type="submit" name="submit" value="Update" class="btn btn-primary mb-2">
        </form>
    </div>
@endsection