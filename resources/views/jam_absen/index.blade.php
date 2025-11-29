@extends('welcome')

@section('title', 'Atur Jam Absen')

@section('navbar')
    <div class="container bg-light border-bottom py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="window.location='{{ route('dashboard') }}'" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Atur Jam Absen</p>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white container" style="min-height: 100vh">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- <div class="card border-0 shadow-sm">
        <div class="card-body"> -->
            <form action="{{ route('jam-absen.update') }}" method="POST" class="pt-2">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="checkin_time" class="form-label">Jam Masuk (Check In)</label>
                    <input type="time" class="form-control" id="checkin_time" name="checkin_time" value="{{ $jamAbsen->checkin_time ?? '' }}" required>
                </div>

                <div class="mb-3">
                    <label for="checkout_time" class="form-label">Jam Pulang (Check Out)</label>
                    <input type="time" class="form-control" id="checkout_time" name="checkout_time" value="{{ $jamAbsen->checkout_time ?? '' }}" required>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        <!-- </div>
    </div> -->
</div>
@endsection
