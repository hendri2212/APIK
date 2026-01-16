@extends('../welcome')
 
@section('title', 'Data Face User')

@section('navbar')
    <div class="container bg-light border-bottom py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="window.location='{{ route('dashboard') }}'" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Data Face Image</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="container bg-white min-vh-100 py-3">
        <div class="rounded-4 p-3 mb-3 text-white" style="background: linear-gradient(135deg, #1b214a 0%, #2c7be5 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="mb-1 small text-white-50">Your Face</p>
                    <h2 class="h5 mb-0">Face Recognition</h2>
                </div>
                {{-- <div class="bg-white text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="bi bi-calendar2-week fs-4"></i>
                </div> --}}
                <a href="{{ url('/face/add') }}" class="bg-white text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="bi bi-person-bounding-box fs-4"></i>
                </a>
            </div>
        </div>
        
        @forelse($facePaths as $path)
            <div class="alert alert-light shadow-sm mb-0 mt-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="rounded-4 overflow-hidden" style="width: 100px; height: 100px;">
                        <img src="{{ $path['path'] }}" alt="face-name" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                    </div>
                    <div>
                        <p>Day: {{ $path['day'] }}</p>
                    </div>
                    <div class="btn-group">
                        <a href="/face/{{ $path['id'] }}" class="btn btn-success">Edit</a>
                        <form action="{{ route('face.delete', $path['id']) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus data ini?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-start-0">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p>No images found.</p>
        @endforelse
    </div>
@endsection
