<?php

namespace App\Http\Controllers;

use Amranidev\Ajaxis\Ajaxis;
use App\Comment;
use App\Helper\FileTrait;
use App\Helper\LaravelFlashSessionHelper;
use App\Helper\PermissionHelper;
use Illuminate\Http\Request;
use URL;
use View;
use Yajra\Datatables\Datatables;

class CommentController extends Controller
{
    use FileTrait;

    public function __construct()
    {
        $this->middleware('preventBackHistory');
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $title = 'Index - Comments';

        // Get all comments data (same as the AJAX data method)
        $commentsData = Comment::distinct()
            ->leftJoin('users', 'users.id', '=', 'comments.author_id')
            ->select('comments.id', 'comments.content', 'comments.created_at',
                'users.name as sender'
            )
            ->where('parent', null)
            ->get();

        // Add action buttons to each comment
        $commentsData->each(function ($comment) {
            $comment->action_buttons = $this->getActionButtons($comment->id, $comment);
        });

        return view('comments.index', compact('title', 'commentsData'));
    }

    public function getActionButtons($id, $comment)
    {
        $url = array('show'       => route('comment.show', ['comment' => $id]),
            'edit'       => route('comment.edit', ['comment' => $id]),
            'delete_msg' => "/comment/{$id}/delete_msg");

        return DatatablesHelperController::getActionButton($url, false, $comment);
    }

    public function data(Request $request)
    {
        return Datatables::of(
            Comment::distinct()
                ->leftJoin('users', 'users.id', '=', 'comments.author_id')
                ->select('comments.id', 'comments.content', 'comments.created_at',
                    'users.name as sender'
                )
                ->where('parent', null)
        )
            ->addColumn('action', function ($comment) {
                return $this->getActionButtons($comment->id, $comment);
            })
            //            ->addColumn('files', function ($announcement) {
            //                return $this->renderAttachmentsList($announcement);
            //            })
            ->rawColumns(['action'/*, 'files'*/])
            ->make(true);
    }

    public function deleteMsg($id, Request $request)
    {
//        $msg = Ajaxis::BtDeleting('Warning!!', 'Would you like to remove This?', '/comment/' . $id . '/delete');
        $msg = Ajaxis::BtDeleting(trans('main.Warning').'!!',trans('main.WouldyouliketoremoveThis').'?', '/comment/' . $id . '/delete');
        
        if ($request->ajax()) {
            return $msg;
        }
    }

    public function destroy($id)
    {
        $comment = Comment::where('id', $id)->first();

//        \DB::beginTransaction();
        Comment::where('id', $id)->delete();
        $comments = Comment::where('parent', $id)->get();
        if(!empty($comments[0])){
            $this->deleteComments($comments);
        }

        LaravelFlashSessionHelper::setFlashMessage("Comment #$id deleted", 'success');

//        \DB::commit();

        return URL::to('comment');
    }

    public function deleteComments($comments){
        if(!empty($comments[0])){
            $underCommentId = array();
            foreach ($comments as $comment){
                $underCommentId[] = $comment->id;
            }
            $underComments = Comment::whereIn('parent', $underCommentId)->get();
            Comment::whereIn('id', $underCommentId)->delete();
            if(!empty($underComments[0])){
                $this->deleteComments($underComments);
            }
        }
    }

    public function store(Request $request)
    {
        if($request->get('content') == null && !$request->hasFile('attach') && !$request->hasFile('attach_bus')){
            $this->validate($request, [
                'content' => 'required',
                'attach' => 'required',
                'attach_bus' => 'required'
            ]);
        }
        $request['author_id'] = \Auth::id();
        $comment = Comment::create($request->except(['_token', 'attach', 'attach_bus', 'id_comment']));

        $this->addFile($request, $comment);


        $data = ["content" => $this->getComments($request), "comments" => true];

        return response()->json($data);

    }

    public function getComments(Request $request){

        $comments = '';
        if($request->get('id_comment') == 'undefined' || empty($request->get('id_comment'))){
            $comments = Comment::where('reference_id', $request->get('reference_id'))
                ->where('reference_type', $request->get('reference_type'))
                ->get();
        }else{
            $comments = Comment::where('id', $request->get('id_comment'))
                ->get();
        }

        return $this->generateView($comments);
    }

    public function generateView($comments){
        $mainParents = $this->getMainParentsAll($comments);
        $view = View::make(
            'comments.show_tree_view_all',
            [
                'mainParents'   => $mainParents
            ]
        );
        $contents = $view->render();

        return $contents;
    }

    public function show($comment, Request $request)
    {
        $comment = Comment::findOrFail($comment);
        if (!$comment->service()) return back()->with('not_found', trans('main.Sorrythisdatawasdeleted'));
        return view('comments.show', compact('comment'));
    }

    public function getMainParent(Comment $comment)
    {
        if (!$comment->parent) {
            return $comment;
        } else {
            $parentComment = Comment::findOrFail($comment->parent);
            return $this->getMainParent($parentComment);
        }
    }

    public function getMainParentsAll($comments){
        $arrComments = array();
        foreach ($comments as $comment){
            if (!$comment->parent) {
                $arrComments[] = $comment;
            }
        }
        return $arrComments;
    }

    public function edit($comment, Request $request)
    {
        $commentObj = Comment::query()->findOrFail($comment);
        if (!$commentObj->service()) return back()->with('not_found', trans('main.Sorrythisdatawasdeleted'));

        $title = 'Edit - comment';
        if ($request->ajax()) {
            return URL::to('comment/' . $comment . '/edit');
        }

        $comment = Comment::findOrFail($comment);
        $files = $this->parseAttach($comment);

        return view('comments.edit', compact('title', 'comment', 'files'));
    }

    public function update($id, Request $request)
    {
        $comment = Comment::findOrFail($id);
        $comment->update($request->except('attach','attach_bus'));
        $this->addFile($request, $comment);

        LaravelFlashSessionHelper::setFlashMessage("Comment $comment->content edited", 'success');
        // return redirect(route('announcements.show', ['id' => $id]));
        return response()->json($data = ['route' => route('comment.show', ['id' => $id])]);
    }
}
