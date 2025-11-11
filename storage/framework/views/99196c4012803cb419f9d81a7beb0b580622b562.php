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
                $edit_route = route('users.edit', ['user' => $entity->id]);
                $delete_route = route('users.destroy', ['user' => $entity->id]);
                $delete_msg_url = "/users/{$entity->id}/deleteMsg";
            } elseif ($prefix === 'announcements') {
                // Special handling for announcements routes
                $edit_route = route('announcements.edit', ['announcement' => $entity->id]);
                $delete_route = route('announcements.destroy', ['announcement' => $entity->id]);
                $delete_msg_url = "/announcements/{$entity->id}/deleteMsg";
            } else {
                // Generic route handling
                try {
                    $show_route = route($prefix . '.show', [$prefix => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/{$prefix}/{$entity->id}/show";
                }
                
                try {
                    $edit_route = route($prefix . '.edit', [$prefix => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/{$prefix}/{$entity->id}/edit";
                }
                
                try {
                    $delete_route = route($prefix . '.destroy', [$prefix => $entity->id]);
                } catch (Exception $e) {
                    $delete_route = "/{$prefix}/{$entity->id}/delete";
                }
                
                $delete_msg_url = "/{$prefix}/{$entity->id}/deleteMsg";
            }
        } catch (Exception $e) {
            // Route doesn't exist, provide conventional URI fallbacks
            if ($entity) {
                $show_route = "/{$prefix}/{$entity->id}/show";
                $edit_route = "/{$prefix}/{$entity->id}/edit";
                $delete_route = "/{$prefix}/{$entity->id}/delete";
                $delete_msg_url = "/{$prefix}/{$entity->id}/deleteMsg";
            }
        }
    }

    // Determine what actions are allowed
    $canShow = $show_route && ($permissions['show'] ?? true);
    $canEdit = $edit_route && ($permissions['edit'] ?? true);
    $canDelete = $delete_route && ($permissions['destroy'] ?? true);
    
    // Always allow delete for transfer
    if ($prefix === 'transfer') {
        $canDelete = true;
    }
    
    // For bus, ensure actions show (fallback to conventional URIs and permit buttons)
    if ($prefix === 'bus') {
        $canShow = (bool) $show_route;
        $canEdit = (bool) $edit_route;
        $canDelete = (bool) $delete_route;
    }
    
    $canClone = ($prefix === 'tour') && Auth::check() && Auth::user()->can('tour.create');
?>

<div class="btn-list flex-nowrap">
    <?php if($canShow): ?>
        <a href="<?php echo e($show_route); ?>" class="btn btn-sm btn-warning" title="<?php echo e(trans('main.View') ?? 'View'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
            </svg>
        </a>
    <?php endif; ?>

    <?php if($canEdit): ?>
        <a href="<?php echo e($edit_route); ?>" class="btn btn-sm btn-primary" title="<?php echo e(trans('main.Edit') ?? 'Edit'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                <path d="M16 5l3 3" />
            </svg>
        </a>
    <?php endif; ?>

    <?php if($canDelete): ?>
        <button type="button" 
                class="btn btn-sm btn-danger delete-action-btn"
                data-bs-toggle="modal"
                data-bs-target="#deleteModal"
                data-delete-url="<?php echo e($delete_msg_url); ?>"
                data-entity-id="<?php echo e($entity->id ?? ''); ?>"
                title="<?php echo e(trans('main.Delete') ?? 'Delete'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 7l16 0" />
                <path d="M10 11l0 6" />
                <path d="M14 11l0 6" />
                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
            </svg>
        </button>
    <?php endif; ?>

    <?php if($canClone): ?>
        <button type="button" 
                class="btn btn-sm btn-success clone-tour-button"
                data-bs-toggle="modal"
                data-bs-target="#tour-clone-modal"
                data-id="<?php echo e($entity->id); ?>"
                title="<?php echo e(trans('main.Clone') ?? 'Clone'); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M8 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z" />
                <path d="M16 8v-2a2 2 0 0 0 -2 -2h-8a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h2" />
            </svg>
        </button>
    <?php endif; ?>
</div>


<?php if (! $__env->hasRenderedOnce('715350a4-5ad2-4d20-a794-6c6282341acf')): $__env->markAsRenderedOnce('715350a4-5ad2-4d20-a794-6c6282341acf'); ?>
<div class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 9v2m0 4v.01" />
                    <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
                </svg>
                <h3><?php echo e(trans('main.Warning') ?? 'Warning'); ?>!</h3>
                <div class="text-secondary" id="deleteModalMessage">
                    <?php echo e(trans('main.WouldyouliketoremoveThis') ?? 'Would you like to remove this item?'); ?>?
                </div>
            </div>
            <div class="modal-footer">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                <?php echo e(trans('main.Cancel') ?? 'Cancel'); ?>

                            </button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-danger w-100" id="confirmDeleteBtn">
                                <?php echo e(trans('main.Delete') ?? 'Delete'); ?>

                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let deleteUrl = '';
    
    // Handle delete button clicks
    document.querySelectorAll('.delete-action-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent row click if in a table
            deleteUrl = this.getAttribute('data-delete-url');
            
            // Load the delete confirmation message via AJAX if deleteMsg endpoint exists
            if (deleteUrl) {
                fetch(deleteUrl, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // If Ajaxis returns custom message, update modal
                    if (data.message) {
                        document.getElementById('deleteModalMessage').innerHTML = data.message;
                    }
                    if (data.link) {
                        deleteUrl = data.link;
                    }
                })
                .catch(error => {
                    console.log('Using default delete confirmation');
                });
            }
        });
    });
    
    // Handle confirm delete button
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteUrl) {
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteUrl;
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.content;
                form.appendChild(csrfInput);
            }
            
            // Add DELETE method
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
});
</script>
<?php endif; ?><?php /**PATH D:\xamppp\htdocs\dev-eetstravel\resources\views/component/action_buttons.blade.php ENDPATH**/ ?>