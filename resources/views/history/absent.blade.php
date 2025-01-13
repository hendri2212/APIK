@php
    use Carbon\Carbon;
@endphp

@extends('../welcome')
 
@section('title', 'Absent History')
 
@section('content')
    <h5>Data Absent History</h5>
    <form method="GET" action="{{ url('/history') }}" class="mb-3">
        <div class="mb-3">
            <label for="tanggal" class="form-label">Pilih Tanggal (Bulan)</label>
            <input type="month" id="tanggal" name="tanggal" class="form-control" value="{{ $tanggal }}">
        </div>
        <button type="submit" class="btn btn-primary">Tampilkan Data</button>
    </form>
    <div class="table-responsive">
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
    </div>
@endsection