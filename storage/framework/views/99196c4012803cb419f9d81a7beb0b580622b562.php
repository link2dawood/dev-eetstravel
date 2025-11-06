<?php
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

    // Initialize route variables
    $show_route = null;
    $edit_route = null;
    $delete_route = null;

    // Build routes based on routePrefix if provided
    $delete_msg_url = null;
    if ($prefix && $entity) {
        try {
            if ($prefix === 'tour') {
                $show_route = route('tour.show', ['tour' => $entity->id]);
                $edit_route = route('tour.edit', ['tour' => $entity->id]);
                $delete_route = route('tour.destroy', ['id' => $entity->id]);
                $delete_msg_url = "/tour/{$entity->id}/deleteMsg";
            } elseif ($prefix === 'users') {
                // Special handling for users routes
                $show_route = route('users.show', ['user' => $entity->id]);
                $edit_route = route('users.edit', ['user' => $entity->id]);
                $delete_route = route('users.destroy', ['user' => $entity->id]);
                $delete_msg_url = "/users/{$entity->id}/deleteMsg";
            } elseif ($prefix === 'announcements') {
                // Special handling for announcements routes
                $edit_route = route('announcements.edit', ['announcement' => $entity->id]);
                $delete_route = route('announcements.destroy', ['announcement' => $entity->id]);
                $delete_msg_url = "/announcements/{$entity->id}/deleteMsg";
            } else {
                $show_route = route($prefix . '.show', [$prefix => $entity->id]);
                $edit_route = route($prefix . '.edit', [$prefix => $entity->id]);
                $delete_route = route($prefix . '.destroy', [$prefix => $entity->id]);
                $delete_msg_url = "/{$prefix}/{$entity->id}/deleteMsg";
            }
        } catch (Exception $e) {
            // Route doesn't exist, keep null
        }
    }

    // Determine what actions are allowed
    $canShow = $show_route && ($permissions['show'] ?? true);
    $canEdit = $edit_route && ($permissions['edit'] ?? true);
    $canDelete = $delete_route && ($permissions['destroy'] ?? true);
    $canClone = ($prefix === 'tour') && Auth::check() && Auth::user()->can('tour.create');
?>

<div class="btn-list flex-nowrap">
    <?php if($canShow): ?>
        <a href="<?php echo e($show_route); ?>" class="btn btn-sm btn-warning" title="View">
            <i class="ti ti-eye"></i>
        </a>
    <?php endif; ?>

    <?php if($canEdit): ?>
        <a href="<?php echo e($edit_route); ?>" class="btn btn-sm btn-primary" title="Edit">
            <i class="ti ti-edit"></i>
        </a>
    <?php endif; ?>

    <?php if($canDelete): ?>
        <button type="button" class="btn btn-sm btn-danger delete"
                data-bs-toggle="modal"
                data-bs-target="#myModal"
                data-link="<?php echo e($delete_msg_url ?? $delete_route); ?>"
                title="Delete">
            <i class="ti ti-trash"></i>
        </button>
    <?php endif; ?>

    <?php if($canClone): ?>
        <button type="button" class="btn btn-sm btn-success clone-tour-button"
                data-bs-toggle="modal"
                data-bs-target="#tour-clone-modal"
                data-id="<?php echo e($entity->id); ?>"
                title="Clone">
            <i class="ti ti-copy"></i>
        </button>
    <?php endif; ?>
</div><?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/component/action_buttons.blade.php ENDPATH**/ ?>