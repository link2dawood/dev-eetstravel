{{-- Form Section Component --}}
@props([
    'title' => '',
    'icon' => '',
    'description' => ''
])

<div class="form-section">
    @if($title)
    <div class="form-section-title">
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        <div>
            <div>{{ $title }}</div>
            @if($description)
                <small class="text-muted" style="font-weight: normal; font-size: 0.875rem;">{{ $description }}</small>
            @endif
        </div>
    </div>
    @endif
    
    <div class="form-row">
        {{ $slot }}
    </div>
</div>

