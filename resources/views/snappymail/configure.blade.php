@extends('scaffold-interface.layouts.tabler-app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Configure Email Settings</h3>
                </div>

                <form action="{{ route('snappymail.save') }}" method="POST">
                    @csrf

                    <div class="box-body">
                        <div class="form-group">
                            <label for="email_login">Email Address</label>
                            <input type="email"
                                   class="form-control"
                                   id="email_login"
                                   name="email_login"
                                   value="{{ $user->email_login ?? '' }}"
                                   required>
                            <small class="text-muted">Your email address for webmail login</small>
                        </div>

                        <div class="form-group">
                            <label for="email_password">Email Password</label>
                            <input type="password"
                                   class="form-control"
                                   id="email_password"
                                   name="email_password"
                                   required>
                            <small class="text-muted">Your email password (will be encrypted)</small>
                        </div>

                        <div class="box box-info">
                            <div class="box-header">
                                <h4 class="box-title">Server Settings</h4>
                            </div>
                            <div class="box-body">
                                <p><strong>IMAP Server:</strong> {{ $imapHost }}</p>
                                <p><strong>IMAP Port:</strong> {{ $imapPort }}</p>
                                <p><strong>Encryption:</strong> {{ strtoupper($imapEncryption) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Save Configuration
                        </button>
                        <a href="{{ url('/home') }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
