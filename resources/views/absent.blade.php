@extends('welcome')

@section('navbar')
    <div class="container bg-warning py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="history.back()" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Absent Page</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white container text-center text-success">
        <p class="text-start mb-0 py-2 text-black fw-bold">Choose your action</p>
        <div class="row">
            <div class="col">
                <div class="d-grid">
                    <input type="button" value="Check In" class="btn btn-success">
                </div>
            </div>
            <div class="col">
                <div class="d-grid">
                    <input type="button" value="Check Out" class="btn btn-info">
                </div>
            </div>
        </div>
        <p class="text-start mt-4 mb-2 text-black fw-bold">Absent History</p>
    </div>
@endsection