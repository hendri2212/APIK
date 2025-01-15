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
                    <a href="/checkin" class="btn btn-success">Check In</a>
                </div>
            </div>
            <div class="col">
                <div class="d-grid">
                    <a href="/checkout" class="btn btn-success">Check Out</a>
                </div>
            </div>
        </div>
        <p class="text-start mt-4 mb-2 text-black fw-bold">Absent History</p>
    </div>
@endsection