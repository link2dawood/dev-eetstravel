@extends('auth.layout')
@section('title', 'Confirm password')

@section('content')
<div>
    <h2 class="text-2xl font-semibold text-slate-900 mb-1">Confirm your password</h2>
    <p class="text-sm text-slate-500 mb-8">For your security, please re-enter your password to continue.</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <div>
            <label for="password" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••"
                   class="block w-full h-10 rounded-md border {{ $errors->has('password') ? 'border-danger-600' : 'border-slate-300' }} bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            @error('password')<p class="mt-1 text-xs text-danger-700">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-between gap-3">
            <button type="submit"
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-md bg-primary-600 px-4 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:ring-offset-2">
                Confirm password
            </button>
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary-700 hover:text-primary-800">Forgot your password?</a>
            @endif
        </div>
    </form>
</div>
@endsection
