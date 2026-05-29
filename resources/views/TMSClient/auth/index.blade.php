@extends('TMSClient.layouts.app')
@section('title', 'Sign in — TMS Client')
@section('with-nav-false')@endsection

@section('content')
<style>
    /* Auth-page-only scoped layout. Preflight is off project-wide so we
       can't depend on Tailwind base resets — explicit CSS guarantees the
       50/50 split renders correctly. */
    .tmsc-auth-shell {
        min-height: 100vh;
        display: flex;
        flex-direction: row;
        align-items: stretch;
        background: #f8fafc;
    }
    .tmsc-auth-aside {
        width: 50%;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 4rem 3rem;
        background-image: linear-gradient(to bottom right, #0f766e 0%, #0d9488 50%, #115e59 100%);
    }
    .tmsc-auth-main {
        flex: 1 1 auto;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 1.5rem;
    }
    .tmsc-auth-card { width: 100%; max-width: 28rem; }
    .tmsc-auth-mobile-logo { text-align: center; margin-bottom: 2rem; }

    @media (max-width: 1023.98px) {
        .tmsc-auth-aside { display: none; }
        .tmsc-auth-main  { width: 100%; padding: 3rem 1rem; }
    }
    @media (min-width: 1024px) {
        .tmsc-auth-mobile-logo { display: none; }
    }

    .tmsc-auth-aside h1 { margin: 0; font-size: 2.25rem; line-height: 1.15; font-weight: 600; letter-spacing: -0.02em; }
    .tmsc-auth-aside p.tmsc-auth-sub { margin: 1rem 0 0; color: rgba(255,255,255,0.85); max-width: 28rem; }
    .tmsc-auth-orb { position: absolute; border-radius: 9999px; filter: blur(48px); pointer-events: none; }
    .tmsc-auth-orb-tr { top: -6rem; right: -6rem; height: 18rem; width: 18rem; background: rgba(255,255,255,0.10); }
    .tmsc-auth-orb-bl { bottom: -8rem; left: -4rem; height: 20rem; width: 20rem; background: rgba(45,212,191,0.30); }
    .tmsc-auth-brand-row { position: relative; display: inline-flex; align-items: center; gap: 0.5rem; font-size: 1.125rem; font-weight: 600; letter-spacing: -0.02em; }
    .tmsc-auth-brand-chip { display: inline-flex; height: 2.25rem; width: 2.25rem; align-items: center; justify-content: center; border-radius: 0.375rem; background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); }
    .tmsc-auth-foot { position: relative; margin: 0; color: rgba(255,255,255,0.65); font-size: 0.75rem; }
    .tmsc-auth-mobile-brand { display: inline-flex; align-items: center; gap: 0.5rem; font-size: 1.125rem; font-weight: 600; color: #0f766e; }
    .tmsc-auth-mobile-chip  { display: inline-flex; height: 2.25rem; width: 2.25rem; align-items: center; justify-content: center; border-radius: 0.375rem; background: #f0fdfa; }
</style>

<div class="tmsc-auth-shell">
    {{-- Left brand panel --}}
    <aside class="tmsc-auth-aside">
        <div class="tmsc-auth-orb tmsc-auth-orb-tr" aria-hidden="true"></div>
        <div class="tmsc-auth-orb tmsc-auth-orb-bl" aria-hidden="true"></div>

        <div class="tmsc-auth-brand-row">
            <span class="tmsc-auth-brand-chip">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
                    <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
                </svg>
            </span>
            TMS Client
        </div>

        <div style="position:relative;">
            <h1>Welcome to your client portal.</h1>
            <p class="tmsc-auth-sub">Submit quotation requests, review your tours, and stay in sync with your travel partner.</p>
        </div>

        <p class="tmsc-auth-foot">© {{ date('Y') }} eetstravel.com</p>
    </aside>

    {{-- Right form pane --}}
    <main class="tmsc-auth-main">
        <div class="tmsc-auth-card">
            <div class="tmsc-auth-mobile-logo">
                <div class="tmsc-auth-mobile-brand">
                    <span class="tmsc-auth-mobile-chip">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
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
