<?php
namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Comment;
use App\Helper\FileTrait;
use App\Services\CommentAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    use FileTrait;

    public function __construct()
    {
        $this->middleware(['auth', 'permissions.required', 'preventBackHistory']);
    }

    public function index(Request $request)
    {
        $access = app(CommentAccess::class);
        abort_unless($access->allowed('comment.index'), 403);
        $types = array_filter(array_keys(Comment::REFERENCE_MODELS), fn($type) => $access->modulePermission($type) && $access->allowed($access->modulePermission($type)));
        $search = trim((string)$request->input('search'));
        $commentsData = Comment::query()->leftJoin('users', 'users.id', '=', 'comments.author_id')
            ->select('comments.*', 'users.name as sender')
            ->where(function ($query) use ($types) {
                $query->whereRaw('1 = 0');
                foreach ($types as $type) {
                    $class = Comment::REFERENCE_MODELS[$type]; $model = new $class;
                    $reference = $model->newQuery()->selectRaw('1')->whereColumn($model->qualifyColumn('id'), 'comments.reference_id')->toBase();
                    $query->orWhere(function ($q) use ($type, $reference) {
                        $q->where('comments.reference_type', $type);
                        $q->getQuery()->addWhereExistsQuery($reference);
                    });
                }
            })
            ->where(function ($q) { $q->whereNull('parent')->orWhere('parent', 0); })
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) { $q->where('comments.content', 'like', "%$search%")->orWhere('users.name', 'like', "%$search%"); });
            })->orderByDesc('comments.id')->paginate(20)->withQueryString();
        return view('comments.index', ['title' => 'Comments', 'commentsData' => $commentsData]);
    }

    public function deleteMsg($id, Request $request)
    {
        app(CommentAccess::class)->comment(Comment::findOrFail($id), 'comment.destroy', true);
        return $request->ajax() ? Ajaxis::BtDeleting('Archive comment', 'Archive this comment? Replies will be preserved.', route('comment.destroy', ['id' => $id])) : redirect()->route('comment.show', $id);
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $comment = Comment::query()->lockForUpdate()->findOrFail($id);
            app(CommentAccess::class)->comment($comment, 'comment.destroy', true);
            $comment->delete();
        });
        return request()->expectsJson() ? response()->json(['route' => route('comment.index'), 'message' => 'Comment archived. Replies are preserved.']) : redirect()->route('comment.index');
    }

    private function validateBody(Request $request, $existing = null)
    {
        $data = $request->validate([
            'content' => 'nullable|string|max:50000',
            'attach' => 'nullable|array|max:10',
            'attach.*' => 'file|max:10240|mimes:jpeg,jpg,png,gif,pdf,doc,docx,xls,xlsx',
            'attach_bus' => 'prohibited',
        ]);
        $content = \App\Helper\CommentHtml::clean($data['content'] ?? '');
        $hasText = trim(html_entity_decode(strip_tags($content), ENT_QUOTES, 'UTF-8')) !== '';
        if (!$hasText && !$request->hasFile('attach') && !($existing && $existing->files()->exists())) {
            throw \Illuminate\Validation\ValidationException::withMessages(['content' => 'Enter a comment or attach a supported file.']);
        }
        return $content;
    }

    public function store(Request $request)
    {
        $access = app(CommentAccess::class);
        abort_unless($access->allowed('comment.create') && $access->allowed('comment.show'), 403);
        $data = $request->validate(['reference_type' => ['required', 'integer', \Illuminate\Validation\Rule::in(array_keys(Comment::REFERENCE_MODELS))], 'reference_id' => 'required|integer|min:1', 'parent' => 'nullable|integer|min:0']);
        $access->reference($data['reference_type'], $data['reference_id']);
        if (!empty($data['parent'])) {
            $parent = Comment::findOrFail($data['parent']);
            abort_unless((int)$parent->reference_type === (int)$data['reference_type'] && (int)$parent->reference_id === (int)$data['reference_id'], 422);
        }
        $content = $this->validateBody($request);
        DB::transaction(function () use ($data, $content, $request) {
            $comment = new Comment(['content' => $content, 'reference_type' => $data['reference_type'], 'reference_id' => $data['reference_id'], 'parent' => ($data['parent'] ?? null) ?: null]);
            $comment->author_id = auth()->id(); $comment->save(); $this->addFile($request, $comment);
        });
        return response()->json(['content' => $this->getComments($request), 'comments' => true]);
    }

    public function getComments(Request $request)
    {
        abort_unless(app(CommentAccess::class)->allowed('comment.show'), 403);
        $data = $request->validate(['reference_type' => ['required', 'integer', \Illuminate\Validation\Rule::in(array_keys(Comment::REFERENCE_MODELS))], 'reference_id' => 'required|integer|min:1']);
        app(CommentAccess::class)->reference($data['reference_type'], $data['reference_id']);
        $comments = Comment::withTrashed()->with(['author', 'files'])->where('reference_type', $data['reference_type'])->where('reference_id', $data['reference_id'])->orderBy('id')->get();
        $groups = $comments->groupBy('parent');
        foreach ($comments as $comment) $comment->setRelation('childs', $groups->get($comment->id, collect()));
        $focus = $request->input('id_comment');
        if ($focus && $focus !== 'undefined') {
            abort_unless(is_scalar($focus) && ctype_digit((string)$focus), 422);
            $selected = $comments->firstWhere('id', (int)$focus); abort_unless($selected, 404);
            $roots = collect([$selected]);
        } else {
            $ids = $comments->pluck('id');
            $roots = $comments->filter(fn($comment) => !$comment->parent || !$ids->contains($comment->parent));
        }
        return view('comments.show_tree_view_all', ['mainParents' => $roots])->render();
    }

    public function show($comment, Request $request)
    {
        $comment = Comment::findOrFail($comment);
        app(CommentAccess::class)->comment($comment, 'comment.show');
        return view('comments.show', compact('comment'));
    }

    public function edit($comment, Request $request)
    {
        $comment = Comment::findOrFail($comment);
        app(CommentAccess::class)->comment($comment, 'comment.edit', true);
        return $request->ajax() ? route('comment.edit', $comment->id) : view('comments.edit', ['title' => 'Edit Comment', 'comment' => $comment, 'files' => $this->parseAttach($comment)]);
    }

    public function update($id, Request $request)
    {
        DB::transaction(function () use ($id, $request) {
            $comment = Comment::query()->lockForUpdate()->findOrFail($id);
            app(CommentAccess::class)->comment($comment, 'comment.edit', true);
            $comment->content = $this->validateBody($request, $comment); $comment->save(); $this->addFile($request, $comment);
        });
        return $request->expectsJson() ? response()->json(['route' => route('comment.show', $id)]) : redirect()->route('comment.show', $id);
    }
}
