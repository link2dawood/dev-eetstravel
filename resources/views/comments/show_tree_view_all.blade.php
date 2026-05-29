{{-- Top-level comment tree fragment. Loaded into #show_comments by
     comment.js — see comments/show.blade.php. Each item recursively
     pulls in comments/childs.blade.php. Selectors preserved:
     .chat, .item, .reply_comment[data-comment-id][data-parent-name]. --}}
@if(!empty($mainParents))
    <div class="chat space-y-3">
        @foreach($mainParents as $comment)
            <div class="item rounded border border-slate-200 bg-white">
                <div class="chat-details flex items-center justify-between gap-2 px-4 py-2 border-b border-slate-100 text-xs text-slate-500">
                    <span class="chat-author">by <b class="text-slate-700">{{ \App\User::find($comment->author)->first()->name }}</b></span>
                    <span class="chat-date inline-flex items-center gap-1">
                        <x-ui.icon name="clock" size="xs" class="text-slate-400" />
                        <i>{{ $comment->created_at }}</i>
                    </span>
                </div>

                <div class="chat-content px-4 py-3 text-sm text-slate-800 prose prose-sm max-w-none">
                    {!! $comment->content !!}
                </div>

                @if(count($comment->files))
                    <div class="chat-attachments border-t border-slate-100 px-4 py-2">
                        <table class="table w-full text-xs">
                            <tbody>
                                @foreach($comment->files as $attach)
                                    <tr class="del-container">
                                        <td class="td_link_attach py-1">
                                            <div class="td_link_attach__name inline-flex items-center gap-1.5">
                                                <a class="name_attach inline-flex items-center gap-1 text-primary-600 hover:text-primary-700" href="{{ 'public' . $attach->attach->url() }}" target="_blank">
                                                    <span class="glyphicon glyphicon-paperclip"></span>
                                                    {{ $attach->attach_file_name }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="announcement-actions px-4 py-2 border-t border-slate-100 bg-slate-50">
                    <a href="#content"
                       data-comment-id="{{ $comment->id }}"
                       data-parent-name="{{ \App\User::find($comment->author)->first()->name }}"
                       class="link-black text-sm reply_comment inline-flex items-center gap-1 text-slate-600 hover:text-primary-600">
                        <x-ui.icon name="corner-up-left" size="xs" /> {!! trans('main.Reply') !!}
                    </a>
                </div>

                @if(count($comment->childs))
                    <div class="px-3 pb-3">
                        @include('comments.childs', ['childs' => $comment->childs, 'nesting' => 1])
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif
