@extends('scaffold-interface.layouts.tabler-app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Configure Mailbox</h3>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('warning'))
                        <div class="alert alert-warning">{{ session('warning') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('snappymail.save') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="email_login" class="form-label">Email address</label>
                            <input type="email"
                                   class="form-control"
                                   id="email_login"
                                   name="email_login"
                                   value="{{ old('email_login', $user->email_login ?? '') }}"
                                   autocomplete="username"
                                   required>
                            <small class="form-hint">The full address you use to log in to your mailbox.</small>
                        </div>

                        <div class="mb-3">
                            <label for="email_password" class="form-label">Password</label>
                            <input type="password"
                                   class="form-control"
                                   id="email_password"
                                   name="email_password"
                                   autocomplete="current-password"
                                   required>
                            <small class="form-hint">
                                Encrypted at rest. You only need to re-enter this if your mailbox password changes.
                            </small>
                        </div>

                        <div class="alert alert-info">
                            <strong>IMAP server:</strong> {{ $imapHost }}<br>
                            <strong>Port:</strong> {{ $imapPort }}<br>
                            <strong>Encryption:</strong> {{ strtoupper($imapEncryption) }}
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                Save &amp; open webmail
                            </button>
                            <a href="{{ url('/home') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
