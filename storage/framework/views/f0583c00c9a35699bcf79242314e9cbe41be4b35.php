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
    if ($prefix && $entity) {
        try {
            if ($prefix === 'tour') {
                $show_route = route('tour.show', ['tour' => $entity->id]);
                $edit_route = route('tour.edit', ['tour' => $entity->id]);
                $delete_route = route('tour.destroy', ['id' => $entity->id]);
            } elseif ($prefix === 'users') {
                // Special handling for users routes
                $show_route = route('users.show', ['user' => $entity->id]);
                $edit_route = route('users.edit', ['user' => $entity->id]);
                $delete_route = route('users.destroy', ['user' => $entity->id]);
            } elseif ($prefix === 'announcements') {
                // Special handling for announcements routes
                $edit_route = route('announcements.edit', ['announcement' => $entity->id]);
                $delete_route = route('announcements.destroy', ['announcement' => $entity->id]);
            } else {
                $show_route = route($prefix . '.show', [$prefix => $entity->id]);
                $edit_route = route($prefix . '.edit', [$prefix => $entity->id]);
                $delete_route = route($prefix . '.destroy', [$prefix => $entity->id]);
            }
        } catch (Exception $e) {
            // Route doesn't exist, keep null
        }
    }

    // Determine what actions are allowed
    $canShow = false; // Hide view button for all pages
    $canEdit = $edit_route && ($permissions['edit'] ?? true);
    $canDelete = $delete_route && ($permissions['destroy'] ?? true);
?>

<div class="btn-list flex-nowrap">
    <?php if($canShow): ?>
        <a href="<?php echo e($show_route); ?>" class="btn btn-icon btn-ghost-primary" title="View">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                <path d="M22 12c-2.667 4 -6 6 -10 6s-7.333 -2 -10 -6c2.667 -4 6 -6 10 -6s7.333 2 10 6" />
            </svg>
        </a>
    <?php endif; ?>

    <?php if($canEdit): ?>
        <a href="<?php echo e($edit_route); ?>" class="btn btn-icon btn-ghost-warning" title="Edit">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                <path d="M16 5l3 3" />
            </svg>
        </a>
    <?php endif; ?>

    <?php if($canDelete): ?>
        <button type="button" class="btn btn-icon btn-ghost-danger delete-btn"
                data-url="<?php echo e($delete_route); ?>"
                title="Delete">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 7l16 0" />
                <path d="M10 11l0 6" />
                <path d="M14 11l0 6" />
                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                <path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" />
            </svg>
        </button>
    <?php endif; ?>
</div><?php /**PATH /var/www/html/resources/views/component/action_buttons.blade.php ENDPATH**/ ?>