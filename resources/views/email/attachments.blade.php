{{-- Attachments list fragment — rendered into an email view. --}}
@foreach($mail->getAttachments() as $attachment)
    <li class="flex items-center gap-2 px-3 py-2 border-b border-slate-100 last:border-0">
        <span class="mailbox-attachment-icon flex h-7 w-7 items-center justify-center rounded bg-slate-100 text-slate-500">
            <x-ui.icon name="file" size="sm" />
        </span>
        <div class="mailbox-attachment-info flex-1 min-w-0">
            <a class="mailbox-attachment-name inline-flex items-center gap-1 text-sm text-primary-600 hover:text-primary-700"
               href="{{ route('email.attachment', [
                    'id' => $mail->getNumber(),
                    'attachmentName' => preg_replace('/[^a-zA-Z]/', '', $attachment->getFilename()),
                    'folderName' => $currentFolder
                ]) }}">
                <x-ui.icon name="paperclip" size="xs" /> {{ $attachment->getFilename() }}
            </a>
        </div>
    </li>
@endforeach
