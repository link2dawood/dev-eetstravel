{{-- Recursive nested-comments fragment. Loaded into #show_comments by
     comment.js. Keep selectors intact: .reply_comment[data-comment-id]
     and [data-parent-name] are wired up in comment.js to populate the
     reply form's #parent_comment + #author_name fields. --}}
<div class="pl-6 space-y-3 border-l border-slate-200">
    @foreach($childs as $child)
        <div class="item nesting-{{ $nesting }}">
            <div class="rounded border border-slate-200 bg-white">
                <div class="chat-title flex items-center justify-between gap-2 px-3 py-2 border-b border-slate-100 {{ $child->id == $comment->id ? 'active-announcement bg-primary-50' : '' }}">
                    <h4 class="text-sm font-medium text-slate-800 truncate">{{ $child->title }}</h4>
                </div>

                <div class="chat-details flex items-center justify-between gap-2 px-3 py-1.5 text-xs text-slate-500">
                    <span class="chat-author">by <b class="text-slate-700">{{ \App\User::find($child->author)->first()->name }}</b></span>
                    <span class="chat-date inline-flex items-center gap-1">
                        <x-ui.icon name="clock" size="xs" class="text-slate-400" />
                        <i>{{ $child->created_at }}</i>
                    </span>
                </div>

                <div class="chat-content px-3 py-2 text-sm text-slate-800 prose prose-sm max-w-none">
                    {!! $child->content !!}
                </div>

                @if(count($child->files))
                    <div class="chat-attachments border-t border-slate-100 px-3 py-2">
                        <table class="table w-full text-xs">
                            <tbody>
                                @foreach($child->files as $attach)
                                    <tr class="del-container">
                                        <td class="td_link_attach py-1">
                                            <div class="td_link_attach__name inline-flex items-center gap-1.5">
                                                <a class="name_attach inline-flex items-center gap-1 text-primary-600 hover:text-primary-700" href="{{ 'public/' . $attach->attach->url() }}" target="_blank">
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

                <div class="announcement-actions px-3 py-2 border-t border-slate-100 bg-slate-50">
                    <a href="#content"
                       data-comment-id="{{ $child->id }}"
                       data-parent-name="{{ \App\User::find($child->author)->first()->name }}"
                       class="link-black text-sm reply_comment inline-flex items-center gap-1 text-slate-600 hover:text-primary-600">
                        <x-ui.icon name="corner-up-left" size="xs" /> Reply
                    </a>
                </div>
            </div>

            @if(count($child->childs))
                @include('comments.childs', ['childs' => $child->childs, 'nesting' => $nesting + 1])
            @endif
        </div>
    @endforeach
</div>
