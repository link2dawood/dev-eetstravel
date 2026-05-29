{{-- Client-facing 404 for the public landing page route. Used when:
       - the tour id doesn't exist
       - or the anonymous viewer didn't pass a valid ?t=<share_token>.
     Deliberately generic — does not reveal which case applies. --}}
@extends('layouts.public')

@section('title', trans('main.Itinerary') . ' — not available')

@section('content')
<main class="mx-auto flex min-h-screen max-w-md flex-col items-center justify-center px-6 py-12 text-center">
    <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
        <x-ui.icon name="link" size="lg" />
    </div>

    <h1 class="text-xl font-semibold text-slate-900">
        This itinerary isn't available
    </h1>

    <p class="mt-3 text-sm text-slate-500">
        The share link may have expired, been revoked, or contain a typo.
        Contact the travel agency you received it from to get a fresh link.
    </p>
</main>
@endsection
