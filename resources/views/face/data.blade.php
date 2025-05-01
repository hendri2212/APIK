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
    <div class="bg-white container" style="min-height: calc(100vh - 51px)">
        <a href="{{ url('/face/add') }}" class="btn btn-primary mt-2">Add Image</a>
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