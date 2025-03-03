@extends('welcome')
 
@section('title', 'Dashboard')
 
@section('content')
    <div class="collapse" id="navbarToggleExternalContent" data-bs-theme="dark">
        <div class="bg-dark p-4">
            <a href="http://" class="btn btn-outline-danger">Profile</a>
            <a href="http://" class="btn btn-outline-danger">Setting</a>
            <a href="/logout" class="btn btn-outline-danger">Log Out</a>
        </div>
    </div>
    <div class="container bg-black text-white text-center pt-2">
        <div class="rounded-circle border border-white w-100 h-auto d-flex flex-column justify-content-center align-items-center" style="aspect-ratio: 1/1;">
            <a href="" class="text-reset text-decoration-none" data-bs-toggle="collapse" data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
                <img src="https://simpeg.kalselprov.go.id/document/images/199108022024211014.png" alt="" class="w-25 h-auto border border-white rounded-circle mb-2" style="aspect-ratio: 1/1;">
                <p class="fw-bold text-uppercase text-white mb-0">{{ session('full_name') }}</p>
            </a>
            <h1 id="realtime-clock" class="fw-bold mb-0" style="font-size: 70px"></h1>
            <p id="realtime-date" class="mb-0 px-2"></p>
        </div>
    </div>
    <div class="container bg-black text-white text-center pt-4" style="min-height: calc(100vh - 409px)">
        <div class="d-flex flex-column align-items-center justify-content-center gap-2 h-100">
            <div class="col-3">
                <a href="/history/absent" class="bg-black-subtle border border-white d-flex flex-column justify-content-center align-items-center text-center rounded-circle text-reset text-decoration-none w-100 h-auto" style="aspect-ratio: 1/1;">
                    <i class="bi bi-clock-history fs-3"></i>
                    <span class="small">History</span>
                </a>
            </div>
            {{-- <div class="col-3">
                <a href="/profile" class="bg-success-subtle d-flex flex-column py-2 rounded-2 text-reset text-decoration-none shadow">
                    <i class="bi bi-person fs-3"></i>
                    <span class="small">Profile</span>
                </a>
            </div>
            <div class="col-3">
                <a href="/workplace" class="bg-success-subtle d-flex flex-column py-2 rounded-2 text-reset text-decoration-none shadow">
                    <i class="bi bi-buildings fs-3"></i>
                    <span class="small">Work Place</span>
                </a>
            </div> --}}
            <div class="col-3">
                <a href="/face" class="bg-black-subtle border border-white d-flex flex-column justify-content-center align-items-center text-center rounded-circle text-reset text-decoration-none w-100 h-auto" style="aspect-ratio: 1/1;">
                    <i class="bi bi-fingerprint fs-3"></i>
                    <span class="small">Face Image</span>
                </a>
            </div>
            {{-- <div class="col-3">
                <div class="bg-secondary-subtle text-muted bg-opacity-10 d-flex flex-column py-2 rounded-2 shadow">
                    <i class="bi bi-filetype-pdf fs-3"></i>
                    <span class="small">Face Check</span>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-secondary-subtle text-muted bg-opacity-10 d-flex flex-column py-2 rounded-2 shadow">
                    <i class="bi bi-book fs-3"></i>
                    <span class="small">Performance</span>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-secondary-subtle text-muted bg-opacity-10 d-flex flex-column py-2 rounded-2 shadow">
                    <i class="bi bi-mortarboard fs-3"></i>
                    <span class="small">Tracer</span>
                </div>
            </div>
            <div class="col-3">
                <RouterLink :to="{ name: 'punish' }" class="bg-success-subtle d-flex flex-column py-2 rounded-2 text-reset text-decoration-none shadow">
                    <i class="bi bi-hammer fs-3"></i>
                    <span class="small">Punishment</span>
                </RouterLink>
            </div> --}}
        </div>
        
        <a href="/presence" class="btn btn-lg btn-dark rounded-0 border-0 border-top border border-white fixed-bottom">
            <i class="bi bi-door-open-fill mx-2"></i>
            Presence
        </a>
    </div>
@endsection