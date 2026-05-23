{{--
    <x-ui.form-field /> — Standard label + control + hint + error wrapper.

    This is the spine of every form. Always wrap an Input/Select/Textarea
    in a FormField unless you have a strong reason not to.

    Props
    -----
    label    (string)        Visible label. If `for` not provided we infer
                             from the control's `id` via :for.
    for      (string)        Override the `for` attribute.
    required (bool)          Renders an asterisk after the label.
    hint     (string)        Helper text under the field (slate-500).
    error    (string|null)   Validation error message. Renders below in
                             danger-600 and sets the wrapper's data-state.

    Slot
    ----
    default   The input/select/textarea, ideally one of our widgets.

    Example
    -------
        <x-ui.form-field label="Email" for="email" required
                         hint="We'll never share your email."
                         :error="$errors->first('email')">
            <x-ui.input name="email" id="email" type="email"
                        :invalid="$errors->has('email')" required />
        </x-ui.form-field>
--}}

@props([
    'label' => null,
    'for' => null,
    'required' => false,
    'hint' => null,
    'error' => null,
])

<div class="space-y-1" data-state="{{ $error ? 'error' : 'idle' }}">
    @if($label)
        <label
            @if($for) for="{{ $for }}" @endif
            class="block text-sm font-medium text-slate-700"
        >
            {{ $label }}
            @if($required)<span class="text-danger-600" aria-hidden="true">*</span>@endif
        </label>
    @endif

    {{ $slot }}

    @if($error)
        <p class="text-xs text-danger-600 flex items-start gap-1">
            <x-ui.icon name="alert-circle" size="xs" class="mt-0.5" />
            <span>{{ $error }}</span>
        </p>
    @elseif($hint)
        <p class="text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
