@extends('scaffold-interface.layouts.tabler-app')
@section('title', 'Configure Email')
@section('content')
<div class="container-xl py-3">
    <div class="row justify-content-center"><div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h1 class="card-title">Configure email</h1></div>
            <div class="card-body">
                @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
                @if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
                @if($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                <p>Enter your own mailbox credentials. We verify sign-in before saving your password securely.</p>
                <form action="{{ route('snappymail.save') }}" method="POST">
                    @csrf
                    <div class="mb-3"><label for="email_login" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email_login" name="email_login" value="{{ old('email_login', $user->email_login) }}" autocomplete="username" maxlength="191" required></div>
                    <div class="mb-3"><label for="email_password" class="form-label">Mailbox password</label>
                        <input type="password" class="form-control" id="email_password" name="email_password" autocomplete="current-password" minlength="6" required></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ url('/home') }}" class="btn btn-outline-secondary">Back</a>
                        <button type="submit" class="btn btn-primary">Verify and save</button>
                    </div>
                </form>
                @if($user->email_login)<a class="btn btn-link mt-3" href="{{ route('snappymail.sso') }}">Open webmail</a>@endif
            </div>
        </div>
    </div></div>
</div>
@endsection
