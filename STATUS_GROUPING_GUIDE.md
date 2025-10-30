# Task Status Grouping Guide

## Overview
Tasks are now automatically grouped in the monday.com-style interface based on their status type.

## Status Groups

### 1. **To-Do Group** (Blue - #0073ea)
Contains tasks with statuses where:
- `is_completed = 0` (false)
- `is_aborted = 0` (false)

**Current Status:** Pending

These are active tasks that need to be worked on.

### 2. **Completed Group** (Green - #00c875)
Contains tasks with statuses where:
- `is_completed = 1` (true)

**Current Status:** Completed

These are finished tasks that have been successfully completed.

### 3. **Aborted Group** (Red - #e2445c)
Contains tasks with statuses where:
- `is_aborted = 1` (true)

**Current Status:** Abort

These are tasks that were cancelled or aborted.

## Current Status Configuration

| ID | Status Name | is_completed | is_aborted | Displays In |
|----|-------------|--------------|------------|-------------|
| 2  | Pending     | 0            | 0          | To-Do       |
| 7  | Completed   | 1            | 0          | Completed   |
| 8  | Abort       | 0            | 1          | Aborted     |

## Adding New Statuses

To add a new status and assign it to a group:

### Option 1: Via Database (Recommended)
```sql
-- Add a new "In Progress" status to To-Do group
INSERT INTO status (name, type, color, is_completed, is_aborted, sort_order, status, created_at, updated_at)
VALUES ('In Progress', 'task', '#0073ea', 0, 0, 3, 1, NOW(), NOW());

-- Add a new "On Hold" status to To-Do group
INSERT INTO status (name, type, color, is_completed, is_aborted, sort_order, status, created_at, updated_at)
VALUES ('On Hold', 'task', '#fdab3d', 0, 0, 4, 1, NOW(), NOW());

-- Add a new "Cancelled" status to Aborted group
INSERT INTO status (name, type, color, is_completed, is_aborted, sort_order, status, created_at, updated_at)
VALUES ('Cancelled', 'task', '#e2445c', 0, 1, 5, 1, NOW(), NOW());
```

### Option 2: Via Tinker
```bash
php artisan tinker
```

```php
// Create a new "In Progress" status for To-Do
App\Status::create([
    'name' => 'In Progress',
    'type' => 'task',
    'color' => '#0073ea',
    'is_completed' => false,
    'is_aborted' => false,
    'sort_order' => 3,
    'status' => 1
]);

// Create a new "Done" status for Completed
App\Status::create([
    'name' => 'Done',
    'type' => 'task',
    'color' => '#00c875',
    'is_completed' => true,
    'is_aborted' => false,
    'sort_order' => 4,
    'status' => 1
]);
```

### Option 3: Update Existing Status
```bash
php artisan tinker
```

```php
// Move "Pending" to Completed group
$status = App\Status::where('name', 'Pending')->first();
$status->is_completed = true;
$status->save();

// Move a status to Aborted group
$status = App\Status::where('name', 'Cancelled')->first();
$status->is_aborted = true;
$status->save();
```

## Status Color Recommendations

### To-Do Statuses
- **Pending**: `#fdab3d` (Orange)
- **In Progress**: `#0073ea` (Blue)
- **On Hold**: `#f59f00` (Yellow)
- **Under Review**: `#a25ddc` (Purple)

### Completed Statuses
- **Completed**: `#00c875` (Green)
- **Done**: `#2fb344` (Dark Green)
- **Verified**: `#00ca72` (Bright Green)

### Aborted Statuses
- **Abort**: `#e2445c` (Red)
- **Cancelled**: `#d63939` (Dark Red)
- **Rejected**: `#f03e3e` (Bright Red)

## How Tasks Are Filtered

The monday.com interface uses the following logic to display tasks:

```php
// To-Do Group
$tasks->filter(function($task) {
    return $task->status && !$task->status->is_completed && !$task->status->is_aborted;
})

// Completed Group
$tasks->filter(function($task) {
    return $task->status && $task->status->is_completed;
})

// Aborted Group
$tasks->filter(function($task) {
    return $task->status && $task->status->is_aborted;
})
```

## Troubleshooting

### Tasks not appearing in the correct group?
1. Check the task's status ID:
   ```sql
   SELECT id, content, status FROM tasks WHERE id = YOUR_TASK_ID;
   ```

2. Verify the status configuration:
   ```sql
   SELECT * FROM status WHERE id = YOUR_STATUS_ID;
   ```

3. Ensure `is_completed` and `is_aborted` are set correctly

### All tasks showing in To-Do?
- Check if status relationship is loaded: Task must have a valid `status` relationship
- Run: `php artisan cache:clear && php artisan view:clear`

### Tasks disappear when changing status?
- This is normal! Tasks move between groups based on their status
- Check the appropriate group (To-Do, Completed, or Aborted)

## Best Practices

1. **Keep it Simple**: Don't create too many statuses. 3-5 per group is ideal.

2. **Use Colors Wisely**:
   - Blue/Purple for active work
   - Green for completion
   - Red for problems/cancellation
   - Orange/Yellow for waiting/on-hold

3. **Clear Naming**: Use clear, unambiguous status names like "In Progress" not "Working"

4. **Consistent Workflow**: Establish a clear task workflow:
   ```
   Pending → In Progress → Under Review → Completed
                        ↘ Aborted (if cancelled)
   ```

5. **Regular Cleanup**: Periodically archive or delete old completed/aborted tasks

## Migration Applied

The following migration added the status flags:
- **Migration**: `2025_10_23_213109_add_status_flags_to_status_table.php`
- **Columns Added**:
  - `is_completed` (boolean, default: false)
  - `is_aborted` (boolean, default: false)

---

**Last Updated**: October 23, 2025
**Status**: Active and Working
