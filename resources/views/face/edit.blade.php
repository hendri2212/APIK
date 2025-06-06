@extends('../welcome')

@section('title', 'Edit Face User')

@section('navbar')
    <div class="container bg-light border-bottom py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="window.location='{{ route('face.data') }}'" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Edit Face Image</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white container" style="min-height: calc(100vh - 54px)">
        <form action="{{ route('face.update', $file->id) }}" method="post" class="mb-3">
            @csrf
            @method('PUT')
            
            <fieldset disabled>
            <div class="mb-3">
                <label for="face_name" class="form-label">Face Name</label>
                <input type="text" name="face_name" id="face_name" class="form-control" value="{{ old('face_name', $file->face_name) }}" required>
            </div>
            </fieldset>
            <div class="mb-3">
                <label for="day" class="form-label">Day</label>
                <select name="day" id="day" class="form-control" required>
                    @php
                        $daysOfWeek = [
                            0 => 'Minggu',
                            1 => 'Senin',
                            2 => 'Selasa',
                            3 => 'Rabu',
                            4 => 'Kamis',
                            5 => 'Jumat',
                            6 => 'Sabtu',
                        ];
                    @endphp
                    @foreach ($daysOfWeek as $key => $day)
                        <option value="{{ $key }}" {{ $file->day == $key ? 'selected' : '' }}>{{ $day }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="/face" class="btn btn-secondary">Cancel</a>
        </form>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    </div>
@endsection