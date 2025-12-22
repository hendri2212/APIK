@extends('welcome')

@section('title', 'Profile Employee')

@section('navbar')
    <div class="container bg-light border-bottom py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="window.location='{{ route('dashboard') }}'" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Profile Employee</p>
        </div>
    </div>
@endsection

@section('content')
    @php
        $identitas = data_get($data ?? null, 'simpegIdentitas', $data ?? null);

        $fields = [
            'nip' => 'NIP',
            'nama' => 'Nama',
            'gelar_depan' => 'Gelar Depan',
            'gelar_belakang' => 'Gelar Belakang',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tanggal_lahir' => 'Tanggal Lahir',
            'agama' => 'Agama',
            'jenis' => 'Jenis',
            'jenis_jabatan' => 'Jenis Jabatan',
            'tingkat_jabatan' => 'Tingkat Jabatan',
            'golongan' => 'Golongan',
            'pangkat' => 'Pangkat',
            'kode_jabatan' => 'Kode Jabatan',
            'jabatan' => 'Jabatan',
            'kode_satker' => 'Kode Satker',
            'satker' => 'Satker',
            'kode_unker' => 'Kode Unker',
            'unker' => 'Unker',
            'instansi' => 'Instansi',
            'ktpu' => 'KTPU',
            'jurusan' => 'Jurusan',
            'status_pns' => 'Status PNS',
            'email' => 'Email',
            'alamat' => 'Alamat',
            'telpon' => 'Telpon',
        ];
    @endphp

    <div class="container bg-white min-vh-100 py-3">
        @if ($identitas)
            <div class="card mb-3">
                <div class="card-body text-center">
                    @php $image = data_get($identitas, 'images'); @endphp
                    @if ($image)
                        <img src="{{ e($image) }}" alt="Foto" class="rounded-circle border mb-2" style="width: 96px; height: 96px; object-fit: cover;">
                    @endif
                    <p class="fw-bold mb-0">{{ e(data_get($identitas, 'nama')) }}</p>
                    <p class="text-muted mb-0">{{ e(data_get($identitas, 'nip')) }}</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <tbody>
                        @foreach ($fields as $key => $label)
                            @php $value = data_get($identitas, $key); @endphp
                            @if ($value !== null && $value !== '')
                                <tr>
                                    <th class="text-nowrap" style="width: 34%;">{{ e($label) }}</th>
                                    <td>{{ e($value) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-warning mb-0" role="alert">
                Data work place belum tersedia.
            </div>
        @endif
    </div>
@endsection
