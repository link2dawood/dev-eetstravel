<?php
namespace App\Services;

use App\Comment;
use App\Helper\PermissionHelper;

class CommentAccess
{
    public function allowed($permission): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        try { return $user->hasPermissionTo($permission); }
        catch (\Throwable $e) { return false; }
    }

    public function modulePermission($type): ?string
    {
        $class = Comment::REFERENCE_MODELS[(int)$type] ?? null;
        return PermissionHelper::$relationsPermissionShow[$class] ?? [19 => 'bus.calendar', 20 => 'templates.show', 21 => 'comparison.show'][(int)$type] ?? null;
    }

    public function reference($type, $id)
    {
        $class = Comment::REFERENCE_MODELS[(int)$type] ?? null;
        abort_unless($class && class_exists($class), 404);
        $record = $class::findOrFail($id);
        $permission = $this->modulePermission($type);
        abort_unless($permission && $this->allowed($permission), 403);
        if (\Illuminate\Support\Facades\Gate::getPolicyFor($record)) abort_unless(auth()->user()->can('view', $record), 403);
        return $record;
    }

    public function comment(Comment $comment, $permission, $write = false)
    {
        abort_unless($this->allowed($permission), 403);
        $this->reference($comment->reference_type, $comment->reference_id);
        if ($write) abort_unless((int)$comment->author_id === (int)auth()->id() || auth()->user()->hasRole('admin'), 403);
    }
}
