{{--
    /help — Help / Documentation index page.
    Route:  GET /help (help.index)
    Layout: scaffold-interface.layouts.tabler-app (staff layout, with sidebar)

    Vars from HelpController::index():
        $faqs       array<{id, question, answer}>     six curated entries
        $quickLinks array<{icon, label, href, description}>  four entries

    No JS framework, no Bootstrap. Native HTML <details> + Tailwind only.
--}}
@extends('scaffold-interface.layouts.tabler-app')

@section('title', 'Help')

@section('content')

<x-ui.page-header
    title="Help"
    description="Common questions, useful links, and how to get in touch when you're stuck."
    :breadcrumbs="[
        ['label' => 'Home', 'href' => url('/home')],
        ['label' => 'Help'],
    ]"
/>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

    {{-- Quick links — 1/3 width on lg+, full width on small --}}
    <div class="lg:col-span-1">
        <x-ui.card title="Quick links" description="Documentation, shortcuts, and the bug tracker.">
            @if (empty($quickLinks))
                <x-ui.empty-state
                    icon="link"
                    title="No links yet"
                    message="Quick links will appear here once your admin sets them up."
                />
            @else
                <ul class="divide-y divide-slate-100 -my-2">
                    @foreach ($quickLinks as $link)
                        <li class="py-3">
                            <a
                                href="{{ $link['href'] }}"
                                @if (str_starts_with($link['href'], 'http')) target="_blank" rel="noopener noreferrer" @endif
                                class="group flex items-start gap-3 rounded-sm p-2 -mx-2 transition-colors hover:bg-slate-50"
                            >
                                <span class="mt-0.5 flex h-8 w-8 flex-none items-center justify-center rounded bg-primary-50 text-primary-600">
                                    <x-ui.icon :name="$link['icon']" />
                                </span>
                                <span class="flex-1 min-w-0">
                                    <span class="block text-sm font-medium text-slate-900 group-hover:text-primary-700">
                                        {{ $link['label'] }}
                                    </span>
                                    @if (!empty($link['description']))
                                        <span class="block text-xs text-slate-500 mt-0.5">
                                            {{ $link['description'] }}
                                        </span>
                                    @endif
                                </span>
                                <x-ui.icon
                                    name="{{ str_starts_with($link['href'], 'http') ? 'external-link' : 'arrow-right' }}"
                                    size="xs"
                                    class="mt-1 text-slate-300 group-hover:text-slate-500"
                                />
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>
    </div>

    {{-- FAQ + contact form — 2/3 width on lg+ --}}
    <div class="space-y-6 lg:col-span-2">

        <x-ui.card title="Frequently asked questions" description="Quick answers to common questions.">
            @if (empty($faqs))
                <x-ui.empty-state
                    icon="message-circle-question"
                    title="No FAQs yet"
                    message="Check back later — we'll add common questions here as they come up."
                />
            @else
                <div class="-my-2 divide-y divide-slate-100">
                    @foreach ($faqs as $faq)
                        <details id="{{ $faq['id'] }}" class="group py-3">
                            <summary class="flex cursor-pointer list-none items-start gap-3 py-1 text-sm font-medium text-slate-900 outline-none focus-visible:rounded focus-visible:ring-2 focus-visible:ring-primary-600/30">
                                <span class="mt-0.5 text-slate-400 transition-transform group-open:rotate-90">
                                    <x-ui.icon name="chevron-right" size="xs" />
                                </span>
                                <span class="flex-1">{{ $faq['question'] }}</span>
                            </summary>
                            <div class="pl-7 pt-2 text-sm leading-relaxed text-slate-600">
                                {{ $faq['answer'] }}
                            </div>
                        </details>
                    @endforeach
                </div>
            @endif
        </x-ui.card>

        {{-- Contact form --}}
        <x-ui.card title="Contact support" description="Can't find what you need? Send a short message and the team will follow up by email.">

            {{-- Server-side flash: success toast renders via the layout's
                 toast container (session('toast') is consumed there). --}}
            @if (session()->has('toast'))
                <div class="mb-4 flex items-start gap-2 rounded border border-success-600/20 bg-success-50 p-3 text-sm text-success-700">
                    <x-ui.icon name="check-circle-2" class="mt-0.5 shrink-0" />
                    <span>{{ session('toast')['message'] ?? 'Saved.' }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('help.contact') }}" class="space-y-4">
                @csrf

                <x-ui.form-field
                    label="Subject"
                    for="help-subject"
                    required
                    :error="$errors->first('subject')"
                >
                    <x-ui.input
                        name="subject"
                        id="help-subject"
                        :value="old('subject')"
                        :invalid="$errors->has('subject')"
                        placeholder="A short description of what you need help with"
                        required
                    />
                </x-ui.form-field>

                <x-ui.form-field
                    label="Message"
                    for="help-message"
                    required
                    hint="Include the URL you were on and any error messages you saw."
                    :error="$errors->first('message')"
                >
                    <x-ui.textarea
                        name="message"
                        id="help-message"
                        :value="old('message')"
                        :invalid="$errors->has('message')"
                        rows="6"
                        placeholder="What were you trying to do? What went wrong?"
                        required
                    />
                </x-ui.form-field>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <x-ui.button as="a" href="{{ route('help.index') }}" variant="secondary">
                        Cancel
                    </x-ui.button>
                    <x-ui.button type="submit" variant="primary" icon="send">
                        Send message
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>

</div>

@endsection
