@extends('../welcome')

@section('title', 'Add Face User')

@section('navbar')
    <div class="container bg-warning py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="history.back()" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Add Face Image</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white container" style="height: 90vh">
        <form action="{{ route('face.store') }}" method="post" enctype="multipart/form-data" class="mb-3">
            @csrf
            <div class="mb-3">
                <label for="formFile" class="form-label">Choose image</label>
                <input type="file" name="face_name" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    </div>
@endsection