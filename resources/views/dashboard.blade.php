@extends('welcome')
 
@section('title', 'Dashboard')
 
@section('content')
    <div class="collapse" id="navbarToggleExternalContent" data-bs-theme="dark">
        <div class="bg-dark p-4">
            <a href="http://" class="btn btn-outline-warning">Profile</a>
            <a href="http://" class="btn btn-outline-warning">Setting</a>
            <a href="/logout" class="btn btn-outline-warning">Log Out</a>
            <!-- <h5 class="text-body-emphasis h4">Collapsed content</h5>
            <span class="text-body-secondary">Toggleable via the navbar brand.</span> -->
        </div>
    </div>
    <div class="container bg-warning text-white pt-4">
        <div class="d-flex align-items-center text-success">
            <a href="" class="text-reset text-decoration-none d-flex" data-bs-toggle="collapse" data-bs-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-person-circle fs-1 border-white text-white"></i>
                <div class="mx-2">
                    <span class="fw-lighter small text-white">Selamat Datang,</span>
                    <p class="fw-bold text-uppercase text-white mb-0">{{ session('full_name') }}</p>
                </div>
            </a>
        </div>
        <h1 id="realtime-clock" class="fw-bold mb-0" style="font-size: 70px"></h1>
        <p id="realtime-date" class="mb-0 px-2"></p>
        <div class="d-grid py-4">
            <a href="/presence" class="btn btn-lg rounded-4 btn-success border-white py-3">
                <i class="bi bi-door-open-fill mx-2"></i>
                Presence
            </a>
        </div>
    </div>
    <div class="bg-white container text-center text-success" style="height: 70vh">
        <div class="row g-2">
            <div class="col-3">
                <a href="/history/absent" class="bg-success-subtle d-flex flex-column py-2 rounded-2 text-reset text-decoration-none shadow">
                    <i class="bi bi-clock-history fs-3"></i>
                    <span class="small">History</span>
                </a>
            </div>
            <div class="col-3">
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
            </div>
            <div class="col-3">
                <a href="/face" class="bg-success-subtle d-flex flex-column py-2 rounded-2 text-reset text-decoration-none shadow">
                    <i class="bi bi-fingerprint fs-3"></i>
                    <span class="small">Face Image</span>
                </a>
            </div>
            <div class="col-3">
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
            </div>
        </div>
        <p class="text-start mt-4 mb-2 text-black fw-bold">Learning Activities</p>
        <div id="carouselExampleSlidesOnly" class="carousel slide pb-4" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="https://media.wired.com/photos/647e7400d96882f74caa3e5c/master/w_2240,c_limit/Don't-Want-Students-To-Rely-On-ChatGPT-Ideas-1356557660.jpg" class="d-block w-100 rounded-4" style="height: 25vh" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="https://www.uopeople.edu/wp-content/uploads/2022/05/sam-balye-w1FwDvIreZU-unsplash-scaled.jpg.webp" class="d-block w-100 rounded-4" style="height: 25vh" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="https://image.cnbcfm.com/api/v1/image/106918576-1627532474886-gettyimages-871203832-pi-1589476.jpeg" class="d-block w-100 rounded-4" style="height: 25vh" alt="...">
                </div>
            </div>
        </div>
    </div>
@endsection