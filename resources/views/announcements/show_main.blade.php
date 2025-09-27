<div class="chat">
                    <div class="item">
                        <div class="chat-details">
                            <span class="chat-author">
                                by <b>{{\App\User::find($announcement->author)->name}}</b>
                            </span>
                            <span class="chat-date">
                               <i>{{$announcement->created_at}}</i>
                            </span>
                        </div>
                        <div class="chat-content">
                            {!! $announcement->content !!}

                        </div>
                        <div class="chat-attachments">
                            <table class="table">
                                <tbody>


                            @foreach($announcement->files as $attach)
                                <tr class="del-container">
                                    <td class="td_link_attach">
                                        <div class="td_link_attach__name">
                                            <a class="name_attach" href="{{$attach->attach->url()}}" target="_blank">
                                                <span class="glyphicon glyphicon-paperclip"></span>
                                                {{$attach->attach_file_name}}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                                </tbody>
                            </table>
                        </div>
                            <div class="announcement-actions">
                                {{-- <a href="{{route('announcement_reply', ['id' => $mainParent->id])}}" class="link-black text-sm reply_comment" style="margin-top: 10px"><i class="fa fa-reply margin-r-5"></i> Reply</a> --}}

                            </div>

        @if(count($announcement->childs))
            @include('announcements.childs',['childs' => $announcement->childs, 'nesting' => 1])
        @endif
    </div>
</div>