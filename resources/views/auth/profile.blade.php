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
        <div class="rounded-4 p-3 mb-3 text-white" style="background: linear-gradient(135deg, #1f6feb 0%, #0f5132 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="mb-1 small text-white-50">Profile</p>
                    <h2 class="h5 mb-0">Profil Pegawai</h2>
                </div>
                <div class="bg-white text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="bi bi-person-badge fs-4"></i>
                </div>
            </div>
        </div>

        @if ($identitas)
            @php
                $image = data_get($identitas, 'images');
                $nama = data_get($identitas, 'nama');
                $nip = data_get($identitas, 'nip');
            @endphp
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body d-flex align-items-center gap-3">
                    @if ($image)
                        <img src="{{ e($image) }}" alt="Foto" class="rounded-circle border" style="width: 72px; height: 72px; object-fit: cover;">
                    @else
                        <div class="rounded-circle border d-flex align-items-center justify-content-center bg-light text-muted" style="width: 72px; height: 72px;">
                            <i class="bi bi-person fs-3"></i>
                        </div>
                    @endif
                    <div>
                        <p class="fw-bold mb-1">{{ e($nama) }}</p>
                        <span class="badge text-bg-light border">
                            <i class="bi bi-person-vcard me-1"></i>{{ e($nip) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="row g-2">
                @foreach ($fields as $key => $label)
                    @php $value = data_get($identitas, $key); @endphp
                    @if ($value !== null && $value !== '')
                        <div class="col-12 col-md-6">
                            <div class="p-2 rounded-3 bg-light border h-100">
                                <div class="small text-muted">{{ e($label) }}</div>
                                <div class="fw-semibold small">{{ e($value) }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="alert alert-warning mb-0" role="alert">
                Data profile belum tersedia.
            </div>
        @endif
    </div>
@endsection
