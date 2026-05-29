@extends('auth.layout')
@section('title', 'Reset password')

@section('content')
<div>
    <h2 class="text-2xl font-semibold text-slate-900 mb-1">Forgot your password?</h2>
    <p class="text-sm text-slate-500 mb-8">No worries — we'll send a reset link to your inbox.</p>

    @if(session('status'))
        <div class="mb-4 flex items-start gap-3 rounded border border-success-600/20 bg-success-50 px-4 py-3 text-sm text-success-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 mt-0.5 text-success-600 shrink-0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
            <div class="flex-1">{{ session('status') }}</div>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="you@example.com"
                   class="block w-full h-10 rounded-md border {{ $errors->has('email') ? 'border-danger-600' : 'border-slate-300' }} bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            @error('email')<p class="mt-1 text-xs text-danger-700">{{ $message }}</p>@enderror
        </div>

        <button type="submit"
                class="inline-flex w-full h-10 items-center justify-center gap-2 rounded-md bg-primary-600 px-4 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:ring-offset-2">
            Send reset link
        </button>

        <p class="text-center text-xs text-slate-500 pt-2">
            Remembered? <a href="{{ url('/login') }}" class="text-primary-700 hover:text-primary-800 font-medium">Back to sign in</a>
        </p>
    </form>
</div>
@endsection
