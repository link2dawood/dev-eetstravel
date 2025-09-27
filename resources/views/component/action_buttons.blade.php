@php
    use App\Helper\PermissionHelper;
    $permissions = [];

    // Handle both old and new parameter formats
    $entity = $model ?? $item ?? null;
    $prefix = $routePrefix ?? '';

    if ($entity && Auth::check()) {
        $permissions = PermissionHelper::getActionPermission(get_class($entity), Auth::id());
    }

    // Build routes based on routePrefix if provided
    if ($prefix && $entity) {
        // For tour routes, handle the mixed parameter naming
        if ($prefix === 'tour') {
            $show_route = $show_route ?? route('tour.show', ['tour' => $entity->id]);
            $edit_route = $edit_route ?? route('tour.edit', ['tour' => $entity->id]);
            $delete_route = $delete_route ?? route('tour.destroy', ['id' => $entity->id]);
        } else {
            // Standard resource route handling for other entities
            $show_route = $show_route ?? route($prefix . '.show', [$prefix => $entity->id]);
            $edit_route = $edit_route ?? route($prefix . '.edit', [$prefix => $entity->id]);
            $delete_route = $delete_route ?? route($prefix . '.destroy', [$prefix => $entity->id]);
        }
    }
@endphp

<div class="btn-group" role="group">
    @if(($permissions['show'] ?? false) && isset($show_route))
        <a href="{{ $show_route }}" class="btn btn-info btn-sm" title="View">
            <i class="fa fa-eye"></i>
        </a>
    @endif

    @if(($permissions['edit'] ?? false) && isset($edit_route))
        <a href="{{ $edit_route }}" class="btn btn-warning btn-sm" title="Edit">
            <i class="fa fa-edit"></i>
        </a>
    @endif

    @if(($permissions['destroy'] ?? false) && isset($delete_route))
        <button type="button" class="btn btn-danger btn-sm delete-btn"
                data-url="{{ $delete_route }}"
                title="Delete"
                onclick="confirmDelete(this)">
            <i class="fa fa-trash"></i>
        </button>
    @endif
</div>

<script>
function confirmDelete(button) {
    if (confirm('Are you sure you want to delete this item?')) {
        const url = button.getAttribute('data-url');

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting item');
        });
    }
}
</script>