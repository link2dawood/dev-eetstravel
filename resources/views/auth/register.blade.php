@extends('auth.layout')
@section('title', 'Create account')

@section('content')
<div>
    <h2 class="text-2xl font-semibold text-slate-900 mb-1">Create your account</h2>
    <p class="text-sm text-slate-500 mb-8">Get started with TMS in under a minute.</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Jane Doe"
                   class="block w-full h-10 rounded-md border {{ $errors->has('name') ? 'border-danger-600' : 'border-slate-300' }} bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            @error('name')<p class="mt-1 text-xs text-danger-700">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="you@example.com"
                   class="block w-full h-10 rounded-md border {{ $errors->has('email') ? 'border-danger-600' : 'border-slate-300' }} bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            @error('email')<p class="mt-1 text-xs text-danger-700">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="At least 8 characters"
                   class="block w-full h-10 rounded-md border {{ $errors->has('password') ? 'border-danger-600' : 'border-slate-300' }} bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
            @error('password')<p class="mt-1 text-xs text-danger-700">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password-confirm" class="block text-xs font-medium uppercase tracking-wide text-slate-500 mb-1">Confirm password</label>
            <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Re-enter password"
                   class="block w-full h-10 rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-subtle focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:border-primary-600" />
        </div>

        <button type="submit"
                class="inline-flex w-full h-10 items-center justify-center gap-2 rounded-md bg-primary-600 px-4 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-600/30 focus:ring-offset-2">
            Create account
        </button>

        <p class="text-center text-xs text-slate-500 pt-2">
            Already have an account? <a href="{{ url('/login') }}" class="text-primary-700 hover:text-primary-800 font-medium">Sign in</a>
        </p>
    </form>
</div>
@endsection
