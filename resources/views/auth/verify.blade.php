@extends('auth.layout')
@section('title', 'Verify your email')

@section('content')
<div>
    <h2 class="text-2xl font-semibold text-slate-900 mb-1">Verify your email</h2>
    <p class="text-sm text-slate-500 mb-8">We sent a verification link to your inbox. Click it to activate your account.</p>

    @if(session('resent'))
        <div class="mb-4 flex items-start gap-3 rounded border border-success-600/20 bg-success-50 px-4 py-3 text-sm text-success-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 mt-0.5 text-success-600 shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
            <div class="flex-1">A fresh verification link has been sent to your email address.</div>
        </div>
    @endif

    <div class="rounded-md border border-slate-200 bg-white p-5 mb-4">
        <div class="flex items-start gap-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-50 text-primary-600 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 5L2 7"/></svg>
            </span>
            <div class="flex-1">
                <p class="text-sm font-medium text-slate-900">Check your inbox</p>
                <p class="mt-0.5 text-sm text-slate-500">Click the link in the email we just sent you. It might take a minute to arrive.</p>
            </div>
        </div>
    </div>

    <p class="text-sm text-slate-600">
        Didn't receive the email?
        <form class="inline" method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-primary-700 hover:text-primary-800 align-baseline">Resend the link</button>.
        </form>
    </p>
</div>
@endsection
