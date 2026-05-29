@extends('scaffold-interface.layouts.tabler-app')

@section('content')
<div class="container-xl py-6">
    {{-- Page header --}}
    <header class="mb-6">
        <nav aria-label="Breadcrumb" class="mb-2">
            <ol class="flex items-center gap-1 text-xs text-slate-500 list-none pl-0 m-0">
                <li><a href="{{ url('/home') }}" class="hover:text-slate-700">Home</a></li>
                <li><x-ui.icon name="chevron-right" size="xs" class="text-slate-300" /></li>
                <li class="text-slate-700" aria-current="page">Mailbox configuration</li>
            </ol>
        </nav>

        <div class="flex items-start gap-3">
            <span class="flex h-9 w-9 items-center justify-center rounded bg-primary-50 text-primary-600">
                <x-ui.icon name="mail" />
            </span>
            <div class="flex-1 min-w-0">
                <h1 class="text-xl font-semibold text-slate-900">Configure mailbox</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Save your IMAP credentials once. Webmail signs you in automatically next time.
                </p>
            </div>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 flex items-start gap-3 rounded border border-success-600/20 bg-success-50 px-4 py-3 text-sm text-success-700">
            <x-ui.icon name="check-circle" class="mt-0.5 text-success-600" />
            <div class="flex-1">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('warning'))
        <div class="mb-4 flex items-start gap-3 rounded border border-warning-600/20 bg-warning-50 px-4 py-3 text-sm text-warning-700">
            <x-ui.icon name="alert-triangle" class="mt-0.5 text-warning-600" />
            <div class="flex-1">{{ session('warning') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 flex items-start gap-3 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
            <x-ui.icon name="alert-octagon" class="mt-0.5 text-danger-600" />
            <div class="flex-1">{{ session('error') }}</div>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
            <div class="flex items-center gap-2 font-medium">
                <x-ui.icon name="alert-octagon" class="text-danger-600" />
                Please correct the following:
            </div>
            <ul class="mt-2 list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Form card --}}
        <div class="lg:col-span-2 rounded border border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-5 py-3">
                <h2 class="text-sm font-medium text-slate-700">Credentials</h2>
            </div>

            <form action="{{ route('snappymail.save') }}" method="POST" class="px-5 py-5 space-y-5">
                @csrf

                <div>
                    <label for="email_login" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                        Email address
                    </label>
                    <input type="email"
                           class="block w-full h-9 rounded border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                           id="email_login"
                           name="email_login"
                           value="{{ old('email_login', $user->email_login ?? '') }}"
                           autocomplete="username"
                           placeholder="you@yourcompany.com"
                           required>
                    <p class="mt-1 text-xs text-slate-500">
                        The full address you use to log in to your mailbox.
                    </p>
                </div>

                <div>
                    <label for="email_password" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">
                        Password
                    </label>
                    <div class="relative">
                        <input type="password"
                               class="block w-full h-9 rounded border border-slate-300 bg-white px-3 pr-10 text-sm text-slate-700 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600"
                               id="email_password"
                               name="email_password"
                               autocomplete="current-password"
                               placeholder="••••••••"
                               required>
                        <button type="button"
                                onclick="(function(b){var i=document.getElementById('email_password');i.type=i.type==='password'?'text':'password';b.querySelector('[data-on]').classList.toggle('hidden');b.querySelector('[data-off]').classList.toggle('hidden');})(this)"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-600"
                                aria-label="Show or hide password">
                            <span data-on><x-ui.icon name="eye" size="sm" /></span>
                            <span data-off class="hidden"><x-ui.icon name="eye-off" size="sm" /></span>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">
                        Encrypted at rest. You only need to re-enter this if your mailbox password changes.
                    </p>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit"
                            class="inline-flex h-9 items-center gap-2 rounded bg-primary-600 px-4 text-sm font-medium text-white hover:bg-primary-700">
                        <x-ui.icon name="save" />
                        Save &amp; open webmail
                    </button>
                    <a href="{{ url('/home') }}"
                       class="inline-flex h-9 items-center gap-2 rounded border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        {{-- Side panel: IMAP info + help --}}
        <div class="space-y-4">
            <div class="rounded border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-5 py-3">
                    <h2 class="text-sm font-medium text-slate-700 flex items-center gap-2">
                        <x-ui.icon name="server" size="xs" class="text-slate-400" />
                        IMAP server
                    </h2>
                </div>
                <dl class="px-5 py-4 space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Host</dt>
                        <dd class="mt-0.5 font-mono text-slate-800">{{ $imapHost ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Port</dt>
                        <dd class="mt-0.5 font-mono text-slate-800">{{ $imapPort }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Encryption</dt>
                        <dd class="mt-0.5">
                            <span class="inline-flex items-center gap-1 rounded bg-success-50 px-2 py-0.5 text-xs font-medium text-success-700">
                                <x-ui.icon name="lock" size="xs" />
                                {{ strtoupper($imapEncryption) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="rounded border border-info-600/20 bg-info-50 p-4">
                <div class="flex items-start gap-3">
                    <x-ui.icon name="info" class="mt-0.5 text-info-600" />
                    <div class="text-sm text-info-700">
                        <p class="font-medium">First time signing in?</p>
                        <p class="mt-1">
                            After saving you'll be redirected to webmail. If your mailbox provider uses
                            app-passwords, generate one specifically for IMAP — not your normal login password.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
