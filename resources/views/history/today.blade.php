@php
    use Carbon\Carbon;
@endphp

@extends('welcome')

@section('title', 'Presence Page')

@section('navbar')
    <div class="container bg-light border-bottom py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="history.back()" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Absent Page</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white container" style="min-height: calc(100vh - 51px)">
        <p class="text-start py-2 text-black fw-bold">Absent History</p>
        @if(isset($error))
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endif
        @foreach($data as $item)
            <div style="height: 80vh">
                <div class="alert alert-light mb-4 shadow-sm p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title fw-bold mb-0">Check In</h5>
                        <span class="rounded-pill bg-warning text-white fw-bold p-2">{{ $item['jam_masuk_status'] }}</span>
                    </div>
                    <p class="card-text text-muted">{{ Carbon::parse($item['tanggal_masuk'])->translatedFormat('d F Y') }}</p>
                    <div class="d-flex align-items-center">
                        @if ($item['presensi_foto_masuk'])
                        <div class="rounded-4 overflow-hidden" style="width: 100px; height: 100px;">
                            <!-- <img src="{{ isset($item['presensi_apik'][0]['presensi_foto_url']) ? $item['presensi_apik'][0]['presensi_foto_url'] : '-' }}" alt="{{ isset($item['presensi_apik'][0]['presensi_foto_file_name']) ? $item['presensi_apik'][0]['presensi_foto_file_name'] : '-' }}" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;"> -->
                            <img src="{{ $item['presensi_foto_masuk'] }}" alt="Foto Absen Masuk" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                        </div>
                        @else
                        <div class="rounded-4 overflow-hidden" style="width: 100px; height: 100px;">
                            <img src="{{ asset('icon/icon-192x192.png') }}" alt="Logo Shortcut Point" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                        </div>
                        @endif
                        <div class="mx-3">
                            <h1 class="display-4 text-warning mb-0 fw-bold">{{ isset($item['jam_masuk']) ? $item['jam_masuk'] : '-' }}</h1>
                            <p class="text-muted mb-0">Masuk : {{ $item['jam_mulai_absen_pagi'] }} - {{ $item['jam_mulai_kerja'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="alert alert-light mb-4 shadow-sm p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title fw-bold mb-0">Check Out</h5>
                        <span class="rounded-pill bg-warning text-white fw-bold p-2">{{ $item['jam_keluar_status'] }}</span>
                    </div>
                    <p class="card-text text-muted">{{ Carbon::parse($item['tanggal_keluar'])->translatedFormat('d F Y') }}</p>
                    <div class="d-flex align-items-center">
                        @if ($item['presensi_foto_keluar'])
                        <div class="rounded-4 overflow-hidden" style="width: 100px; height: 100px;">
                            <!-- <img src="{{ isset($item['presensi_apik'][1]['presensi_foto_url']) ? $item['presensi_apik'][1]['presensi_foto_url'] : '-' }}" alt="{{ isset($item['presensi_apik'][1]['presensi_foto_file_name']) ? $item['presensi_apik'][1]['presensi_foto_file_name'] : '-' }}" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;"> -->
                            <img src="{{ $item['presensi_foto_keluar'] }}" alt="Foto Absen Keluar" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                        </div>
                        @else
                        <div class="rounded-4 overflow-hidden" style="width: 100px; height: 100px;">
                            <img src="{{ asset('icon/icon-192x192.png') }}" alt="Logo Shortcut Point" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                        </div>
                        @endif
                        <div class="mx-3">
                            <h1 class="display-4 text-warning mb-0 fw-bold">{{ isset($item['jam_keluar']) ? $item['jam_keluar'] : '-' }}</h1>
                            <p class="text-muted mb-0">Pulang : {{ $item['jam_pulang_kerja'] }} - {{ $item['jam_mulai_absen_pulang'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection