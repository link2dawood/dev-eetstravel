{{-- Form Actions Component --}}
@props([
    'submitText' => 'Save',
    'cancelUrl' => '',
    'sticky' => false,
    'submitIcon' => 'ti ti-device-floppy',
    'cancelIcon' => 'ti ti-arrow-left'
])

<div class="form-actions {{ $sticky ? 'sticky' : '' }}">
    @if($cancelUrl)
        <a href="{{ $cancelUrl }}" class="btn btn-secondary">
            @if($cancelIcon)
                <i class="{{ $cancelIcon }}"></i>
            @endif
            {{ trans('main.Cancel') }}
        </a>
    @else
        <button type="button" onclick="history.back()" class="btn btn-secondary">
            @if($cancelIcon)
                <i class="{{ $cancelIcon }}"></i>
            @endif
            {{ trans('main.Back') }}
        </button>
    @endif

    {{ $slot }}

    <button type="submit" class="btn btn-success">
        @if($submitIcon)
            <i class="{{ $submitIcon }}"></i>
        @endif
        {{ $submitText }}
    </button>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add loading state to submit buttons
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.classList.contains('loading')) {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            }
        });
    });
});
</script>
@endpush

