<div class="direct-chat-msg right">
    <div class="direct-chat-info clearfix">
        <span class="direct-chat-name pull-right">{{$message->author->name}}</span>
        <span class="direct-chat-timestamp pull-left">{{$message->created_at}}</span>
    </div>
    <!-- /.direct-chat-info -->
    <img class="direct-chat-img" src="{{$message->author->avatar_file_name == null ? asset('img/avatar.png') : $message->author->avatar->url('logo')}}" alt="Logo" 
        style="text-align: center;"><!-- /.direct-chat-img -->
    <div class="direct-chat-text">
        {{nl2br($message->message)}}
    </div>
    <!-- /.direct-chat-text -->
</div>