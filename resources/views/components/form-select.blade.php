{{-- Form Select Component --}}
@props([
    'name' => '',
    'label' => '',
    'value' => '',
    'options' => [],
    'required' => false,
    'disabled' => false,
    'help' => '',
    'placeholder' => 'Select an option',
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

        <select 
            name="{{ $name }}" 
            id="{{ $name }}" 
            class="form-control form-select @error($name) is-invalid @enderror"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes }}
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            
            @foreach($options as $key => $option)
                @if(is_array($option))
                    <option value="{{ $option['value'] ?? $key }}" 
                        {{ (old($name, $value) == ($option['value'] ?? $key)) ? 'selected' : '' }}>
                        {{ $option['label'] ?? $option['value'] ?? $key }}
                    </option>
                @else
                    <option value="{{ $key }}" {{ (old($name, $value) == $key) ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endif
            @endforeach

            {{ $slot }}
        </select>

        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

