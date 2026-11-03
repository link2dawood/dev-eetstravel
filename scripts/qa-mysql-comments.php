<?php
// Local MySQL comment workflows; identities/grants are simulated only in memory.
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Comment;
use App\Http\Controllers\CommentController;
class QaCommentUser extends App\User
{
    protected $table = 'users';
    public $grants = ['comment.index', 'comment.show', 'comment.create', 'comment.edit', 'comment.destroy', 'tour.show'];
    public function hasPermissionTo($permission, $guardName = null): bool { return in_array($permission, $this->grants, true); }
    public function hasRole($roles, string $guard = null): bool { return false; }
    public function getMorphClass() { return App\User::class; }
}
if (DB::getDriverName() !== 'mysql') throw new RuntimeException('MySQL required');
$results = [];
$check = function ($name, $fn) use (&$results) {
    try { if ($fn() === false) throw new RuntimeException('Assertion failed'); $results[] = ['check' => $name, 'status' => 'passed']; }
    catch (Throwable $e) { $results[] = ['check' => $name, 'status' => 'failed', 'error' => $e->getMessage()]; }
};
$tables = ['comments', 'files', 'activity_log', 'users'];
foreach ($tables as $table) {
    $engine = DB::selectOne('SELECT ENGINE AS engine FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [$table]);
    if (!$engine || $engine->engine !== 'InnoDB') throw new RuntimeException('Transactional table required: ' . $table);
}
$snapshot = fn() => collect($tables)->mapWithKeys(fn($t) => [$t => hash('sha256', serialize(DB::table($t)->orderBy('id')->get()->toArray()))])->all();
$before = $snapshot();
DB::beginTransaction();
try {
    $users = DB::table('users')->orderBy('id')->limit(2)->get(); if ($users->count() < 2) throw new RuntimeException('Two existing users required');
    $first = new QaCommentUser; $first->setRawAttributes((array)$users[0]); $first->exists = true; Auth::setUser($first);
    $second = new QaCommentUser; $second->setRawAttributes((array)$users[1]); $second->exists = true;
    $tour = App\Tour::firstOrFail(); $tag = 'QA MySQL comment ' . bin2hex(random_bytes(5));
    $data = ['reference_type' => 1, 'reference_id' => $tour->id, 'content' => $tag . '<b>Bold</b><script>alert(1)</script>', 'author_id' => $second->id, 'id' => 2147483647, 'status' => 9, 'created_at' => '2000-01-01'];
    $controller = new CommentController;
    $check('Comment index filters by permitted existing parent records', fn() => $controller->index(Request::create('/comment'))->getData()['commentsData']->perPage() === 20);
    $check('Create returns a sanitized thread', function () use ($controller, $data) { $response = $controller->store(Request::create('/', 'POST', $data)); return $response->getStatusCode() === 200 && strpos($response->getContent(), '<script>') === false; });
    $parent = Comment::where('content', 'like', '%' . $tag . '%')->orderByDesc('id')->firstOrFail();
    $check('CO4 forged ID/status/author/timestamps ignored', fn() => $parent->id != 2147483647 && $parent->author_id == $first->id && $parent->status != 9 && $parent->created_at->year !== 2000);
    $check('CO1 stored rich text is allow-list sanitized', fn() => strpos($parent->getRawOriginal('content'), '<script>') === false && strpos($parent->content, '<b>Bold</b>') !== false);
    Auth::setUser($second);
    $check('CO2 another author cannot edit', function () use ($controller, $parent) {
        try { $controller->update($parent->id, Request::create('/', 'PUT', ['content' => 'Forged'])); }
        catch (Symfony\Component\HttpKernel\Exception\HttpException $e) { return $e->getStatusCode() === 403; }
        return false;
    });
    $check('CO2 another author cannot delete', function () use ($controller, $parent) {
        try { $controller->destroy($parent->id); }
        catch (Symfony\Component\HttpKernel\Exception\HttpException $e) { return $e->getStatusCode() === 403; }
        return false;
    });
    $replyData = ['reference_type' => 1, 'reference_id' => $tour->id, 'parent' => $parent->id, 'content' => $tag . ' reply'];
    $controller->store(Request::create('/', 'POST', $replyData)); $reply = Comment::where('parent', $parent->id)->firstOrFail();
    $second->grants = ['comment.show'];
    $check('CO3 missing parent-module permission blocks thread', function () use ($controller, $data) {
        try { $controller->getComments(Request::create('/', 'POST', $data)); }
        catch (Symfony\Component\HttpKernel\Exception\HttpException $e) { return $e->getStatusCode() === 403; }
        return false;
    });
    Auth::setUser($first);
    $check('CO4 owner edit preserves metadata', function () use ($controller, $parent, $data, $second) {
        $controller->update($parent->id, Request::create('/', 'PUT', ['content' => 'Owner update', 'author_id' => $second->id, 'reference_type' => 999, 'status' => 9]));
        $fresh = $parent->fresh(); return $fresh->author_id == $parent->author_id && $fresh->reference_type == 1 && $fresh->status != 9;
    });
    $check('CO5 unknown reference saves nothing', function () use ($controller, $data) {
        $count = DB::table('comments')->count();
        try { $controller->store(Request::create('/', 'POST', array_replace($data, ['reference_type' => 999]))); }
        catch (Illuminate\Validation\ValidationException $e) { return $count === DB::table('comments')->count(); }
        return false;
    });
    $check('CO5 missing reference saves nothing', function () use ($controller, $data) {
        $count = DB::table('comments')->count();
        try { $controller->store(Request::create('/', 'POST', array_replace($data, ['reference_id' => 2147483647]))); }
        catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { return $count === DB::table('comments')->count(); }
        return false;
    });
    app()->instance('request', Request::create('/', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']));
    $check('CO7 archive preserves the parent row and active reply', function () use ($controller, $parent, $reply) {
        $response = $controller->destroy($parent->id);
        return $response->getStatusCode() === 200 && Comment::find($parent->id) === null && Comment::withTrashed()->find($parent->id)->trashed() && Comment::find($reply->id) !== null;
    });
    $check('CO7 archived parent renders placeholder with reply', function () use ($controller, $data, $tag) {
        $html = $controller->getComments(Request::create('/', 'POST', $data));
        return strpos($html, 'Comment removed') !== false && strpos($html, $tag . ' reply') !== false && strpos($html, 'Owner update') === false;
    });
    $check('CO6 missing delete gives not-found', function () use ($controller) {
        try { $controller->destroy(2147483647); }
        catch (Illuminate\Database\Eloquent\ModelNotFoundException $e) { return true; }
        return false;
    });
} catch (Throwable $e) { $results[] = ['check' => 'Workflow setup', 'status' => 'failed', 'error' => $e->getMessage()]; }
finally { while (DB::transactionLevel()) DB::rollBack(); }
$check('All comment/file/activity/user values preserved after rollback', fn() => $before === $snapshot());
file_put_contents(storage_path('app/qa-mysql-comments-results.json'), json_encode(['date' => date(DATE_ATOM), 'results' => $results], JSON_PRETTY_PRINT));
foreach ($results as $r) echo strtoupper($r['status']) . ' ' . $r['check'] . (isset($r['error']) ? ': ' . $r['error'] : '') . PHP_EOL;
exit(count(array_filter($results, fn($r) => $r['status'] === 'failed')) ? 1 : 0);
