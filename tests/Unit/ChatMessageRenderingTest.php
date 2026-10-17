<?php

namespace Tests\Unit;

use App\ChatMessage;
use App\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ChatMessageRenderingTest extends TestCase
{
    public function test_legacy_avatars_and_missing_authors_render_without_attachment_calls(): void
    {
        $viewer = new User(); $viewer->id = 1;
        Auth::setUser($viewer);
        foreach ([1, 2, null] as $authorId) {
            $author = $authorId ? new User() : null;
            if ($author) $author->forceFill(['id' => $authorId, 'name' => 'QA author', 'avatar_file_name' => 'legacy.png']);
            $message = new ChatMessage(['message' => '<script>alert(1)</script>']);
            $message->setRelation('author', $author);
            $html = view('chats.component.message', compact('message'))->render();
            $this->assertStringNotContainsString('<script>', $html);
            $this->assertStringContainsString('direct-chat-img', $html);
            if (!$author) $this->assertStringContainsString('Former user', $html);
        }
    }
}
