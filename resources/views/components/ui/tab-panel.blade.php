{{--
    <x-ui.tab-panel /> — Single tab panel inside <x-ui.tabs />.

    Props
    -----
    id    (required string)  Unique panel id (also used as the URL hash).
    label (required string)  Visible tab label.
    icon  (string)            Optional Lucide icon name.
--}}

@props([
    'id',
    'label',
    'icon' => null,
])

@php
    // Render the icon SVG to an inline HTML string so the Tabs parent can
    // x-html it into the tab button.
    $iconHtml = '';
    if ($icon) {
        $iconHtml = view('components.ui.icon', ['name' => $icon, 'size' => 'sm', 'strokeWidth' => null])->render();
    }
@endphp

<div
    x-init="register({ id: '{{ $id }}', label: @js($label), icon: @js(trim($iconHtml) ?: null) })"
    x-show="active === '{{ $id }}'"
    x-cloak
    role="tabpanel"
    id="panel-{{ $id }}"
    aria-labelledby="tab-{{ $id }}"
    style="display: none"
>
    {{ $slot }}
</div>
