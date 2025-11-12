{{-- Form Input Component --}}
@props([
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'help' => '',
    'icon' => '',
    'col' => 'col-md-6'
])

<div class="{{ $col }}">
    <div class="form-group @error($name) has-error @enderror">
        @if($label)
            <label for="{{ $name }}" class="form-label {{ $required ? 'required' : '' }}">
                {{ $label }}
            </label>
        @endif
        
        @if($help)
            <small class="form-label-description">{{ $help }}</small>
        @endif

        @if($icon)
        <div class="input-group">
            <span class="input-group-text">
                <i class="{{ $icon }}"></i>
            </span>
        @endif

        @if($type === 'textarea')
            <textarea 
                name="{{ $name }}" 
                id="{{ $name }}" 
                class="form-control @error($name) is-invalid @enderror"
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes }}
            >{{ old($name, $value) }}</textarea>
        @else
            <input 
                type="{{ $type }}" 
                name="{{ $name }}" 
                id="{{ $name }}" 
                class="form-control @error($name) is-invalid @enderror"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes }}
            >
        @endif

        @if($icon)
        </div>
        @endif

        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

