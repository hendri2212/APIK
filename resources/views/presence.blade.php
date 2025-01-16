@php
    use Carbon\Carbon;
@endphp

@extends('welcome')

@section('title', 'Presence Page')

@section('navbar')
    <div class="container bg-warning py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="history.back()" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Absent Page</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white container">
        <p class="text-start mb-0 py-2 text-black fw-bold">Choose your action</p>
        <div class="row">
            <div class="col">
                <div class="d-grid">
                    <a href="/checkin" class="btn btn-success">Check In</a>
                </div>
            </div>
            <div class="col">
                <div class="d-grid">
                    <a href="/checkout" class="btn btn-success">Check Out</a>
                </div>
            </div>
        </div>

        <p class="text-start mt-4 mb-2 text-black fw-bold">Absent History</p>
        @foreach($data as $item)
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title">Presensi Masuk</h5>
                        <span class="badge bg-warning text-dark ms-auto fs-5">{{ $item['status_name'] }}</span>
                    </div>
                    <p class="card-text text-muted">{{ Carbon::parse($item['tanggal_masuk'])->translatedFormat('d F Y') }}</p>
                    <div class="d-flex align-items-center">
                        <img src="{{ isset($item['presensi_apik'][0]['presensi_foto_url']) ? $item['presensi_apik'][0]['presensi_foto_url'] : '-' }}" alt="{{ isset($item['presensi_apik'][0]['presensi_foto_file_name']) ? $item['presensi_apik'][0]['presensi_foto_file_name'] : '-' }}" class="rounded-4 me-3" style="width: 100px; height: 100px;">
                        <div>
                            <h1 class="display-4 text-warning mb-0">{{ isset($item['presensi_apik'][0]['presensi_time']) ? $item['presensi_apik'][0]['presensi_time'] : '-' }}</h1>
                            <p class="text-muted mb-0">Masuk: 07:15:00 - 08:00:00</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title">Presensi Pulang</h5>
                        <span class="badge bg-warning text-dark ms-auto fs-5">{{ $item['status_name'] }}</span>
                    </div>
                    <p class="card-text text-muted">{{ Carbon::parse($item['tanggal_keluar'])->translatedFormat('d F Y') }}</p>
                    <div class="d-flex align-items-center">
                        <img src="{{ isset($item['presensi_apik'][1]['presensi_foto_url']) ? $item['presensi_apik'][1]['presensi_foto_url'] : '-' }}" alt="{{ isset($item['presensi_apik'][1]['presensi_foto_file_name']) ? $item['presensi_apik'][1]['presensi_foto_file_name'] : '-' }}" class="rounded-4 me-3" style="width: 100px; height: 100px;">
                        <div>
                            <h1 class="display-4 text-warning mb-0">{{ isset($item['presensi_apik'][1]['presensi_time']) ? $item['presensi_apik'][1]['presensi_time'] : '-' }}</h1>
                            <p class="text-muted mb-0">Pulang: 16:00:00 - 23:59:00</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection