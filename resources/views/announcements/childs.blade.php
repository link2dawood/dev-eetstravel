<div style="margin-left: 25px; ">
    @foreach($childs as $child)
        <div class="item nesting-{{$nesting}}">
            <div class="chat-title
                @if ($child->id == $announcement->id)
                    active-announcement
                @endif
                    ">
                <h4>
                    {!! $child->title !!}
                </h4>
            </div>
            <div class="chat-details">
                            <span class="chat-author">
                                by <b>{{ optional(\App\User::find($child->author))->name ?? 'Unknown' }}</b>
                            </span>
                <span class="chat-date">
                               <i>{{$child->created_at}}</i>
                            </span>
            </div>
            <div class="chat-content">
                {!! $child->content !!}
            </div>
            <div class="chat-attachments">
                <table class="table">
                    <tbody>
                    @php
                        $childAttachments = collect();
                        try {
                            if (\Illuminate\Support\Facades\Schema::hasTable('media')) {
                                $childAttachments = $child->getMedia('announcement_files');
                            }
                        } catch (\Exception $e) {
                            // Media table doesn't exist, skip attachments
                        }
                    @endphp
                    @foreach($childAttachments as $attach)
                        <tr class="del-container">
                            <td class="td_link_attach">
                                <div class="td_link_attach__name">
                                    <a class="name_attach" href="{{ $attach->getUrl() }}" target="_blank">
                                        <span class="glyphicon glyphicon-paperclip"></span>
                                        {{ $attach->file_name }}
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
                <a href="#content" data-comment-id="{{ $child->id }}" data-parent-name="{{ optional(\App\User::find($child->author))->name ?? 'Unknown' }}" class="link-black text-sm reply_comment" style="margin-top: 10px"><i class="fa fa-reply margin-r-5"></i> Reply</a>
            </div>
            @if(count($child->childs))
                @include('announcements.childs',['childs' => $child->childs, 'nesting' => $nesting+1])
            @endif
        </div>
    @endforeach
</div>