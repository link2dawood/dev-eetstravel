<div class="mailbox-controls with-border text-center pull-right">
    <button type="button" class="btn btn-default" @click="backToList">
        <i class="fa fa-reply"></i> Back
    </button>
    <button type="button" class="btn btn-default" @click="replyEmail(email)">
        <i class="fa fa-reply"></i> Reply
    </button>
    <a data-toggle="modal" class="btn-sm" @click="deleteMail(email, true)">
        <button type="button" class="btn btn-default"><i class="fa fa-trash-o"></i> Delete</button>
    </a>

</div>
<!-- /.box-header -->
<div class="box-body no-padding">
    <div class="mailbox-read-info">
        <h3>
            @{{ email.header.subject}}</h3>
        <h5>From:
            @{{ email.header.from }}
            <span class="mailbox-read-time pull-right">
                                             @{{moment(email.header.date).format('YYYY-MM-DD H:m:s')}}
                                    </span></h5>
    </div>
    <!-- /.mailbox-read-info -->
    <div class="mailbox-read-message">
        {{-- Shown in a sandboxed frame: an email's HTML can't run code in the app or restyle it
             (it used to be inserted with v-html, where e.g. <img onerror> runs). --}}
        <iframe class="w-100 border-0" style="min-height: 300px;" title="Email message"
                sandbox="allow-same-origin allow-popups allow-popups-to-escape-sandbox"
                referrerpolicy="no-referrer"
                :srcdoc="'<base target=&quot;_blank&quot;>' + ((email.message.html && email.message.html.body) ? email.message.html.body : '<pre style=&quot;white-space:pre-wrap;font-family:inherit;margin:0&quot;>' + String(email.message.text || '').replace(/&/g, '&amp;').replace(/</g, '&lt;') + '</pre>')"
                @load="$event.target.style.height = (($event.target.contentDocument && $event.target.contentDocument.documentElement.scrollHeight) || 300) + 20 + 'px'"></iframe>

    </div>
    <!-- /.mailbox-read-message -->
</div>
<!-- /.box-body -->

<div class="attach-block"  v-if="email.attachments.length>0">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3>Attachments</h3>
        </div>
        <div class="box box-body" style="border-top: none">
            <ul class="mailbox-attachments clearfix">
                <li v-for="attach in email.attachments">
                    <span class="mailbox-attachment-icon"><i class="fa fa-file-o"></i></span>
                    <div class="mailbox-attachment-info">
                        <a @click="downloadAttachment(attach)" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i>
                            @{{attach.name}}
                        </a>
                    </div>
                </li>
            </ul>
        </div>

        {{--<div class="overlay" id="overlay_attach">--}}
            {{--<i class="fa fa-refresh fa-spin"></i>--}}
        {{--</div>--}}
    </div>
</div>