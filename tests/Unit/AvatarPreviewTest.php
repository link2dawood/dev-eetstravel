<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;

class AvatarPreviewTest extends TestCase
{
    public function test_missing_avatar_uses_escaped_initials_without_broken_image(): void
    {
        $user = new User();
        $user->forceFill(['name' => 'Alice Brown', 'avatar' => 'missing-qa-avatar.png', 'avatar_path' => 'missing-modern-avatar.png']);
        $html = view('component.avatar-preview', ['avatarUser' => $user])->render();
        $this->assertStringContainsString('AB</span>', $html);
        $this->assertStringNotContainsString('<img', $html);
        $user->name = '<script> bad';
        $this->assertStringNotContainsString('<script>', view('component.avatar-preview', ['avatarUser' => $user])->render());
    }

    public function test_existing_legacy_avatar_is_retained_with_failure_fallback(): void
    {
        $path = tempnam(public_path(), 'qa-avatar-');
        try {
            $user = new User();
            $user->forceFill(['name' => 'Alice Brown', 'avatar' => basename($path)]);
            $html = view('component.avatar-preview', ['avatarUser' => $user])->render();
            $this->assertStringContainsString(asset(basename($path)), $html);
            $this->assertStringContainsString('onerror="this.remove()"', $html);
            $this->assertStringContainsString('AB</span>', $html);
        } finally {
            unlink($path);
        }
    }
}
