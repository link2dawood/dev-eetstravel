{{-- Modern Form Component --}}
@props([
    'method' => 'POST',
    'action' => '',
    'title' => '',
    'subtitle' => '',
    'icon' => '',
    'stickyActions' => false,
    'id' => ''
])

<div class="modern-form-container" id="{{ $id }}">
    @if($title)
    <div class="form-section-title">
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        <div>
            <div>{{ $title }}</div>
            @if($subtitle)
                <small class="text-muted" style="font-weight: normal; font-size: 0.875rem;">{{ $subtitle }}</small>
            @endif
        </div>
    </div>
    @endif

    <form method="{{ $method === 'GET' ? 'GET' : 'POST' }}" action="{{ $action }}" enctype="multipart/form-data">
        @if($method !== 'GET')
            @csrf
        @endif
        
        @if(in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
            @method($method)
        @endif

        {{ $slot }}
    </form>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/modern-forms.css') }}">
@endpush

