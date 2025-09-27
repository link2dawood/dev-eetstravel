@foreach($mail->getAttachments() as $attachment)
<li>
    <span class="mailbox-attachment-icon"><i class="fa fa-file-o"></i></span>
    <div class="mailbox-attachment-info">
        <a href="{{
                        route('email.attachment',
                         [
                            'id' => $mail->getNumber(),
                            'attachmentName' => preg_replace("/[^a-zA-Z]/",
        "",
        $attachment->getFilename()
        ),
        'folderName'    => $currentFolder
        ]
        )
        }}"
        class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> {{$attachment->getFilename()}}
        </a>
        </div>
    </li>
@endforeach