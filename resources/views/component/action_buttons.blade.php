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
            } elseif ($prefix === 'tour_package') {
                try {
                    $show_route = route('tour_package.show', ['tour_package' => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/tour_package/{$entity->id}";
                }
                try {
                    $edit_route = route('tour_package.edit', ['tour_package' => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/tour_package/{$entity->id}/edit";
                }
                try {
                    $delete_route = route('tour_package.destroy', ['id' => $entity->id]);
                } catch (Exception $e) {
                    $delete_route = "/tour_package/{$entity->id}/delete";
                }
            } elseif ($prefix === 'transfer') {
                $show_route = route('transfer.show', ['transfer' => $entity->id]);
                $edit_route = route('transfer.edit', ['transfer' => $entity->id]);
                $delete_route = route('transfer.destroy', ['id' => $entity->id]);
            } elseif ($prefix === 'bus') {
                try {
                    $show_route = route('bus.show', ['bu' => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/bus/{$entity->id}";
                }
                try {
                    $edit_route = route('bus.edit', ['bu' => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/bus/{$entity->id}/edit";
                }
                $delete_route = route('bus.destroy', ['id' => $entity->id]);
            } elseif ($prefix === 'driver') {
                try {
                    $show_route = route('driver.show', ['driver' => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/driver/{$entity->id}";
                }
                try {
                    $edit_route = route('driver.edit', ['driver' => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/driver/{$entity->id}/edit";
                }
                $delete_route = route('driver.destroy', ['id' => $entity->id]);
            } elseif ($prefix === 'status') {
                try {
                    $show_route = route('status.show', ['status' => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/status/{$entity->id}";
                }
                try {
                    $edit_route = route('status.edit', ['status' => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/status/{$entity->id}/edit";
                }
                $delete_route = route('status.destroy', ['id' => $entity->id]);
            } elseif ($prefix === 'room_types') {
                try {
                    $show_route = route('room_types.show', ['room_type' => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/room_types/{$entity->id}";
                }
                try {
                    $edit_route = route('room_types.edit', ['room_type' => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/room_types/{$entity->id}/edit";
                }
                $delete_route = route('room_types.destroy', ['id' => $entity->id]);
            } elseif ($prefix === 'rate') {
                try {
                    $show_route = route('rate.show', ['rate' => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/rate/{$entity->id}";
                }
                try {
                    $edit_route = route('rate.edit', ['rate' => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/rate/{$entity->id}/edit";
                }
                $delete_route = route('rate.destroy', ['id' => $entity->id]);
            } elseif ($prefix === 'currency_rate') {
                try {
                    $show_route = route('currency_rate.show', ['currency_rate' => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/currency_rate/{$entity->id}";
                }
                try {
                    $edit_route = route('currency_rate.edit', ['currency_rate' => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/currency_rate/{$entity->id}/edit";
                }
                $delete_route = route('currency_rate.destroy', ['id' => $entity->id]);
            } elseif ($prefix === 'currencies') {
                try {
                    $show_route = route('currencies.show', ['currency' => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/currencies/{$entity->id}";
                }
                try {
                    $edit_route = route('currencies.edit', ['currency' => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/currencies/{$entity->id}/edit";
                }
                $delete_route = route('currencies.destroy', ['id' => $entity->id]);
            } elseif ($prefix === 'criteria') {
                try {
                    $show_route = route('criteria.show', ['criterion' => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/criteria/{$entity->id}";
                }
                try {
                    $edit_route = route('criteria.edit', ['criterion' => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/criteria/{$entity->id}/edit";
                }
                $delete_route = route('criteria.destroy', ['id' => $entity->id]);
            } elseif ($prefix === 'notifications') {
                $show_route = $entity->link ?? null;
                $edit_route = null;
                $delete_route = route('notifications.destroy', ['id' => $entity->id]);
            } elseif ($prefix === 'comment') {
                try {
                    $show_route = route('comment.show', ['comment' => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/comment/{$entity->id}";
                }
                try {
                    $edit_route = route('comment.edit', ['comment' => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/comment/{$entity->id}/edit";
                }
                $delete_route = "/comment/{$entity->id}/delete_msg";
            } elseif ($prefix === 'clients') {
                try {
                    $show_route = route('clients.show', ['client' => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/clients/{$entity->id}";
                }
                try {
                    $edit_route = route('clients.edit', ['client' => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/clients/{$entity->id}/edit";
                }
                $delete_route = route('client.destroy', ['id' => $entity->id]);
            } elseif ($prefix === 'office') {
                try {
                    $show_route = route('office.show', ['office' => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/office/{$entity->id}";
                }
                try {
                    $edit_route = route('office.edit', ['office' => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/office/{$entity->id}/edit";
                }
                $delete_route = route('office.destroy', ['id' => $entity->id]);
            } elseif ($prefix === 'invoices') {
                try {
                    $show_route = route('invoices.show', ['invoice' => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/invoices/{$entity->id}";
                }
                try {
                    $edit_route = route('invoices.edit', ['invoice' => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/invoices/{$entity->id}/edit";
                }
                $delete_route = route('invoices.destroy', ['id' => $entity->id]);
            } elseif ($prefix === 'taxes') {
                try {
                    $show_route = route('taxes.show', ['tax' => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/taxes/{$entity->id}";
                }
                try {
                    $edit_route = route('taxes.edit', ['tax' => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/taxes/{$entity->id}/edit";
                }
                $delete_route = "/taxes/{$entity->id}/deleteMsg";
            } elseif ($prefix === 'accounting') {
                try {
                    $show_route = route('accounting.show', ['accounting' => $entity->id]);
                } catch (Exception $e) {
                    $show_route = "/accounting/{$entity->id}";
                }
                try {
                    $edit_route = route('accounting.edit', ['accounting' => $entity->id]);
                } catch (Exception $e) {
                    $edit_route = "/accounting/{$entity->id}/edit";
                }
                $delete_route = route('accounts.destroy', ['id' => $entity->id]);
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
    if ($prefix === 'notifications') {
        $canShow = false;
        $canEdit = false;
        $canDelete = true;
    }
    
    $canClone = ($prefix === 'tour') && Auth::check() && Auth::user()->can('tour.create');

    $deleteMethodOverrides = [
        'announcements' => 'DELETE',
        'users' => 'DELETE',
        'notifications' => 'GET',
        'tour' => 'GET',
        'tour_package' => 'GET',
        'transfer' => 'GET',
        'bus' => 'GET',
        'driver' => 'GET',
        'status' => 'GET',
        'room_types' => 'GET',
        'rate' => 'GET',
        'currency_rate' => 'GET',
        'currencies' => 'GET',
        'criteria' => 'GET',
        'comment' => 'GET',
        'clients' => 'GET',
        'office' => 'GET',
        'invoices' => 'GET',
        'taxes' => 'GET',
        'accounting' => 'GET',
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
        <a href="javascript:void(0);" 
           class="btn btn-sm btn-danger action-delete-btn"
           data-delete-url="{{ $delete_route }}"
           data-delete-method="{{ $delete_method }}"
           data-entity-name="{{ $entity->content ?? $entity->name ?? 'item' }}"
           onclick="confirmDelete(this); return false;"
           title="{{ trans('main.Delete') ?? 'Delete' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
            </svg>
        </a>
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

@once
@push('scripts')
<script>
// Simple confirm and delete function
function confirmDelete(element) {
    const deleteUrl = element.dataset.deleteUrl;
    const entityName = element.dataset.entityName || 'this item';
    const deleteMethod = element.dataset.deleteMethod || 'GET';
    
    console.log('=== DELETE DEBUG ===');
    console.log('Delete URL:', deleteUrl);
    console.log('Entity Name:', entityName);
    console.log('Method:', deleteMethod);
    
    if (confirm(`Are you sure you want to delete "${entityName}"?`)) {
        console.log('User confirmed, processing delete...');
        
        if (deleteMethod === 'GET') {
            // For GET method, just navigate
            window.location.href = deleteUrl;
        } else {
            // For other methods, use fetch
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            fetch(deleteUrl, {
                method: deleteMethod,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error deleting item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting item. Please try again.');
            });
        }
    } else {
        console.log('User cancelled');
    }
}

// Universal delete handler for action buttons (backup method)
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.action-delete-btn');
        if (!deleteBtn || deleteBtn.hasAttribute('onclick')) return;

        e.preventDefault();
        e.stopPropagation();

        const deleteUrl = deleteBtn.dataset.deleteUrl;
        const deleteMethod = deleteBtn.dataset.deleteMethod || 'GET';
        const entityName = deleteBtn.dataset.entityName || 'this item';

        if (!confirm(`Are you sure you want to delete "${entityName}"?`)) {
            return;
        }

        deleteBtn.style.opacity = '0.6';
        deleteBtn.style.pointerEvents = 'none';

        if (deleteMethod === 'GET') {
            window.location.href = deleteUrl;
            return;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(deleteUrl, {
            method: deleteMethod,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting item. Please try again.');
            deleteBtn.style.opacity = '1';
            deleteBtn.style.pointerEvents = 'auto';
        });
    });
});
</script>
@endpush
@endonce