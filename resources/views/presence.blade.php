@php
    use Carbon\Carbon;
@endphp

@extends('welcome')

@section('title', 'Presence Page')

@section('navbar')
    <div class="container bg-light border-bottom py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="history.back()" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Absent Page</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white container" style="min-height: calc(100vh - 51px)">
        <p class="text-start mb-0 py-2 text-black fw-bold">Choose your action</p>
        <div class="row">
            <div class="col">
                <div class="d-grid">
                    <a href="/checkin" class="btn btn-lg btn-success">Check In</a>
                </div>
            </div>
            <div class="col">
                <div class="d-grid">
                    <a href="/checkout" class="btn btn-lg btn-info">Check Out</a>
                </div>
            </div>
        </div>
    </div>
@endsection