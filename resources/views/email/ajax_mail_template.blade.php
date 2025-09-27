@if($imapConnected)
    <div class="mailbox-controls with-border text-center">
        {{--@if(\App\Helper\AdminHelper::emailCheck($mail))--}}
        {{--@php--}}
        {{--$addresses = [];--}}
        {{--$toArray = $mail->getTo();--}}
        {{--array_walk($toArray, function($to) use (&$addresses){--}}
        {{--$addresses[] = $to->getAddress();--}}
        {{--})--}}

        {{--@endphp--}}
        <button
                type="button"
                class="btn btn-default reply"
                data-reply-message="{{$mail->message_id}}"
                data-reply-folder="{{$currentFolder}}"
                data-to="
                    @if($currentFolder == 'INBOX.Sent')
                {{$mail->to}}
                @else
                {{$mail->from}}
                @endif
                        "
                data-link="{{route('email.getComposeForm', ['id' => $mail->message_id, 'folder' => $currentFolder], false)}}">
            <i class="fa fa-reply"></i> Reply
        </button>
        <a data-toggle="modal" data-target="#myModal" class="btn-sm delete" data-link="{{route('email.deleteMsg', ['id' => $mail->message_id, 'folder' => $currentFolder], false)}}"><button type="button" class="btn btn-default"><i class="fa fa-trash-o"></i> Delete</button></a>
        {{--@endif--}}
    </div>
    <!-- /.box-header -->
    <div class="box-body no-padding">
        <div class="mailbox-read-info">
            <h3>
                @if(\App\Helper\AdminHelper::emailCheck($mail))
                    {{$mail->subject}}</h3>
            @endif
            <h5>From:
                @if(\App\Helper\AdminHelper::emailCheck($mail))
                    {{$mail->from}}
                @endif
                <span class="mailbox-read-time pull-right">
                    @if(\App\Helper\AdminHelper::emailCheck($mail))
                        {{$mail->date}}
                    @endif
                </span></h5>
        </div>
        <!-- /.mailbox-read-info -->
        <div class="mailbox-read-message">
            @if($mail->body_html)
                {!! $mail->body_html !!}
            @else
                {!! $mail->body_text  !!}
            @endif
        </div>
        <!-- /.mailbox-read-message -->
    </div>
    <!-- /.box-body -->

    <div class="attach-block" style="padding: 20px; margin-top: 30px">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3>Attachments</h3>
            </div>
            <div class="box box-body" style="border-top: none">
                <ul class="mailbox-attachments clearfix">

                </ul>
            </div>

            <div class="overlay" id="overlay_attach">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $.ajax({
                method: "GET",
                url: "/email/attachmentList/{{$currentFolder}}/{{$mail->message_id}}",
                data: {
                }
            }).done(function(data){
                $('.mailbox-attachments').html(data);
                $('#overlay_attach').remove();
            });
            $('.mailbox-read-message').find('style').remove();
        });

    </script>
    <!-- /.box-footer -->
@endif