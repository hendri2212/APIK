@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
@endphp

@extends('../welcome')
 
@section('title', 'Absent History')

@section('navbar')
    <div class="container bg-warning py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="history.back()" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Presence History</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white container text-success pb-2">
        <form method="GET" action="{{ url('/history/absent') }}" class="py-2">
            <div class="mb-3">
                <label for="tanggal" class="form-label text-black fw-bold">Select a Month</label>
                <input type="month" id="tanggal" name="tanggal" class="form-control" value="{{ $tanggal }}">
            </div>
            <button type="submit" class="btn btn-primary">Show Data</button>
        </form>
        @if(isset($error))
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endif
        @foreach($data as $item)
        <div class="card mb-2 shadow-sm">
            <div class="card-body p-2">
                <div class="d-flex justify-content-between">
                    <div class="col-6">
                        <h5 class="card-title fw-bold mb-0">Check In</h5>
                        <p class="card-text text-muted mb-0 border-bottom">{{ Carbon::parse($item['tanggal_masuk'])->translatedFormat('l, d M Y') }}</p>
                        <div class="d-flex align-items-center mt-2">
                            @if (isset($item['presensi_apik'][0]['presensi_foto_url']))
                                <div class="rounded-4 overflow-hidden" style="width: 100px; height: 100px;">
                                    <img src="{{ isset($item['presensi_apik'][0]['presensi_foto_url']) ? $item['presensi_apik'][0]['presensi_foto_url'] : '-' }}" alt="{{ isset($item['presensi_apik'][0]['presensi_foto_file_name']) ? $item['presensi_apik'][0]['presensi_foto_file_name'] : '-' }}" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                                    {{-- <img src="{{ isset($item['presensi_apik'][0]['presensi_foto_url']) }}" alt="Foto Absen Masuk" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;"> --}}
                                </div>
                            @else
                                <div class="rounded-4 overflow-hidden" style="width: 100px; height: 100px;">
                                    <img src="{{ asset('icon/icon-192x192.png') }}" alt="Logo Shortcut Point" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                                </div>
                            @endif
                            <div class="mx-1">
                                <span class="text-success fw-bold">{{ $item['jam_masuk_status'] }}</span>
                                <h5 class="text-warning mb-0 fw-bold">{{ isset($item['jam_masuk']) ? $item['jam_masuk'] : '-' }}</h>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="card-title fw-bold mb-0">Check Out</h5>
                        <p class="card-text text-muted mb-0 border-bottom">{{ Carbon::parse($item['tanggal_masuk'])->translatedFormat('l, d M Y') }}</p>
                        <div class="d-flex align-items-center mt-2">
                            @if (isset($item['presensi_apik'][0]['presensi_foto_url']))
                                <div class="rounded-4 overflow-hidden" style="width: 100px; height: 100px;">
                                    <img src="{{ isset($item['presensi_apik'][1]['presensi_foto_url']) ? $item['presensi_apik'][1]['presensi_foto_url'] : '-' }}" alt="{{ isset($item['presensi_apik'][1]['presensi_foto_file_name']) ? $item['presensi_apik'][1]['presensi_foto_file_name'] : '-' }}" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                                </div>
                            @else
                                <div class="rounded-4 overflow-hidden" style="width: 100px; height: 100px;">
                                    <img src="{{ asset('icon/icon-192x192.png') }}" alt="Logo Shortcut Point" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                                </div>
                            @endif
                            <div class="mx-1">
                                <span class="text-success fw-bold">{{ $item['jam_keluar_status'] }}</span>
                                <h5 class="text-warning mb-0 fw-bold">{{ isset($item['jam_keluar']) ? $item['jam_keluar'] : '-' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        {{-- <div class="table-responsive">
            <table class="table table-bordered table-sm text-nowrap table-hover table-striped">
                <thead>
                    <tr>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal Keluar</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status Code</th>
                        <th>Status</th>
                        <th>NIP</th>
                        <th>Jam Mulai Absen Pagi</th>
                        <th>Jam Mulai Absen Pulang</th>
                        <th>Jenis Jadwal</th>
                        <th>Jam Mulai Kerja</th>
                        <th>Jam Pulang Kerja</th>
                        <th>Lewat Hari</th>
                        <th>Id</th>
                        <th>Jam Keluar Status</th>
                        <th>Jam Masuk Status</th>
                        <th>Presensi Id</th>
                        <th>Employee NIP</th>
                        <th>Presensi Tipe</th>
                        <th>presensi_date</th>
                        <th>presensi_time</th>
                        <th>presensi_lat</th>
                        <th>presensi_long</th>
                        <th>presensi_status</th>
                        <th>presensi_foto_url</th>
                        <th>presensi_foto_file_name</th>
                        <th>presensi_sync_eabsen</th>
                        <th>presensi_sync_eabsen_id</th>
                        <th>__typename</th>
                        <th>Presensi Id</th>
                        <th>Employee NIP</th>
                        <th>Presensi Tipe</th>
                        <th>presensi_date</th>
                        <th>presensi_time</th>
                        <th>presensi_lat</th>
                        <th>presensi_long</th>
                        <th>presensi_status</th>
                        <th>presensi_foto_url</th>
                        <th>presensi_foto_file_name</th>
                        <th>presensi_sync_eabsen</th>
                        <th>presensi_sync_eabsen_id</th>
                        <th>__typename</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $item)
                        <tr>
                            <td>{{ Carbon::parse($item['tanggal_masuk'])->translatedFormat('d F Y') }}</td>
                            <td>{{ Carbon::parse($item['tanggal_keluar'])->translatedFormat('d F Y') }}</td>
                            <td>{{ $item['jam_masuk'] }}</td>
                            <td>{{ $item['jam_keluar'] }}</td>
                            <td>{{ $item['status_code'] }}</td>
                            <td>{{ $item['status_name'] }}</td>
                            <td>{{ $item['nip'] }}</td>
                            <td>{{ $item['jam_mulai_absen_pagi'] }}</td>
                            <td>{{ $item['jam_mulai_absen_pulang'] }}</td>
                            <td>{{ $item['jenis_jadwal'] }}</td>
                            <td>{{ $item['jam_mulai_kerja'] }}</td>
                            <td>{{ $item['jam_pulang_kerja'] }}</td>
                            <td>{{ $item['lewathari'] }}</td>
                            <td>{{ $item['id'] }}</td>
                            <td>{{ $item['jam_keluar_status'] }}</td>
                            <td>{{ $item['jam_masuk_status'] }}</td>
                            <td>{{ isset($item['presensi_apik'][0]['presensi_id']) ? $item['presensi_apik'][0]['presensi_id'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][0]['employee_nip']) ? $item['presensi_apik'][0]['employee_nip'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][0]['presensi_tipe']) ? $item['presensi_apik'][0]['presensi_tipe'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][0]['presensi_date']) ? $item['presensi_apik'][0]['presensi_date'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][0]['presensi_time']) ? $item['presensi_apik'][0]['presensi_time'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][0]['presensi_lat']) ? $item['presensi_apik'][0]['presensi_lat'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][0]['presensi_long']) ? $item['presensi_apik'][0]['presensi_long'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][0]['presensi_status']) ? $item['presensi_apik'][0]['presensi_status'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][0]['presensi_foto_url']) ? $item['presensi_apik'][0]['presensi_foto_url'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][0]['presensi_foto_file_name']) ? $item['presensi_apik'][0]['presensi_foto_file_name'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][0]['presensi_sync_eabsen']) ? $item['presensi_apik'][0]['presensi_sync_eabsen'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][0]['presensi_sync_eabsen_id']) ? $item['presensi_apik'][0]['presensi_sync_eabsen_id'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][0]['__typename']) ? $item['presensi_apik'][0]['__typename'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][1]['presensi_id']) ? $item['presensi_apik'][1]['presensi_id'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][1]['employee_nip']) ? $item['presensi_apik'][1]['employee_nip'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][1]['presensi_tipe']) ? $item['presensi_apik'][1]['presensi_tipe'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][1]['presensi_date']) ? $item['presensi_apik'][1]['presensi_date'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][1]['presensi_time']) ? $item['presensi_apik'][1]['presensi_time'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][1]['presensi_lat']) ? $item['presensi_apik'][1]['presensi_lat'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][1]['presensi_long']) ? $item['presensi_apik'][1]['presensi_long'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][1]['presensi_status']) ? $item['presensi_apik'][1]['presensi_status'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][1]['presensi_foto_url']) ? $item['presensi_apik'][1]['presensi_foto_url'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][1]['presensi_foto_file_name']) ? $item['presensi_apik'][1]['presensi_foto_file_name'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][1]['presensi_sync_eabsen']) ? $item['presensi_apik'][1]['presensi_sync_eabsen'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][1]['presensi_sync_eabsen_id']) ? $item['presensi_apik'][1]['presensi_sync_eabsen_id'] : '-' }}</td>
                            <td>{{ isset($item['presensi_apik'][1]['__typename']) ? $item['presensi_apik'][1]['__typename'] : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div> --}}
    </div>
@endsection