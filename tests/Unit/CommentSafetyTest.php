<?php
namespace Tests\Unit;

use App\Comment;
use App\User;
use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CommentSafetyTest extends TestCase
{
    private $dispatcher;
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']); DB::purge('sqlite');
        Schema::create('comments', function ($t) { $t->increments('id'); $t->text('content')->nullable(); $t->integer('author_id'); $t->integer('reference_type'); $t->integer('reference_id'); $t->integer('parent')->nullable(); $t->integer('status')->default(0); $t->timestamps(); $t->softDeletes(); });
        Schema::create('users', function ($t) { $t->increments('id'); $t->string('name'); });
        DB::table('users')->insert([['id' => 1, 'name' => 'First Author'], ['id' => 2, 'name' => 'Second Author']]);
        foreach (['tours', 'clients'] as $table) {
            Schema::create($table, function ($t) { $t->increments('id'); $t->string('name'); $t->softDeletes(); });
            DB::table($table)->insert(['id' => 1, 'name' => 'Parent record']);
        }
        Schema::create('files', function ($t) { $t->increments('id'); $t->integer('comment_id')->nullable(); $t->string('attach_file_name')->nullable(); $t->string('attach_content_type')->nullable(); });
        $this->dispatcher = Comment::getEventDispatcher(); Comment::unsetEventDispatcher();
        $this->asUser();
    }
    protected function tearDown(): void { Comment::setEventDispatcher($this->dispatcher); parent::tearDown(); }
    private function asUser($id = 1, $grants = null, $admin = false)
    {
        $grants = $grants ?? ['comment.index', 'comment.show', 'comment.create', 'comment.edit', 'comment.destroy', 'tour.show', 'clients.show'];
        $user = $this->getMockBuilder(User::class)->onlyMethods(['hasPermissionTo', 'hasRole'])->getMock(); $user->setTable('users'); $user->id = $id;
        $user->method('hasPermissionTo')->willReturnCallback(fn($permission) => in_array($permission, $grants, true)); $user->method('hasRole')->willReturn($admin);
        Auth::setUser($user); return $user;
    }
    private function data($extra = []) { return array_replace(['reference_type' => 1, 'reference_id' => 1, 'content' => '<b>Safe text</b>'], $extra); }
    private function create($extra = [])
    {
        (new CommentController)->store(Request::create('/comment', 'POST', $this->data($extra)));
        return Comment::orderByDesc('id')->first();
    }
    public function test_mass_assignment_fields_and_author_cannot_be_spoofed()
    {
        $comment = $this->create(['id' => 9999, 'author_id' => 2, 'status' => 9, 'created_at' => '2000-01-01']);
        $this->assertNotEquals(9999, $comment->id); $this->assertEquals(1, $comment->author_id); $this->assertEquals(0, $comment->status);
        (new CommentController)->update($comment->id, Request::create('/', 'PUT', ['content' => 'Updated', 'author_id' => 2, 'reference_id' => 9999, 'status' => 9]));
        $this->assertEquals(1, $comment->fresh()->author_id); $this->assertEquals(1, $comment->fresh()->reference_id); $this->assertEquals(0, $comment->fresh()->status);
    }
    public function test_other_author_cannot_edit_delete_or_open_edit_form()
    {
        $comment = $this->create(); $this->asUser(2);
        foreach (['update', 'destroy', 'edit', 'deleteMsg'] as $action) {
            try {
                $controller = new CommentController;
                if ($action === 'update') $controller->update($comment->id, Request::create('/', 'PUT', ['content' => 'Forged']));
                elseif ($action === 'destroy') $controller->destroy($comment->id);
                else $controller->$action($comment->id, Request::create('/'));
                $this->fail('Other author write allowed');
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
        }
        $this->assertSame('<b>Safe text</b>', $comment->fresh()->content);
        $this->asUser(2, null, true);
        (new CommentController)->update($comment->id, Request::create('/', 'PUT', ['content' => 'Admin update']));
        $this->assertSame('Admin update', $comment->fresh()->content);
    }
    public function test_thread_requires_comment_and_parent_module_permissions()
    {
        $this->create();
        foreach ([['tour.show'], ['comment.show'], ['comment.show', 'clients.show']] as $grants) {
            $this->asUser(1, $grants);
            try { (new CommentController)->getComments(Request::create('/', 'POST', $this->data())); $this->fail('Thread leaked'); }
            catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
        }
    }
    public function test_unknown_missing_and_cross_record_references_never_persist()
    {
        foreach ([['reference_type' => 999], ['reference_id' => 9999]] as $bad) {
            try { $this->create($bad); $this->fail('Invalid parent accepted'); }
            catch (\Illuminate\Validation\ValidationException $e) { $this->assertSame(0, Comment::count()); }
            catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { $this->assertSame(0, Comment::count()); }
        }
        $parent = $this->create();
        try { $this->create(['reference_type' => 17, 'parent' => $parent->id]); $this->fail('Cross-record reply accepted'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(422, $e->getStatusCode()); $this->assertSame(1, Comment::count()); }
        $legacy = new Comment; $legacy->reference_type = 999; $this->assertNull($legacy->service());
        foreach (Comment::REFERENCE_MODELS as $model) $this->assertTrue(class_exists($model), $model);
    }
    public function test_legacy_and_new_rich_text_are_sanitized_in_roots_and_replies()
    {
        $parent = $this->create(['content' => '<b>Bold</b><script>alert(1)</script><a href="javascript:alert(1)">link</a>']);
        $this->asUser(2); $child = $this->create(['parent' => $parent->id, 'content' => '<i>Reply</i>']);
        DB::table('comments')->where('id', $child->id)->update(['content' => '<svg onload="alert(1)"></svg><b>Legacy</b><img src=x onerror=alert(1)>']);
        $html = (new CommentController)->getComments(Request::create('/', 'POST', $this->data()));
        $this->assertStringContainsString('<b>Bold</b>', $html); $this->assertStringContainsString('<b>Legacy</b>', $html);
        foreach (['<script', 'javascript:', '<svg', '<img', 'onerror'] as $unsafe) $this->assertStringNotContainsString($unsafe, $html);
        $this->assertStringContainsString('First Author', $html); $this->assertStringContainsString('Second Author', $html);
    }
    public function test_archiving_parent_preserves_all_replies_with_placeholder()
    {
        $parent = $this->create(); $this->asUser(2); $child = $this->create(['parent' => $parent->id, 'content' => 'Child reply']);
        $grandchild = $this->create(['parent' => $child->id, 'content' => 'Grandchild reply']); $this->asUser();
        (new CommentController)->destroy($parent->id);
        $this->assertNull(Comment::find($parent->id)); $this->assertTrue(Comment::withTrashed()->find($parent->id)->trashed());
        $this->assertNotNull(Comment::find($child->id)); $this->assertNotNull(Comment::find($grandchild->id));
        $html = (new CommentController)->getComments(Request::create('/', 'POST', $this->data()));
        $this->assertStringContainsString('Comment removed', $html); $this->assertStringContainsString('Grandchild reply', $html); $this->assertStringNotContainsString('<b>Safe text</b>', $html);
    }
    public function test_missing_delete_returns_not_found()
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class); (new CommentController)->destroy(9999);
    }
    public function test_unsafe_uploads_and_blank_content_are_rejected()
    {
        foreach ([[], ['attach' => [UploadedFile::fake()->createWithContent('evil.svg', '<svg><script>x</script></svg>')]], ['attach_bus' => [UploadedFile::fake()->create('evil.html')]]] as $files) {
            try { (new CommentController)->store(Request::create('/', 'POST', $this->data(['content' => '']), [], $files)); $this->fail('Unsafe or empty comment accepted'); }
            catch (\Illuminate\Validation\ValidationException $e) { $this->assertSame(0, Comment::count()); }
        }
    }
    public function test_thread_focus_cannot_escape_reference_and_helpers_are_protected()
    {
        $tourComment = $this->create();
        $clientComment = $this->create(['reference_type' => 17]);
        try { (new CommentController)->getComments(Request::create('/', 'POST', $this->data(['id_comment' => $clientComment->id]))); $this->fail('Cross-reference focus accepted'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(404, $e->getStatusCode()); }
        foreach (['/comment/1/delete_msg' => ['GET', 'comment.destroy'], '/comment/generate-comments' => ['POST', 'comment.show']] as $url => [$method, $permission]) {
            $route = app('router')->getRoutes()->match(Request::create($url, $method)); $this->assertNotNull($route->getName()); $this->assertSame($permission, $route->getAction('permissions'));
        }
    }
    public function test_comment_attachment_reads_and_deletes_require_parent_access_and_ownership()
    {
        $comment = $this->create(); $fileId = DB::table('files')->insertGetId(['comment_id' => $comment->id, 'attach_file_name' => 'not-sent.pdf']);
        $this->asUser(2, ['comment.show', 'comment.edit']);
        try { (new \App\Http\Controllers\FileController)->download($fileId); $this->fail('Private attachment read allowed'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
        $this->asUser(2);
        try { (new \App\Http\Controllers\FileController)->delete($fileId); $this->fail('Another author attachment delete allowed'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) { $this->assertSame(403, $e->getStatusCode()); }
        $this->assertSame(1, DB::table('files')->count());
    }
    public function test_index_excludes_records_from_denied_modules_and_deleted_parents()
    {
        $this->create(); $this->create(['reference_type' => 17]);
        $this->asUser(1, ['comment.index', 'comment.show', 'tour.show']);
        $this->assertSame(1, (new CommentController)->index(Request::create('/'))->getData()['commentsData']->total());
        DB::table('tours')->where('id', 1)->update(['deleted_at' => now()]);
        $this->assertSame(0, (new CommentController)->index(Request::create('/'))->getData()['commentsData']->total());
    }
}
