@php
    use App\Helper\PermissionHelper;
    
    $permissions = [];
    $entity = $model ?? $item ?? null;
    $prefix = $routePrefix ?? '';

    if ($entity && Auth::check()) {
        try {
            $entityClass = get_class($entity);
            $result = PermissionHelper::getActionPermission($entityClass, Auth::id());
            $permissions = is_array($result) ? $result : [];
        } catch (Exception $e) {
            $permissions = [];
        }
    }

    $show_route = null;
    $edit_route = null;
    $delete_route = null;

    if ($prefix && $entity) {
        try {
            if ($prefix === 'tour') {
                $show_route = route('tour.show', ['tour' => $entity->id]);
                $edit_route = route('tour.edit', ['tour' => $entity->id]);
                $delete_route = route('tour.destroy', ['id' => $entity->id]);
            } elseif ($prefix === 'users') {
                $edit_route = route('users.edit', ['user' => $entity->id]);
                $delete_route = route('users.destroy', ['user' => $entity->id]);
            } elseif ($prefix === 'announcements') {
                $show_route = route('announcements.show', ['announcement' => $entity->id]);
                $edit_route = route('announcements.edit', ['announcement' => $entity->id]);
                $delete_route = route('announcements.destroy', ['announcement' => $entity->id]);
            } elseif ($prefix === 'task') {
                $show_route = route('task.show', ['id' => $entity->id]);
                $edit_route = route('task.edit', ['id' => $entity->id]);
                $delete_route = route('task.destroy', ['id' => $entity->id]);
            } else {
                try { $show_route = route($prefix . '.show', [$prefix => $entity->id]); } 
                catch (Exception $e) { $show_route = "/{$prefix}/{$entity->id}/show"; }
                
                try { $edit_route = route($prefix . '.edit', [$prefix => $entity->id]); }
                catch (Exception $e) { $edit_route = "/{$prefix}/{$entity->id}/edit"; }
                
                try { $delete_route = route($prefix . '.destroy', [$prefix => $entity->id]); }
                catch (Exception $e) { $delete_route = "/{$prefix}/{$entity->id}/delete"; }
            }
        } catch (Exception $e) {
            if ($entity) {
                $show_route = "/{$prefix}/{$entity->id}/show";
                $edit_route = "/{$prefix}/{$entity->id}/edit";
                $delete_route = "/{$prefix}/{$entity->id}/delete";
            }
        }
    }

    $canShow = $show_route && ($permissions['show'] ?? true);
    $canEdit = $edit_route && ($permissions['edit'] ?? true);
    $canDelete = $delete_route && ($permissions['destroy'] ?? true);
    
    if ($prefix === 'transfer') { $canDelete = true; }
    if ($prefix === 'bus') {
        $canShow = (bool) $show_route;
        $canEdit = (bool) $edit_route;
        $canDelete = (bool) $delete_route;
    }
    $canClone = ($prefix === 'tour') && Auth::check() && Auth::user()->can('tour.create');

    $deleteMethodOverrides = [
        'announcements' => 'DELETE',
        'users' => 'DELETE',
        'notifications' => 'DELETE',
    ];
    $delete_method = $deleteMethodOverrides[$prefix] ?? 'GET';
@endphp

<div class="btn-list flex-nowrap">
    @if($canShow)
        <a href="{{ $show_route }}" class="btn btn-sm btn-warning" title="{{ trans('main.View') ?? 'View' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
            </svg>
        </a>
    @endif

    @if($canEdit)
        <a href="{{ $edit_route }}" class="btn btn-sm btn-primary" title="{{ trans('main.Edit') ?? 'Edit' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" />
            </svg>
        </a>
    @endif

    @if($canDelete)
        {{-- ================================== --}}
        {{-- == START OF DELETE BUTTON FIX == --}}
        {{-- ================================== --}}
        <button type="button" 
                class="btn btn-sm btn-danger delete" {{-- Class changed to 'delete' to match your script --}}
                data-link="{{ $delete_route }}" {{-- Attribute changed to 'data-link' to match your script --}}
                data-delete-method="{{ $delete_method }}"
                title="{{ trans('main.Delete') ?? 'Delete' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
            </svg>
        </button>
        {{-- ================================== --}}
        {{-- == END OF DELETE BUTTON FIX == --}}
        {{-- ================================== --}}
    @endif

    @if($canClone)
        <button type="button" 
                class="btn btn-sm btn-success clone-tour-button"
                data-bs-toggle="modal"
                data-bs-target="#tour-clone-modal"
                data-id="{{ $entity->id }}"
                title="{{ trans('main.Clone') ?? 'Clone' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" /><path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" />
            </svg>
        </button>
    @endif
</div>

{{-- ================================== --}}
{{-- == FIX: REMOVED THE ENTIRE @once BLOCK == --}}
{{-- This was creating the conflicting modal and script --}}
{{-- ================================== --}}