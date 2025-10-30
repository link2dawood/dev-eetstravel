// Monday.com Style Task Management JavaScript

// Toggle group visibility
function toggleGroup(groupName) {
    const group = document.querySelector(`[data-group="${groupName}"]`);
    const content = document.getElementById(`group-${groupName}`);

    if (content.style.display === 'none') {
        content.style.display = 'block';
        group.classList.remove('monday-group-collapsed');
    } else {
        content.style.display = 'none';
        group.classList.add('monday-group-collapsed');
    }
}

// Open/close modals
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';

        // Reset form if it exists
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('monday-modal')) {
        closeModal(event.target.id);
    }
});

// New Task Button
document.getElementById('newTaskBtn')?.addEventListener('click', function() {
    openModal('quickAddModal');
});

// Quick Add Task
function openQuickAdd(group) {
    openModal('quickAddModal');
    // Could set default status based on group
}

function saveQuickTask() {
    const form = document.getElementById('quickAddForm');
    const formData = new FormData(form);

    // Validate required fields
    if (!formData.get('content') || !formData.get('status') || !formData.get('end_date') || !formData.get('end_time')) {
        alert('Please fill in all required fields');
        return;
    }

    // Show loading state
    const saveBtn = event.target;
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<div class="monday-spinner" style="width: 16px; height: 16px; border-width: 2px;"></div>';
    saveBtn.disabled = true;

    // Send AJAX request
    fetch('/task', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': formData.get('_token'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.route) {
            // Reload page to show new task
            window.location.href = data.route || window.location.href;
        } else {
            alert('Task created successfully!');
            closeModal('quickAddModal');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating task. Please try again.');
    })
    .finally(() => {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    });
}

// Inline Editing
let currentEditingElement = null;

function editInline(element) {
    // If already editing, don't start another edit
    if (currentEditingElement) {
        saveInlineEdit(currentEditingElement);
    }

    const field = element.dataset.field;
    const taskId = element.dataset.taskId;
    const currentValue = element.textContent.trim();

    currentEditingElement = element;

    // Create input field
    const input = document.createElement('input');
    input.type = field === 'story_points' ? 'number' : 'text';
    input.className = 'monday-editable-input';
    input.value = currentValue;

    // Replace element with input
    element.innerHTML = '';
    element.appendChild(input);
    input.focus();
    input.select();

    // Save on blur or enter key
    input.addEventListener('blur', () => saveInlineEdit(element));
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            saveInlineEdit(element);
        } else if (e.key === 'Escape') {
            cancelInlineEdit(element, currentValue);
        }
    });
}

function saveInlineEdit(element) {
    const input = element.querySelector('input');
    if (!input) return;

    const field = element.dataset.field;
    const taskId = element.dataset.taskId;
    const newValue = input.value.trim();
    const oldValue = input.defaultValue;

    // If no change, just restore
    if (newValue === oldValue) {
        element.textContent = oldValue;
        currentEditingElement = null;
        return;
    }

    // Update UI immediately
    element.textContent = newValue || '-';
    currentEditingElement = null;

    // Send AJAX update
    updateTaskField(taskId, field, newValue);
}

function cancelInlineEdit(element, originalValue) {
    element.textContent = originalValue;
    currentEditingElement = null;
}

// Update task field via AJAX
function updateTaskField(taskId, field, value) {
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    formData.append('field', field);
    formData.append('value', value);

    fetch(`/task/${taskId}/update-field`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Field updated successfully');
    })
    .catch(error => {
        console.error('Error updating field:', error);
        alert('Error updating task. Please refresh the page.');
    });
}

// Toggle task complete/incomplete
function toggleTaskComplete(taskId) {
    const checkbox = event.target;
    const row = document.querySelector(`tr[data-task-id="${taskId}"]`);

    // Find a "completed" status
    const completedStatusId = document.querySelector('#quick_status option')?.value || 1;

    if (checkbox.checked) {
        row.classList.add('completed');
        // Update status to completed
        updateTaskStatus(taskId, completedStatusId);
    } else {
        row.classList.remove('completed');
        // Update status to pending/in-progress
        updateTaskStatus(taskId, completedStatusId);
    }
}

// Toggle priority
function togglePriority(taskId, event) {
    event.stopPropagation();
    const icon = event.target.closest('svg');
    const isFilled = icon.getAttribute('fill') === 'currentColor';

    if (isFilled) {
        // Remove priority
        icon.setAttribute('fill', 'none');
        icon.setAttribute('stroke', 'currentColor');
        icon.setAttribute('stroke-width', '2');
        icon.style.opacity = '0.3';
        updateTaskField(taskId, 'priority', 0);
    } else {
        // Add priority
        icon.setAttribute('fill', 'currentColor');
        icon.removeAttribute('stroke');
        icon.removeAttribute('stroke-width');
        icon.style.opacity = '1';
        updateTaskField(taskId, 'priority', 1);
    }
}

// Status Picker
function openStatusPicker(taskId, element) {
    // Close any existing status pickers
    document.querySelectorAll('.monday-dropdown-menu').forEach(menu => menu.remove());

    // Get available statuses from the page
    const statusOptions = Array.from(document.querySelectorAll('#quick_status option')).map(opt => ({
        id: opt.value,
        name: opt.textContent,
        color: getStatusColor(opt.textContent)
    }));

    // Create dropdown menu
    const dropdown = document.createElement('div');
    dropdown.className = 'monday-dropdown-menu show';
    dropdown.style.position = 'absolute';
    dropdown.style.top = (element.offsetTop + element.offsetHeight) + 'px';
    dropdown.style.left = element.offsetLeft + 'px';

    statusOptions.forEach(status => {
        const item = document.createElement('div');
        item.className = 'monday-dropdown-item';
        item.style.backgroundColor = status.color + '20';
        item.style.color = status.color;
        item.textContent = status.name;
        item.onclick = () => {
            updateTaskStatus(taskId, status.id);
            element.textContent = status.name;
            element.style.backgroundColor = status.color + '20';
            element.style.color = status.color;
            dropdown.remove();
        };
        dropdown.appendChild(item);
    });

    element.parentElement.appendChild(dropdown);

    // Close on click outside
    setTimeout(() => {
        document.addEventListener('click', function closeDropdown(e) {
            if (!dropdown.contains(e.target) && e.target !== element) {
                dropdown.remove();
                document.removeEventListener('click', closeDropdown);
            }
        });
    }, 10);
}

function getStatusColor(statusName) {
    const colors = {
        'Pending': '#fdab3d',
        'In Progress': '#0073ea',
        'Completed': '#00c875',
        'Aborted': '#e2445c',
        'Working': '#0073ea',
        'Stuck': '#e2445c'
    };
    return colors[statusName] || '#6c757d';
}

function updateTaskStatus(taskId, statusId) {
    fetch(`/task/${taskId}/update`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            _token: document.querySelector('meta[name="csrf-token"]').content,
            newStatus: statusId
        })
    })
    .then(response => {
        if (response.ok) {
            console.log('Status updated successfully');
        } else {
            throw new Error('Failed to update status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating status. Please refresh the page.');
    });
}

// Person Picker
function openPersonPicker(taskId) {
    // For now, redirect to edit page
    // In a full implementation, this would show a modal with user selection
    editTask(taskId);
}

// Date Picker
function openDatePicker(taskId) {
    // For now, redirect to edit page
    // In a full implementation, this would show a date picker inline
    editTask(taskId);
}

// Edit Task
function editTask(taskId) {
    window.location.href = `/task/${taskId}/edit`;
}

// Delete Task
function deleteTask(taskId) {
    if (!confirm('Are you sure you want to delete this task?')) {
        return;
    }

    fetch(`/task/${taskId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        // Remove the row from the table
        const row = document.querySelector(`tr[data-task-id="${taskId}"]`);
        if (row) {
            row.style.opacity = '0';
            row.style.transform = 'translateX(-20px)';
            setTimeout(() => row.remove(), 300);
        }

        // Update group counts
        updateGroupCounts();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting task');
    });
}

// Search functionality
document.getElementById('taskSearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.monday-table-row');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Update group counts
function updateGroupCounts() {
    document.querySelectorAll('.monday-group').forEach(group => {
        const groupName = group.dataset.group;
        const content = group.querySelector('.monday-group-content');
        const rows = content.querySelectorAll('.monday-table-row:not(.monday-add-task-row)');
        const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
        const countBadge = group.querySelector('.monday-group-count');
        if (countBadge) {
            countBadge.textContent = visibleRows.length;
        }
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Set default date to today in quick add form
    const today = new Date().toISOString().split('T')[0];
    const dateInput = document.getElementById('quick_due_date');
    if (dateInput && !dateInput.value) {
        dateInput.value = today;
    }

    // Add transition effects
    document.querySelectorAll('.monday-table-row').forEach(row => {
        row.style.transition = 'all 0.3s ease';
    });
});
