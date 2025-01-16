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
            <div>
                <div class="alert alert-light" role="alert">
                    <div class="row">
                        <div class="col">
                            <div class="d-flex flex-column">
                                <label>Check In</label>
                                {{ Carbon::parse($item['tanggal_masuk'])->translatedFormat('d F Y') }}
                                <img src="{{ isset($item['presensi_apik'][0]['presensi_foto_url']) ? $item['presensi_apik'][0]['presensi_foto_url'] : '-' }}" alt="{{ isset($item['presensi_apik'][0]['presensi_foto_file_name']) ? $item['presensi_apik'][0]['presensi_foto_file_name'] : '-' }}" class="img-fluid rounded-4 w-25">
                            </div>
                        </div>
                        <div class="col">
                            <h1>{{ isset($item['presensi_apik'][0]['presensi_time']) ? $item['presensi_apik'][0]['presensi_time'] : '-' }}</h1>
                        </div>
                    </div>
                </div>
                <div class="alert alert-light" role="alert">
                    <div class="row">
                        <div class="col">
                            <div class="d-flex flex-column">
                                <label>Check Out</label>
                                {{ Carbon::parse($item['tanggal_keluar'])->translatedFormat('d F Y') }}
                                <img src="{{ isset($item['presensi_apik'][1]['presensi_foto_url']) ? $item['presensi_apik'][1]['presensi_foto_url'] : '-' }}" alt="{{ isset($item['presensi_apik'][1]['presensi_foto_file_name']) ? $item['presensi_apik'][1]['presensi_foto_file_name'] : '-' }}" class="img-fluid rounded-4 w-25">
                            </div>
                        </div>
                        <div class="col">
                            <h1>{{ isset($item['presensi_apik'][1]['presensi_time']) ? $item['presensi_apik'][1]['presensi_time'] : '-' }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection