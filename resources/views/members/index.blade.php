@extends('welcome')

@section('title', 'Members List')

@section('navbar')
    <div class="container bg-light border-bottom py-2">
        <div class="d-flex align-items-center">
            <i class="bi bi-arrow-left-circle fs-3" onclick="window.location='{{ route('dashboard') }}'" style="cursor: pointer;"></i>
            <p class="mb-0 mx-3 fw-bold">Members Page</p>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white bg-white min-vh-100 py-3">
        <div class="rounded-4 p-3 mb-3 text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="mb-1 small text-white-50">Members</p>
                    <h2 class="h5 mb-0">Manage Members</h2>
                    <p class="mb-0 small text-white-50">Add or update member details below.</p>
                </div>
                <a href="{{ url('/members/create') }}" class="bg-white text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="bi bi-people-fill fs-4"></i>
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-end" colspan="2">ID</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Uuid</th>
                        <th>Telegram</th>
                        <th>Expired</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                    <tr>
                        <td class="d-flex">
                            <form action="{{ 'members/' . $member->id }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="absent_type" value="0">
                                <div class="form-check form-switch">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="absent_type"
                                        value="1"
                                        role="switch"
                                        onchange="this.form.submit()"
                                        {{ $member->absent_type == 1 ? 'checked' : '' }}
                                    >
                                </div>
                            </form>
                            <a href="{{ route('members.edit', $member->id) }}" class="text-decoration-none ms-2">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                        <td class="text-center">{{ $member->id }}</td>
                        <td class="text-nowrap">{{ $member->name }}</td>
                        <td>{{ $member->username }}</td>
                        <td>{{ $member->password }}</td>
                        <td>{{ $member->uuid }}</td>
                        <td>{{ $member->telegram_id }}</td>
                        <td class="text-nowrap">{{ date('d-m-Y', strtotime($member->expired)) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">No members found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
