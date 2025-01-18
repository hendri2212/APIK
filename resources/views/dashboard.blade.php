@extends('welcome')
 
@section('title', 'Dashboard')
 
@section('content')
    <div class="container bg-warning text-white pt-4">
        <div class="d-flex align-items-center text-success">
            <a href="/profile" class="text-reset text-decoration-none d-flex">
                <i class="bi bi-person-circle fs-1 border-white text-white"></i>
                <div class="mx-2">
                    <span class="fw-lighter small text-white">Selamat Datang,</span>
                    <p class="fw-bold text-uppercase text-white">{{ session('full_name') }}</p>
                </div>
            </a>
        </div>
        <!-- <div id="realtime-clock" style="font-size: 24px; font-weight: bold;"></div> -->
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
                <a href="/history/absent" class="bg-success-subtle d-flex flex-column py-2 rounded-2 text-reset text-decoration-none">
                    <i class="bi bi-clock-history fs-3"></i>
                    <span class="small">History</span>
                </a>
            </div>
            <div class="col-3">
                <a href="/profile" class="bg-success-subtle d-flex flex-column py-2 rounded-2 text-reset text-decoration-none">
                    <i class="bi bi-person fs-3"></i>
                    <span class="small">Profile</span>
                </a>
            </div>
            <div class="col-3">
                <a href="/work_place" class="bg-success-subtle d-flex flex-column py-2 rounded-2 text-reset text-decoration-none">
                    <i class="bi bi-buildings fs-3"></i>
                    <span class="small">Work Place</span>
                </a>
            </div>
            <div class="col-3">
                <div class="bg-secondary-subtle text-muted bg-opacity-10 d-flex flex-column py-2 rounded-2">
                    <i class="bi bi-journal-check fs-3"></i>
                    <span class="small">Journal</span>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-secondary-subtle text-muted bg-opacity-10 d-flex flex-column py-2 rounded-2">
                    <i class="bi bi-filetype-pdf fs-3"></i>
                    <span class="small">Module</span>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-secondary-subtle text-muted bg-opacity-10 d-flex flex-column py-2 rounded-2">
                    <i class="bi bi-book fs-3"></i>
                    <span class="small">Schedule</span>
                </div>
            </div>
            <div class="col-3">
                <div class="bg-secondary-subtle text-muted bg-opacity-10 d-flex flex-column py-2 rounded-2">
                    <i class="bi bi-mortarboard fs-3"></i>
                    <span class="small">Tracer</span>
                </div>
            </div>
            <div class="col-3">
                <RouterLink :to="{ name: 'punish' }" class="bg-success-subtle d-flex flex-column py-2 rounded-2 text-reset text-decoration-none">
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