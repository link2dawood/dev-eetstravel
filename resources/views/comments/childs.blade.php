<div style="margin-left: 25px; ">
    @foreach($childs as $child)
        <div class="item nesting-{{$nesting}}">
            <div class="chat-title
                @if ($child->id == $comment->id)
                    active-announcement
                @endif
                    ">
                <h4>
                    {{ $child->title }}
                </h4>
            </div>
            <div class="chat-details">
                <span class="chat-author">by
                    <b>{{ $child->author->name ?? 'Deleted user' }}</b>
                </span>
                <span class="chat-date pull-right">
                    <i class="fa fa-clock-o" style="margin-right: 5px"></i>
                    <i>{{$child->created_at}}</i>
                </span>
            </div>
            <div class="chat-content">
                @if($child->trashed())<em>Comment removed. Replies are preserved.</em>@else{!! $child->content !!}@endif
            </div>
            <div class="chat-attachments">
                <table class="table">
                    <tbody>


                    @foreach($child->trashed() ? [] : $child->files as $attach)
                        <tr class="del-container">
                            <td class="td_link_attach">
                                <div class="td_link_attach__name">
                                    <a class="name_attach" href="{{ $attach->url }}" target="_blank">
                                        <span class="glyphicon glyphicon-paperclip"></span>
                                        {{$attach->attach_file_name}}
                                    </a>
                                </div>
                            </td>

                            {{-- {{csrf_field()}} --}}
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="announcement-actions">
                @if(!$child->trashed() && app(\App\Services\CommentAccess::class)->allowed('comment.create'))
                <a href="#content" data-comment-id="{{ $child->id }}" data-parent-name="{{ $child->author->name ?? 'Deleted user' }}" class="link-black text-sm reply_comment" style="margin-top: 10px"><i class="fa fa-reply margin-r-5"></i> Reply</a>
                @endif
            </div>
            @if($nesting < 50 && count($child->childs))
                @include('comments.childs',['childs' => $child->childs, 'nesting' => $nesting+1])
            @endif
        </div>
    @endforeach
</div>
