<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-envelope"></i> Email Access
        </h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <p class="text-center">
                    <a href="{{ route('snappymail.sso') }}"
                       class="btn btn-primary btn-block"
                       target="_blank">
                        <i class="fa fa-envelope-open"></i> Open Webmail
                    </a>
                </p>

                @if($snappymailConfigured ?? false)
                    <div class="alert alert-success" style="margin-top: 10px;">
                        <i class="fa fa-check-circle"></i> Email configured
                    </div>
                @else
                    <div class="alert alert-warning" style="margin-top: 10px;">
                        <i class="fa fa-exclamation-triangle"></i>
                        Email not configured.
                        <a href="{{ route('snappymail.configure') }}">Configure now</a>
                    </div>
                @endif

                <hr>

                <div class="info-box-content">
                    <span class="info-box-text">Quick Actions</span>
                    <div class="btn-group-vertical btn-block" style="margin-top: 10px;">
                        <a href="{{ route('templates.index') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-file-code-o"></i> Email Templates
                        </a>
                        <a href="{{ route('email.index') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-list"></i> Old Inbox (Legacy)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
