@extends('../welcome')
 
@section('title', 'Data Face User')

@section('navbar')
    <div class="container bg-warning py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="history.back()" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Presence History</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white container">
        <button type="submit" class="btn btn-primary mt-2">Add Image</button>
        @forelse($facePaths as $path)
            <div class="alert alert-light mb-0 mt-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="rounded-4 overflow-hidden" style="width: 100px; height: 100px;">
                        <img src="{{ $path }}" alt="face-name" class="img-fluid" style="object-fit: cover; width: 100%; height: 100%;">
                    </div>
                    <div class="btn-group">
                        <input type="button" class="btn btn-success" value="Verify">
                        <button class="btn btn-danger">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <p>No images found.</p>
        @endforelse
    </div>
@endsection