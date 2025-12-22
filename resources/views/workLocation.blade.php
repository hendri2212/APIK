@extends('welcome')

@section('title', 'Work Location')

@section('navbar')
    <div class="container bg-light border-bottom py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="history.back()" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Work Location</p>
        </div>
    </div>
@endsection

@section('content')
    @php
        $locations = data_get($data ?? null, 'lokasiKerjaMe', $data ?? []);
    @endphp

    <div class="container bg-white min-vh-100 py-3">
        <div class="rounded-4 p-3 mb-3 text-white" style="background: linear-gradient(135deg, #1f6feb 0%, #0f5132 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="mb-1 small text-white-50">Work Location</p>
                    <h2 class="h5 mb-0">Your Work Location</h2>
                </div>
                <div class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="bi bi-geo-alt-fill fs-4"></i>
                </div>
            </div>
        </div>

        @if (empty($locations))
            <div class="alert alert-warning mb-0" role="alert">
                Data lokasi kerja belum tersedia.
            </div>
        @else
            @foreach ($locations as $location)
                @php
                    $lat = data_get($location, 'latitude');
                    $lng = data_get($location, 'longitude');
                    $mapUrl = ($lat !== null && $lng !== null)
                        ? 'https://www.google.com/maps?q=' . $lat . ',' . $lng
                        : null;
                @endphp
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <p class="fw-bold mb-1">{{ e(data_get($location, 'nama')) }}</p>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-person-vcard me-1"></i>{{ e(data_get($location, 'nip')) }}
                                </p>
                            </div>
                            <span class="badge text-bg-light border">
                                <i class="bi bi-buildings me-1"></i>{{ e(data_get($location, 'kode')) }}
                            </span>
                        </div>

                        <div class="row g-2 mt-1">
                            <div class="col-12">
                                <div class="p-2 rounded-3 bg-light border">
                                    <div class="small text-muted">Instansi Induk</div>
                                    <div class="fw-semibold small">{{ e(data_get($location, 'namainstansiinduk')) }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-2 rounded-3 bg-light border">
                                    <div class="small text-muted">Instansi</div>
                                    <div class="fw-semibold small">{{ e(data_get($location, 'namainstansi')) }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded-3 bg-light border">
                                    <div class="small text-muted">Latitude</div>
                                    <div class="fw-semibold small">{{ e($lat) }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded-3 bg-light border">
                                    <div class="small text-muted">Longitude</div>
                                    <div class="fw-semibold small">{{ e($lng) }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light border">
                                    <div>
                                        <div class="small text-muted">Radius</div>
                                        <div class="fw-semibold small">{{ e(data_get($location, 'radius')) }} m</div>
                                    </div>
                                    @if ($mapUrl)
                                        <a class="btn btn-sm btn-outline-primary" href="{{ e($mapUrl) }}" target="_blank" rel="noopener">
                                            <i class="bi bi-map me-1"></i>Lihat Peta
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
