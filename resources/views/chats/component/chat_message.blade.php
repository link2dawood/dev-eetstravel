<!-- Message. Default to the left -->
<div class="direct-chat-msg">
    <div class="direct-chat-info clearfix">
        {{--{{dd($message->author)}}--}}
        <span class="direct-chat-name pull-left">{{$message->author->name}}</span>
        <span class="direct-chat-timestamp pull-right">{{$message->created_at}}</span>
    </div>
    <!-- /.direct-chat-info -->
    <img class="direct-chat-img" src="{{$message->author->avatar_file_name == null ? asset('img/avatar.png') : $message->author->avatar->url('logo')}}"><!-- /.direct-chat-img -->
    <div class="direct-chat-text">
        {!! nl2br($message->message) !!}
    </div>
    <!-- /.direct-chat-text -->
</div>

