<fieldset class="form-group mb-3">
    <legend class="form-label">Task status behavior</legend>
    <p class="text-muted">For task statuses, choose completed, aborted, or leave both unchecked for To-Do.</p>
    @foreach(['is_completed' => 'Completed', 'is_aborted' => 'Aborted'] as $field => $label)
        <input type="hidden" name="{{ $field }}" value="0">
        <label class="form-check">
            <input class="form-check-input" type="checkbox" name="{{ $field }}" value="1"
                   {{ old($field, isset($status) ? $status->$field : false) ? 'checked' : '' }}>
            <span class="form-check-label">{{ $label }}</span>
        </label>
    @endforeach
</fieldset>
