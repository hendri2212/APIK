@extends('welcome')

@section('title', 'Login Page')

@section('content')
    <div class="bg-white container text-center py-4">
        <img src="https://smkn1kotabaru.sch.id/media_library/images/c6ab60e235b679b8b7f9ac1743593c59.png" alt="Logo SMKN 1 Kotabaru" class="mb-4">
        <h2 class="text-uppercase fw-bold">Absent</h2>
        <h6 class="fw-bold">APPLICATION ABSENT SUPER AI AUTO</h6>
        @if($errors->has('loginError'))
            <div class="alert alert-danger">{{ $errors->first('loginError') }}</div>
        @endif
        <form action="{{ route('login.submit') }}" method="POST" class="w-100">
            @csrf
            <input type="text" id="username" name="username" placeholder="Your username" class="form-control form-control-lg rounded-0 border border-secondary" required>
            <input type="password" id="password" name="password" placeholder="Your password" class="form-control form-control-lg rounded-0 border border-secondary border-top-0" required>
            <div class="d-grid">
                <input type="submit" value="Sign In" class="btn btn-success btn-lg rounded-0">
            </div>
        </form>
    </div>
@endsection