@extends('TMSClient.layouts.app')
@section('title', 'Sign in — TMS Client')
@section('with-nav-false')@endsection

@section('content')
<div class="min-h-screen grid lg:grid-cols-2">
    {{-- Left brand panel --}}
    <aside class="hidden lg:flex relative overflow-hidden bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 text-white px-12 py-16 flex-col justify-between">
        <div aria-hidden="true" class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
        <div aria-hidden="true" class="absolute -bottom-32 -left-16 h-80 w-80 rounded-full bg-primary-400/30 blur-3xl"></div>

        <div class="relative inline-flex items-center gap-2 text-lg font-semibold tracking-tight">
            <span class="flex h-9 w-9 items-center justify-center rounded-md bg-white/15 backdrop-blur">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                    <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
                </svg>
            </span>
            TMS Client
        </div>

        <div class="relative">
            <h1 class="text-4xl font-semibold tracking-tight leading-tight">Welcome to your client portal.</h1>
            <p class="mt-4 text-base text-white/80 max-w-md">Submit quotation requests, review your tours, and stay in sync with your travel partner.</p>
        </div>

        <p class="relative text-xs text-white/60">© {{ date('Y') }} eetstravel.com</p>
    </aside>

    {{-- Right form pane --}}
    <main class="flex items-center justify-center px-4 py-12 sm:px-6 lg:px-12">
        <div class="w-full max-w-md">
            <div class="lg:hidden mb-8 text-center">
                <div class="inline-flex items-center gap-2 text-lg font-semibold text-primary-700">
                    <span class="flex h-9 w-9 items-center justify-center rounded-md bg-primary-50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                            <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
                        </svg>
                    </span>
                    TMS Client
                </div>
            </div>

            <h2 class="text-2xl font-semibold text-slate-900 mb-1">Sign in to your account</h2>
            <p class="text-sm text-slate-500 mb-8">Use the credentials your travel partner shared with you.</p>

            @if(session('error'))
                <div class="mb-4 flex items-start gap-3 rounded border border-danger-600/20 bg-danger-50 px-4 py-3 text-sm text-danger-700">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 mt-0.5 text-danger-600 shrink-0"><path d="M7.86 2h8.28L22 7.86v8.28L16.14 22H7.86L2 16.14V7.86L7.86 2z"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                    <div class="flex-1">{{ session('error') }}</div>
                </div>
            @endif

            <form action="{{ route('client.login') }}" method="post" class="space-y-4">
                {{ csrf_field() }}
                <div>
                    <label for="contact_email" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Email</label>
                    <input id="contact_email" type="email" name="contact_email" required autofocus autocomplete="email" placeholder="you@example.com"
                           class="block w-full h-10 rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>
                <div>
                    <label for="password" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                           class="block w-full h-10 rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
                </div>

                <button type="submit"
                        class="inline-flex w-full h-10 items-center justify-center gap-2 rounded-md bg-primary-600 px-4 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:ring-offset-2">
                    Sign in
                </button>
            </form>
        </div>
    </main>
</div>
@endsection
