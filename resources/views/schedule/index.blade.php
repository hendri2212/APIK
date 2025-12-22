@extends('welcome')

@section('title', 'Schedule Attendance')

@section('navbar')
    <div class="container bg-light border-bottom py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="window.location='{{ route('dashboard') }}'" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Schedule Attendance</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="container bg-white min-vh-100 py-3">
        <div class="rounded-4 p-3 mb-3 text-white" style="background: linear-gradient(135deg, #1f6feb 0%, #0f5132 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="mb-1 small text-white-50">Schedule</p>
                    <h2 class="h5 mb-0">Schedule Attendance</h2>
                </div>
                <div class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="bi bi-alarm fs-4"></i>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('schedule.update') }}" method="POST">
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
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
