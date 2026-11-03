@if(!empty($mainParents))
    <div class='chat'>
        @foreach($mainParents as $comment)
            <div class='item'>
                <div class='chat-details'>
                    <span class='chat-author'>
                        by <b>{{ $comment->author->name ?? 'Deleted user' }}</b>
                    </span>
                    <span class='chat-date pull-right'>
                        <i class='fa fa-clock-o' style='margin-right: 5px'></i>
                        <i>{{$comment->created_at}}</i>
                    </span>
                </div>
                <div class='chat-content'>
                    @if($comment->trashed())<em>Comment removed. Replies are preserved.</em>@else{!! $comment->content !!}@endif
                </div>
                <div class='chat-attachments'>
                    <table class='table'>
                        <tbody>
                        @foreach($comment->trashed() ? [] : $comment->files as $attach)
                            <tr class='del-container'>
                                <td class='td_link_attach'>
                                    <div class='td_link_attach__name'>
                                        <a class='name_attach' href='{{ $attach->url }}' target='_blank'>
                                            <span class='glyphicon glyphicon-paperclip'></span>
                                            {{$attach->attach_file_name}}
                                        </a>
                                    </div>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class='announcement-actions'>
                    @if(!$comment->trashed() && app(\App\Services\CommentAccess::class)->allowed('comment.create'))
                    <a href='#content' data-comment-id='{{ $comment->id }}' data-parent-name='{{ $comment->author->name ?? "Deleted user" }}' class='link-black text-sm reply_comment' style='margin-top: 10px'><i class='fa fa-reply margin-r-5'></i> {{ trans('main.Reply') }}</a>
                    @endif
                </div>
                @if(count($comment->childs))
                    @include('comments.childs',['childs' => $comment->childs, 'nesting' => 1])
                @endif
            </div>
        @endforeach
    </div>
@endif
